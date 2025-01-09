<?php

/**
 * @description    Mailing class. Läuft im Hintergrund und sendet E-Mails an die Benutzer.
 */

namespace App\Jobs;

use App\Models\User\PasswordReset;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;


class JobEmail implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @description    Die Benutzer-ID, der eir eine E-Mail senden möchten.
     */
    private $user_id;
    /**
     * @description    Typ des Ereignisses.
     */
    private $event;
    /**
     * @description    Zusätzliche Nutzdaten werden entweder in einem Array oder einem Objekt bereitgestellt. Darüber hinaus könnte es sein, dass die Nutzlast eine einfache Art von Daten ist (int|string|etc)
     */
    private $payload;

    /**
     * @description    Class' Konstruktion.
     * @param $user_id, user's id
     * @param $event, email's ereignis
     * @param $payload, zusätzliche Daten
     */
    public function __construct($user_id, $event, $payload) {
        $this->user_id = $user_id;
        $this->event = $event;
        $this->payload = $payload;
    }

    /**
     * @description    Execution, wird aufgerufen, wenn die execute Funktion des 'Mailing job' aufgerufen wird oder versendet wird
     */
    public function handle() {

        // Wir bestimmen die obligatorischen Werte. Wenn sie leer sind, vermeiden wir die Ausführung und kehren zurück.
        if(empty($this->payload) === true || empty($this->user_id) === true || empty($this->event) === true) return;

        if($this->event === "reminder_email") {
            // Erinnerungs-E-Mail an den Benutzer

            // Findet den Benutzer, basiert auf der user's id
            $user = User::find($this->user_id);
            // Bestätigung, ob der Benutzer nicht leer ist
            if(empty($user) === false) {
                // Holt  sich die HTML-Vorlage der E-Mail
                $email = Storage::disk('public')->get('email/reminder.html');
                // Prüft, ob der Inhalt der Datei|Dateien leer ist|sind
                if(empty($email) === false) {
                    // Es gibt drei Strings, die ersetzt werden können
                    // Logo ersetzen
                    $email = str_replace("{logo}",  URL::to("/content/brand/logo_wide.png"), $email);
                    // Den Vornamen ersetzen
                    $email = str_replace("{first_name}", $user->first_name, $email);
                    // Den Erinnerungstext ersetzen
                    $email = str_replace("{reminder_text}", $this->payload, $email);
                    // Sendet die E-Mail und übergibt die Instanz des Benutzers und die Vorlage der E-Mail
                    Mail::send([], [], function ($message) use ($user, $email) {
                        $message->to($user->email)
                        ->subject("alwaysDa - Erinnerung")
                            ->from(env("MAIL_FROM", env("MAIL_FROM", "alwaysda@hammado.dev")))
                            ->setBody($email, 'text/html');
                    });

                    // Den Benutzer über die Erinnerung benachrichtigen (Browser|Android)
                    $this->notifyFirebase($user, $this->payload);
                }
            }
        } else if($this->event === "request_token") {
            // Der Benutzer hat ein Token angefordert

            // Findet den Benutzer, basiert auf der user's id
            $user = User::find($this->user_id);
            //payload ist eine integer und stellt die ID der password_reset Instanz dar
            $password_reset = PasswordReset::find($this->payload);


            // Bestätigung, ob der Benutzer und die password_reset-Instanz nicht leer sind
            if(empty($password_reset) === false && empty($user) === false) {
                // Holt  sich die HTML-Vorlage der E-Mail
                $email = Storage::disk('public')->get('email/token.html');
                // Prüft, ob der Inhalt der Datei|Dateien leer ist|sind
                if(empty($email) === false) {
                    // Token von der password_reset-Instanz abrufen.
                    $token = $password_reset->token;
                    // Es gibt drei Strings, die ersetzt werden können
                    // Logo ersetzen
                    $email = str_replace("{logo}",  URL::to("/content/brand/logo_wide.png"), $email);
                    // Den Vornamen ersetzen
                    $email = str_replace("{first_name}", $user->first_name, $email);
                    // Token ersetzen
                    $email = str_replace("{token}", $token, $email);
                    // Sendet die E-Mail und übergibt die Instanz des Benutzers und die Vorlage der E-Mail
                    Mail::send([], [], function ($message) use ($user, $email) {
                        $message->to($user->email)
                            ->subject("alwaysDa - Token")
                            ->from(env("MAIL_FROM", env("MAIL_FROM", "alwaysda@hammado.dev")))
                            ->setBody($email, 'text/html');
                    });
                }
            }
        }
    }

    /**
     * @description    Execution, wird aufgerufen, wenn die execute Funktion des 'Mailing job' aufgerufen wird oder versendet wird
     * @param $user, die Instanz des Benutzers
     * @param $body, Inhalt der Benachrichtigung
     * @return void
     */
    private function notifyFirebase($user, $body) {
        // Überprüfen Sie, ob der Benutzer nicht leer ist und über ein gültiges firebase_token verfügt und ob der Text ebenfalls nicht leer ist
        if(empty($user) === false && empty($user->firebase_token) === false && empty($body) === false) {
            // Sendet eine Anfrage an die offizielle Firebase-API (aus ihrer Dokumentation entnommen)
            $url = 'https://fcm.googleapis.com/fcm/send';

            // Übergibt das firebase_token zusammen mit dem Titel und dem Text der Benachrichtigung
            $title = "Erinnerung";
            $data = array(
                "registration_ids" => [$user->firebase_token],
                "notification" => array(
                    "title" => $title,
                    "body" => $body,
                )
            );

            $RESPONSE = json_encode($data);
            // Geben den Header zusammen mit dem Autorisierungsschlüssel an
            $headers = array(
                'Authorization:key=' . env("FCM_KEY", ""),
                'Content-Type: application/json',
            );

            // CURL Anfrage
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $RESPONSE);

            $output = curl_exec($ch);
            // Ausgabe ist falsch / nicht erfolgreich, protokollieren.
            if ($output === false) {
                die('Curl error: ' . curl_error($ch));
            }
            // schließ die  CURL Anfrage
            curl_close($ch);
        }
    }
}
