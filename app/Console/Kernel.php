<?php

namespace App\Console;

use App\Helper\Helper;
use App\Jobs\JobEmail;
use App\Models\Misc\Contact;
use App\Models\Misc\Reminder;
use App\Models\User\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel {
    /**
     * Die von Ihrer Anwendung bereitgestellten Artisan-Befehle.
     * @var array
     */
    protected $commands = [//
    ];

    /**
     * Definieren Sie den Befehlszeitplan der Anwendung.
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        // Gibt den Benutzer wieder und holt sich die Erinnerungen basierend auf ihrer user_id
        $users = User::select("id", "first_name", "last_name")->get();

        foreach ($users as $user) {
            // Bei jedem Aufruf, erhalten wir den aktuellen Tag. Wir benötigen die exakte Zeit, damit wir eine korrekte E-Mail / Benachrichtigung senden können.
            $reminders = Helper::getNotifications($user->id, 0, true);

            // Wir schleifen durch
            foreach ($reminders as $reminder) {
                // Prüfe, ob die Erinnerung schon losgeschickt wurde
                if(empty($reminder->payload) === false && empty($reminder->payload->contact_id) === false) {

                $contact_user = Contact::find($reminder->payload->contact_id);
                $has_sent = Reminder::where("reminder_type",$reminder->type)->where("contact_id", $reminder->payload->contact_id)->where("reminder_frequency_value", $reminder->frequency_value ?? null)->where("reminder_frequency", $reminder->frequency)->where("reminder_time", $reminder->remind_at)->count() > 0;

                if ($has_sent === true) continue;

                // Wir erhalten den Erinnerungstext
                $reminder_text = $this->getReminderText($reminder, $contact_user->first_name . " " . $contact_user->last_name);

                // Wir Ignorieren und wechseln zur nächsten Iteration der Sammlung, da der Erinnerungstext leer ist und wir keine leere Erinnerung senden möchten (er könnte völlig ungültig sein)
                if (empty($reminder_text) === true) continue;


                // Plant eine E-Mail mit dem Kontakt 'user_id' und dem Erinnerungstext, den wir gerade abgerufen haben
                $schedule->job(new JobEmail($user->id, "reminder_email", $reminder_text));

                    // Wenn die Payload der Benachrichtigung gültig ist, erstellen wir einen Erinnerungsdatensatz.
                    // Wir setzen das updated_at als current_time
                    // Im Front-End prüfen wir, ob updated_at NICHT null ist, dann senden wir eine Front-End-Benachrichtigung
                    $reminder_record = new Reminder();
                    $reminder_record->user_id = $user->id;
                    $reminder_record->contact_id = $reminder->payload->contact_id;
                    $reminder_record->reminder_type = $reminder->type;
                    $reminder_record->updated_at = Carbon::now();
                    $reminder_record->reminder_frequency = $reminder->frequency;
                    $reminder_record->reminder_time = $reminder->remind_at;
                    $reminder_record->reminder_frequency_value = $reminder->frequency_value ?? null;
                    $reminder_record->save();
                }

            }
        }
    }
    /**
     * @description Gibt den Erinnerungstext der Benachrichtigung zurück
     * @return string|null
     */
    protected function getReminderText($item, $full_name) {

        // Normale Kontrolluntersuchungen, mit den If-Abfragen gucken wir, um welchen Erinnerungstyp es sich hierbei handelt
        if (empty($item) === true) return null;

        $type = $item->type;
        $text = null;

        if ($type === "Treffen") {
            $text = "Sie sollten sich Heute mal wieder mit <strong>" . $full_name . "</strong> treffen!";
        } else if ($type === "Geburtstag") {
            $text = "Sie sollten nicht vergessen " . $full_name . " zum Geburtstag zu gratulieren!";
        } else if ($type === "Telefonieren") {
            $text = "Sie sollten Heute mal wieder " . $full_name . " anrufen!";
        } else if ($type === "Schreiben") {
            $text = "Sie sollten Heute mal wieder eine Nachricht bei <strong>" . $full_name .  "</strong> hinterlassen!";
        } else if ($type === "Sonst") {
            $text = "Sie sollten sich mal wieder bei " . $full_name . " melden!";
        }

        return $text;
    }

    /**
     * Registrieren Sie die Befehle für die Anwendung.
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
