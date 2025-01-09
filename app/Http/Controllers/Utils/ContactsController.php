<?php

/**
 * @description ContactsController. Verantwortlich für die Kontaktansicht.
 * */

namespace App\Http\Controllers\Utils;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Misc\Contact;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPUnit\Util\Exception;

class ContactsController extends Controller {

    /**
     * @description    Erhalten Sie alle Kontakte des angemeldeten Benutzers
     * @param $request, Instanz der Anforderung, die die Methode aufgerufen hat (Hat Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function getContacts(Request $request) {
        // Erstellen Sie eine Instanz des Modells
        $contacts = new Contact();

        // Erhalten Sie nur das, was der angemeldete Benutzer erstellt hat
        $contacts = $contacts->where("user_id", Auth::id());
        // Sortiere absteigend
        $contacts = $contacts->orderBy("id", "desc");

        // Zähle es
        $total_count = $contacts->count();
        // Hole es
        $contacts = $contacts->get();
        // Gebe die Antwort zurück
        return response()->json(array("contacts" => $contacts, "total_count" => $total_count), 200);
    }

    /**
     * @description    Kontakt erstellen
     * @param $request, Instanz der Anforderung, die die Methode aufgerufen hat (Hat Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function createContact(Request $request) {
        // Validieren Sie die erforderlichen, obligatorischen Spalten
        $v = Validator::make($request->all(), array(
            'first_name' => 'required',
            'last_name' => 'required',
            'birthday' => 'required|date_format:Y-m-d',
            'priority' => 'required',
            'reminders' => 'required',
        ));

        // Validator hat fehlende erforderliche Werte gefunden
        if ($v->fails()) {
            return Helper::getValidatorMessage($v);
        }

        try {
            $reminders = json_decode($request->reminders, true);
            foreach($reminders as $reminder) {
                if(isset($reminder["frequency"]) === false ||  isset($reminder["type"]) === false) {
                    throw new Exception("Die Format von den Erinnerungen ist nicht korrekt.");
                }

                if($reminder["frequency"] == "Selbst eingeben..." && empty($reminder["frequency_value"])) {
                    throw new Exception("Die Format von den Erinnerungen ist nicht korrekt.");
                }
            }
        } catch (\Throwable $e) {
            return response()->json(array("status" => "failure", "message" => "Kontakt konnte nicht bearbeitet werden. Grund: " . $e->getMessage()), 500);
        }

        // Verwenden Sie DB::transaction, damit wir im Falle eines Fehlers alle Dinge rückgängig machen, die wir in die Datenbank eingefügt haben
        try {
            DB::transaction(function () use ($request) {
                // Erstellen Sie eine Instanz des Modells
                $contact = new Contact();
                // Setzen Sie alle Werte, ob sie bestanden wurden oder nicht
                $contact->first_name = $request->first_name;
                $contact->last_name = $request->last_name;
                $contact->mobile_number = $request->mobile_number;
                $contact->birthday = $request->birthday;
                $contact->priority = $request->priority;
                $contact->social_media = $request->social_media;
                $contact->reminders = $request->reminders;
                $contact->user_id = Auth::id();

                // Überprüfen Sie, ob es eine übergebene Avatar-Datei gibt (Base64-Format)
                if($request->file("avatar") !== null) {
                    // Holen Sie sich die Informationen darüber
                    $array = explode('.', $request->file("avatar")->getClientOriginalName());
                    // Die Erweiterung basiert auf dem Ende davon
                    $ext = end($array);
                    // Zufälliger Name für die Zeichenfolge zusammen mit dem $ext
                    $avatar = Str::random(32) . "." . $ext;
                    // Result ist das Ergebnis der Speicherung der Datei in unserem Verzeichnis
                    // es sollte public/avatars/file_name.ext sein
                    $result = $request->file("avatar")->storeAs("avatars", $avatar, "public");
                    if (empty($result) === false) {
                        // Der Avatar wurde gespeichert, setzen Sie ihn auf die Instanz des Benutzers
                        $contact->avatar = $avatar;
                    }
                }

                // Aktualisieren Sie die Instanz
                $contact->saveOrFail();
            });
        } catch (\Throwable $e) {
            /* todo richtige Nachricht */
            return response()->json(array("status" => "failure", "message" => "Kontakt konnte nicht erstellt werden. Grund: " . $e->getMessage()), 500);
        }


        return response()->json(array("status" => "success", "message" => "Kontakt wurde erstellt!."), 200);
    }

    /**
     * @description    Kontakt aktualisieren
     * @param $request, Instanz der Anforderung, die die Methode aufgerufen hat (Hat Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function updateContact(Request $request, $id) {
        // Validieren Sie die erforderlichen, obligatorischen Spalten
        $v = Validator::make($request->all(), array(
            'first_name' => 'required',
            'last_name' => 'required',
            'birthday' => 'required|date_format:Y-m-d',
            'priority' => 'required',
            'reminders' => 'required',
        ));

        // Validator hat fehlende erforderliche Werte gefunden
        if ($v->fails()) {
            return Helper::getValidatorMessage($v);
        }

        try {
            $reminders = json_decode($request->reminders, true);
            foreach($reminders as $reminder) {
                if(isset($reminder["frequency"]) === false ||  isset($reminder["type"]) === false) {
                    throw new Exception("Die Format von den Erinnerungen ist nicht korrekt.");
                }

                if($reminder["frequency"] == "Selbst eingeben..." && empty($reminder["frequency_value"])) {
                    throw new Exception("Die Format von den Erinnerungen ist nicht korrekt.");
                }
            }
        } catch (\Throwable $e) {
            return response()->json(array("status" => "failure", "message" => "Kontakt konnte nicht bearbeitet werden. Grund: " . $e->getMessage()), 500);
        }

        // Verwenden Sie DB::transaction, damit wir im Falle eines Fehlers alle Dinge rückgängig machen, die wir in die Datenbank eingefügt haben
        try {
            DB::transaction(function () use ($request, $id) {
                $contact = Contact::find($id);
                $contact->first_name = $request->first_name;
                $contact->last_name = $request->last_name;
                $contact->mobile_number = $request->mobile_number;
                $contact->birthday = $request->birthday;
                $contact->priority = $request->priority;
                $contact->social_media = $request->social_media;
                $contact->reminders = $request->reminders;
                $avatar_changed = ($request->avatar_changed === "true");

                // Wenn sich der Avatar geändert hat, prüfen wir, ob er existiert oder nicht
                if($avatar_changed === true) {
                    if($request->file("avatar") !== null) {
                        // Der Benutzer hat einen neuen Avatar bereitgestellt

                        // Wenn der Benutzer bereits einen Avatar gespeichert hat, löschen Sie ihn aus unserem Verzeichnis
                        if(empty($contact->avatar) === false) {
                            Storage::disk("public")->delete("avatars" ."/" . $contact->avatar);
                        }

                        // Holen Sie sich die Informationen darüber
                        $array = explode('.', $request->file("avatar")->getClientOriginalName());
                        // Die Erweiterung basiert auf dem Ende davon
                        $ext = end($array);
                        // Zufälliger Name für die Zeichenfolge zusammen mit dem $ext
                        $avatar = Str::random(32) . "." . $ext;
                        // Result ist das Ergebnis der Speicherung der Datei in unserem Verzeichnis
                        // es sollte public/avatars/file_name.ext sein
                        $result = $request->file("avatar")->storeAs("avatars", $avatar, "public");
                        if (empty($result) === false) {
                            $contact->avatar = $avatar;
                        }
                    } else {
                        // Der Benutzer hat den Avatar gelöscht, daher löschen wir ihn aus unserem Verzeichnis
                        // Wir prüfen, ob die Instanz des Benutzers einen Avatarwert hat
                        if(empty($contact->avatar) === false) {
                            Storage::disk("public")->delete("avatars" ."/" . $contact->avatar);
                            // Aktualisieren Sie den Wert
                            $contact->avatar = null;
                        }
                    }

                }

                // Aktualisieren Sie die Instanz
                $contact->saveOrFail();
            });
        } catch (\Throwable $e) {
            return response()->json(array("status" => "failure", "message" => "Kontakt konnte nicht bearbeitet werden. Grund: " . $e->getMessage()), 500);
        }


        return response()->json(array("status" => "success", "message" => "Kontakt wurde bearbeitet!."), 200);
    }
    /**
     * @description    Den Kontakt löschen
         * @param $request, Instanz der Anforderung, die die Methode aufgerufen hat (Hat Benutzerinformationen).
     * @param $id, die ID des Kontakts
     * @return \Illuminate\Http\JsonResponse;
     */

    public function deleteContact(Request $request, $id) {
        // Versuchen Sie, den Kontakt anhand der übergebenen ID zu finden
        $contact = Contact::find($id);

        // Die ID konnte nicht gefunden werden, senden Sie die Nachricht zurück
        if(empty($contact) === true) {
            return response()->json(array("status" => "failure", "message" => "Kontakt wurde nicht gefunden."), 500);
        }

        // Verwenden Sie DB::transaction, damit wir im Falle eines Fehlers alle Dinge rückgängig machen, die wir in die Datenbank eingefügt haben
        try {
            DB::transaction(function() use ($contact) {
                // Überprüfen Sie, ob der Kontakt einen Avatar hat, dann löschen wir ihn aus unserem Speicher
                if(empty($contact->avatar) === false) {
                    Storage::disk("public")->delete("avatars" ."/" . $contact->avatar);
                }

                // Löschen Sie den Kontakt
                $contact->delete();
            });
        } catch (\Throwable $e) {
            return response()->json(array("status" => "failure", "message" => "Kontakt konnte nicht gelöscht werden. Grund: " . $e->getMessage()), 500);
        }

        // Geben Sie die Antwort zurück
        return response()->json(array("status" => "success", "message" => "Kontakt wurde gelöscht."), 200);
    }
    /**
     * @description    Rufen Sie den Kontakt für Anzeigezwecke ab
     * @param $request, Instanz der Anforderung, die die Methode aufgerufen hat (Hat Benutzerinformationen).
     * @param $id, die ID des Kontakts
     * @return \Illuminate\Http\JsonResponse;
     */
    public function getProfile(Request $request, $id) {
        // Versuchen Sie, den Kontakt anhand der übergebenen ID zu finden
        $contact = Contact::find($id);

        // Die ID konnte nicht gefunden werden, senden Sie die Nachricht zurück
        if(empty($contact) === true) {
            return response()->json(array("status" => "success", "message" => "Kontakt wurde nicht gefunden."), 500);
        }
        // Es wurde gefunden, geben Sie das Objekt zurück
        return response()->json(array("status" => "success",  "contact" => $contact), 200);
    }

}
