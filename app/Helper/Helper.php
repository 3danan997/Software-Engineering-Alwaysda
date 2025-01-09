<?php

/**
 * @description    Hilfs-Klasse. Sie besitzt Globale Funktionen die für speziale models und controller gebraucht werden.
 */

namespace App\Helper;

use App\Models\Misc\Contact;
use App\Models\Misc\Reminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Helper {

    // Globale Konstante für die minimale länge des Passworts.
    public const PASSWORD_MIN_LENGTH = 7;

    /**
     * @description    entnimmt die verfügbaren Erinnerungen.
     * @param int $id , die Benutzer id vom Kontakt
     * @param int $days_count , wie viele Tage zum eintragen.
     * @param boolean $include_exact ,bekommt genau zur richtigen Zeit eine Erinnerung.(wir beim versenden der
     *      Erinnerungs Mail benutzt) Es benötigt $days_count um es auf 0 zu setzten.
     * @return array|\Illuminate\Support\Collection;
     */
    public static function getNotifications($id, int $days_count = 7, $include_exact = false) {
        if (empty($id) === true) return collect();
        // days_count = 0 könnte als leer angesehen werden, deshalb brauchen wir es in unsere bedingung.
        if (empty($days_count) === true && $days_count != 0) return collect();

        $notifications = collect();

        $contacts = Contact::where("user_id", $id)->with("user")->get();

        foreach ($contacts as $contact) {

            $payload = (object)["first_name" => $contact->first_name, "last_name" => $contact->last_name, "full_name" => $contact->full_name, "avatar" => $contact->avatar, "contact_id" => $contact->id];

            if (empty($contact->notifications) === false) {
                foreach ($contact->notifications as $notification) {
                    // Es holt sich die Daten vom Kontakt
                    $type = $notification->type;
                    $frequency = $notification->frequency;
                    $frequency_time = $notification->time;
                    // Analysiert die Zeitzone in Berlin (Wir benötigen keine weitere Zeitzone da die Webseite ausschließlich für in Deutschland lebende Personen erstellt wurde)
                    $frequency_time = Carbon::createFromFormat("H:i", $frequency_time, "Europe/Berlin")->setTimezone("UTC")->format("H:i");
                    $frequency_value = $notification->frequency_value;


                    //Es wird das Datum des Erstellen vom Kontakten als ersten abgefragt
                    $contact_date = Carbon::parse($contact->created_at)->format("Y-m-d");
                    $contact_date = Carbon::createFromFormat("Y-m-d H:i", "{$contact_date} {$frequency_time}")->setSeconds(0);


                    // Falls wir eine Erinnerung haben wird die letzte genommen.
                    $last_reminder = Reminder::where("contact_id", $contact->id)->where("reminder_type", $type)->where("reminder_frequency", $frequency)->where("reminder_time", $notification->time);
                    if ($frequency === "Selbst eingeben...") {
                        //  Falls die Häufigkeit öfter vorkommt, dann wird noch eine "where" Bedingung hinzugefügt.
                        $last_reminder = $last_reminder->where("reminder_frequency_value", $frequency_value);
                    }
                    // Es wird nach den letzten Erinnerung geordnet und die erste Erinnerung der Liste wird entnommen (Welche die letzte eingefügte Erinnerung bei der orderBy Bedingung ist)
                    $last_reminder = $last_reminder->orderBy("created_at", "desc")->first();
                    if (empty($last_reminder) === false) {
                        //  Die letzte Erinnerung wird zur variable Eingeordnet und die Zeit zur Zeitzone angepasst.
                        $last_reminder = Carbon::parse($last_reminder->created_at)->setSeconds(0);
                    }

                    // Aktuelles Datum
                    $to_be_compared = Carbon::now()->setSeconds(0);

                    if ($frequency === "Jeden Tag") {
                        //Unser vergleich Punkt ist entweder das Datum oder die letzte Erinnerung. das zweite wird aus 0 gesetzt, weil wir das Datum und die Erinnerung ohne die Sekunden vergleichen.
                        $to_be_compared = Carbon::now()->setSeconds(0);

                        if (empty($last_reminder) === true) {
                            // Es gibt 3 verschiedene Möglichkeiten:
                            // [1] Es ist der selbe Tag, aber die Uhrzeit hat noch nicht die angegebene Zeit erreicht.
                            // [2] es ist noch nicht der selbe Tag, es wird dann bestätigt.
                            // [3] Gleicher Tag gleiche Stunde und Minute

                            // clone vergleicht das datum wenn wir isSameHour benutzen, deshalb erstellen wir eine neue Variable und setzten Sie auf das selbe.
                            $contact_date_time = clone $contact_date;
                            $contact_date_time = $contact_date_time->setDateFrom($to_be_compared);
                            if (

                                    ($contact_date->isAfter($to_be_compared) && !$include_exact) ||
                                    ($contact_date_time->isSameHour($to_be_compared) && $contact_date_time->isSameMinute($to_be_compared))

                            ) {
                                $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => (clone $to_be_compared)->setTimeFromTimeString($frequency_time), "payload" => $payload]);
                            }
                        } else {
                            // Es wird gecheckt ob es nicht der selbe Tag ist und ob die derzeitige Zeit nach der letzten Erinnerung ist.
                            // z.B. Die letzte Erinnerung ist von Gestern, es wird gecheckt ob es stimmt, falls es stimmt sollte heute eine Erinnerung kommen.
                            if ($last_reminder->isSameDay($to_be_compared) === false && (($to_be_compared->isAfter($last_reminder) && !$include_exact) || ($last_reminder->isSameHour($to_be_compared) && $last_reminder->isSameMinute($to_be_compared)))) {
                                $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $contact_date, "payload" => $payload]);
                            }
                        }

                        // Checkt den nächsten Tag und schaut wie viele Tagen noch kommen.
                        for ($i = 1; $i <= $days_count; $i++) {
                            // Wir benutzen Klone (clone) um ^n addDays zu vermeiden, deshalb klonen wir es jedes mal un speichern es in einer neuen Variable.
                            $new_date = clone $to_be_compared;
                            $new_date = $new_date->addDays($i)->setTimeFromTimeString($frequency_time);
                            $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $new_date, "payload" => $payload]);
                        }

                    }
                    else if ($frequency === "Jede Woche") {
                        if(empty($last_reminder) === true) {
                            // Es gibt 3 verschiedene Möglichkeiten:
                            // [1] Es ist der selbe Tag, aber die Uhrzeit hat noch nicht die angegebene Zeit erreicht.
                            // [2] es ist noch nicht der selbe Tag, es wird dann bestätigt.
                            // [3] Gleicher Tag gleiche Stunde und Minute

                            // clone vergleicht das datum wenn wir isSameHour benutzen, deshalb erstellen wir eine neue Variable und setzten Sie auf das selbe.
                            $contact_date_time = (clone $contact_date)->setDateFrom($to_be_compared);
                            if (
                                $contact_date->isSameDay($to_be_compared) &&
                                (($contact_date->isAfter($to_be_compared) && !$include_exact) ||
                                    ($contact_date_time->isSameHour($to_be_compared) && $contact_date_time->isSameMinute($to_be_compared)))

                            ) {
                                $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $contact_date, "payload" => $payload]);
                            } else if ($contact_date->isSameDay($to_be_compared) === false){
                                // Kompatieble variablen zum checken der Erinnerung falls es keine n Wochen sind.
                                // Wir setzten die Zeit auf den start des Tages zurück.
                                $contact_date_start_of_day = (clone $contact_date)->startOfDay();
                                $to_be_compared_start_of_day = (clone $to_be_compared)->startOfDay();
                                $mod_since_contact_date = $to_be_compared_start_of_day->floatDiffInWeeks($contact_date_start_of_day);
                                if (
                                    self::is_decimal($mod_since_contact_date) === false &&
                                    (($contact_date_time->isAfter($to_be_compared) && !$include_exact) ||
                                        ($contact_date_time->isSameHour($to_be_compared) && $contact_date_time->isSameMinute($to_be_compared)))

                                )  {
                                    $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $to_be_compared, "payload" => $payload]);
                                }
                            }
                        } else {
                            // Wir haben eine Erinnerung. Check ob es ein Wöchentlicher interval ist.
                            $last_reminder_start_of_day = (clone $last_reminder)->startOfDay();
                            $mod_since_last_reminder = (clone $to_be_compared)->startOfDay()->floatDiffInWeeks($last_reminder_start_of_day);
                            // Falls es genau n Wochen (2 Wochen, 3 Wochen, ... )  aber mit verschiedenen Zeiten dann ist es das selbe.
                            // Anschließend  checken wir ob die Zeit bevor oder nach der Erinnerung ist, falls es davor ist dann zeigen wir die Erinnerung.
                            // clone vergleicht das datum wenn wir isSameHour benutzen, deshalb erstellen wir eine neue Variable und setzten Sie auf das selbe.
                            $last_reminder_date_time = (clone $last_reminder)->setDateFrom($to_be_compared);
                            if (
                                $mod_since_last_reminder > 0 && self::is_decimal($mod_since_last_reminder) === false &&
                                (($last_reminder_date_time->isAfter($to_be_compared) && !$include_exact) ||
                                    ($last_reminder_date_time->isSameHour($to_be_compared) && $last_reminder_date_time->isSameMinute($to_be_compared)))

                            )  {
                                $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $to_be_compared, "payload" => $payload]);
                            }
                        }


                        /* Behandelt andere Tage in einer schleife*/
                        for ($i = 1; $i <= $days_count; $i++) {
                            $new_date = (clone $to_be_compared)->startOfDay()->addDays($i);
                            if (empty($last_reminder) === true) {
                                // Wir benutzen das Kontakt Datum als Quelle.
                                if (self::is_decimal((clone $contact_date)->startOfDay()->floatDiffInWeeks($new_date)) === false) {
                                    $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => (clone $new_date)->setTimeFromTimeString($frequency_time), "payload" => $payload]);
                                }
                            } else {
                                if (self::is_decimal((clone $last_reminder)->startOfDay()->floatDiffInWeeks($new_date)) === false) {
                                    $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => (clone $new_date)->setTimeFromTimeString($frequency_time), "payload" => $payload]);
                                }
                            }
                        }
                    } else if ($frequency === "Jeden Monat") {
                        if(empty($last_reminder) === true) {
                            // Es gibt 3 verschiedene Möglichkeiten:
                            // [1] Es ist der selbe Tag, aber die Uhrzeit hat noch nicht die angegebene Zeit erreicht.
                            // [2] Es ist nicht der selbe Tag, wir bestätigen das es nicht n Wochen sind
                            // [3] Gleicher Tag gleiche Stunde und Minute

                            // clone vergleicht das datum wenn wir isSameHour benutzen, deshalb erstellen wir eine neue Variable und setzten Sie auf das selbe.
                            $contact_date_time = (clone $contact_date)->setDateFrom($to_be_compared);
                            if (
                                $contact_date->isSameDay($to_be_compared) &&
                                (($contact_date->isAfter($to_be_compared) && !$include_exact) ||
                                    ($contact_date_time->isSameHour($to_be_compared) && $contact_date_time->isSameMinute($to_be_compared)))

                            ) {
                                $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $contact_date, "payload" => $payload]);
                            } else if ($contact_date->isSameDay($to_be_compared) === false){
                                // Kompatieble variablen zum checken der Erinnerung falls es keine n Wochen sind.
                                // Wir setzten die Zeit auf den start des Tages zurück.
                                $contact_date_start_of_day = (clone $contact_date)->startOfDay();
                                $to_be_compared_start_of_day = (clone $to_be_compared)->startOfDay();
                                $mod_since_contact_date = $to_be_compared_start_of_day->floatDiffInMonths($contact_date_start_of_day);
                                if (
                                    self::is_decimal($mod_since_contact_date) === false &&
                                    (($contact_date_time->isAfter($to_be_compared) && !$include_exact) ||
                                        ($contact_date_time->isSameHour($to_be_compared) && $contact_date_time->isSameMinute($to_be_compared)))

                                )  {
                                    $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $to_be_compared, "payload" => $payload]);
                                }
                            }
                        } else {
                            // Wir haben eine Erinnerung. Check ob es ein Wöchentlicher interval ist.
                            $last_reminder_start_of_day = (clone $last_reminder)->startOfDay();
                            $mod_since_last_reminder = (clone $to_be_compared)->startOfDay()->floatDiffInMonths($last_reminder_start_of_day);
                            // Falls es genau n Wochen (2 Wochen, 3 Wochen, ... )  aber mit verschiedenen Zeiten dann ist es das selbe.
                            // Anschließend  checken wir ob die Zeit bevor oder nach der Erinnerung ist, falls es davor ist dann zeigen wir die Erinnerung.
                            // clone vergleicht das datum wenn wir isSameHour benutzen, deshalb erstellen wir eine neue Variable und setzten Sie auf das selbe.
                            $last_reminder_date_time = (clone $last_reminder)->setDateFrom($to_be_compared);
                            if (
                                $mod_since_last_reminder > 0 && self::is_decimal($mod_since_last_reminder) === false &&
                                (($last_reminder_date_time->isAfter($to_be_compared) && !$include_exact) ||
                                    ($last_reminder_date_time->isSameHour($to_be_compared) && $last_reminder_date_time->isSameMinute($to_be_compared)))

                            )  {
                                $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $to_be_compared, "payload" => $payload]);
                            }

                        }


                        /* Behandelt andere Tage in einer schleife */
                        for ($i = 1; $i <= $days_count; $i++) {
                            $new_date = (clone $to_be_compared)->startOfDay()->addDays($i);
                            if (empty($last_reminder) === true) {
                                // Wir benutzen das Kontakt Datum als Quelle.
                                if (self::is_decimal((clone $contact_date)->startOfDay()->floatDiffInMonths($new_date)) === false) {
                                    $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => (clone $new_date)->setTimeFromTimeString($frequency_time), "payload" => $payload]);
                                }
                            } else {
                                if (self::is_decimal((clone $last_reminder)->startOfDay()->floatDiffInMonths($new_date)) === false) {
                                    $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => (clone $new_date)->setTimeFromTimeString($frequency_time), "payload" => $payload]);
                                }
                            }
                        }
                    } else if ($frequency === "Selbst eingeben...") {
                        if(empty($last_reminder) === true) {
                            // Es gibt 3 verschiedene Möglichkeiten:
                            // [1] Es ist der selbe Tag, aber die Uhrzeit hat noch nicht die angegebene Zeit erreicht.
                            // [2] Es ist nicht der selbe Tag, wir bestätigen das es nicht n Wochen sind
                            // [3] Gleicher Tag gleiche Stunde und Minute

                            // clone vergleicht das datum wenn wir isSameHour benutzen, deshalb erstellen wir eine neue Variable und setzten Sie auf das selbe.
                            $contact_date_time = (clone $contact_date)->setDateFrom($to_be_compared);
                            if (
                                $contact_date->isSameDay($to_be_compared) &&
                                (($contact_date->isAfter($to_be_compared) && !$include_exact) ||
                                    ($contact_date_time->isSameHour($to_be_compared) && $contact_date_time->isSameMinute($to_be_compared)))

                            ) {
                                $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $contact_date, "payload" => $payload]);
                            } else if ($contact_date->isSameDay($to_be_compared) === false){
                                // Kompatieble variablen zum checken der Erinnerung falls es keine n Wochen sind.
                                // Wir setzten die Zeit auf den start des Tages zurück.
                                $contact_date_start_of_day = (clone $contact_date)->startOfDay();
                                $to_be_compared_start_of_day = (clone $to_be_compared)->startOfDay();
                                $mod_since_contact_date = $to_be_compared_start_of_day->floatDiffInDays($contact_date_start_of_day);
                                if (
                                    self::is_decimal($mod_since_contact_date) === false &&  ($mod_since_contact_date % $frequency_value === 0) &&
                                    (($contact_date_time->isAfter($to_be_compared) && !$include_exact) ||
                                        ($contact_date_time->isSameHour($to_be_compared) && $contact_date_time->isSameMinute($to_be_compared)))

                                )  {
                                    $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $to_be_compared, "payload" => $payload]);
                                }
                            }
                        } else {
                            // Wir haben eine Erinnerung. Check ob es ein Wöchentlicher interval ist.
                            $last_reminder_start_of_day = (clone $last_reminder)->startOfDay();
                            $mod_since_last_reminder = (clone $to_be_compared)->startOfDay()->floatDiffInDays($last_reminder_start_of_day);
                            // Falls es genau n Wochen (2 Wochen, 3 Wochen, ... )  aber mit verschiedenen Zeiten dann ist es das selbe.
                            // Anschließend  checken wir ob die Zeit bevor oder nach der Erinnerung ist, falls es davor ist dann zeigen wir die Erinnerung.
                            // clone vergleicht das datum wenn wir isSameHour benutzen, deshalb erstellen wir eine neue Variable und setzten Sie auf das selbe.
                            $last_reminder_date_time = (clone $last_reminder)->setDateFrom($to_be_compared);
                            if (
                                $mod_since_last_reminder > 0 && self::is_decimal($mod_since_last_reminder) === false &&  ($mod_since_last_reminder % $frequency_value === 0) &&
                                (($last_reminder_date_time->isAfter($to_be_compared) && !$include_exact) ||
                                    ($last_reminder_date_time->isSameHour($to_be_compared) && $last_reminder_date_time->isSameMinute($to_be_compared)))

                            )  {
                                $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => $to_be_compared, "payload" => $payload]);
                            }
                        }


                        /* Behandelt andere Tage in einer schleife */
                        for ($i = 1; $i <= $days_count; $i++) {
                            $new_date = (clone $to_be_compared)->startOfDay()->addDays($i);
                            if (empty($last_reminder) === true) {
                                // Wir benutzen das Kontakt Datum als Quelle.
                                $reminder_in_days = (clone $contact_date)->startOfDay()->floatDiffInDays($new_date);
                            } else {
                                $reminder_in_days = (clone $last_reminder)->startOfDay()->floatDiffInDays($new_date);
                            }
                            if (self::is_decimal($reminder_in_days) === false && ($reminder_in_days % $frequency_value === 0)) {
                                $notifications->push((object)["type" => $type, "frequency" => $frequency, "remind_at" => (clone $new_date)->setTimeFromTimeString($frequency_time), "payload" => $payload]);
                            }
                        }
                    }
                }
            }

            // Bekomme den Geburtstag vom Kontakten
            $birthday = Carbon::parse($contact->birthday);
            // setzte das Jahr des Gebutstags entsprechend
            $birthday = $birthday->year(date("Y"));
            // Berechne die differnz aus den Tagen des Gebutstags und des aktuelen Tages.
            $birthday_diff = Carbon::now()->diffInDays($birthday->startOfDay(), false);

            // Checkt ob das Datum noch nicht schon im Jahr vorbei ist.
            if ($birthday_diff <= $days_count && $birthday_diff >= 0) {
                // Checkt ob es eine Erinnerung deises Jahr schon versendet hat
                // Das kann nur einmal vorkommen, wenn wir eine Erinnerung an unseren Planer in Kernel.php senden.
                $birthday_reminder = Reminder::where("user_id", $id)->where("contact_id", $contact->id)->where("reminder_type", "Geburtstag")->where("reminder_frequency", "Jedes Jahr")->whereDate("created_at", ">=", Carbon::now()->startOfYear())->whereDate("created_at", "<=", Carbon::now()->endOfYear())->count();

                if (empty($birthday_reminder) === true || $birthday_reminder == 0)
                    // Falls es nicht existiert dann erstelle eine Benachrichtigung.
                    // Setzte die Zeit auf die Berliner Zeitzone und bekomme die Koordinierte Weltziet.
                    $birthday = $birthday->setTimezone("Europe/Berlin")->setHour(0)->setMinutes(0)->setSeconds(0)->utc();


                $current_time = Carbon::now()->setSeconds(0);
                if(!$include_exact || ($include_exact && $birthday->isSameHour($current_time) && $birthday->isSameMinute($current_time))) {
                    $notifications->push((object)["type" => "Geburtstag", "frequency" => "Jedes Jahr", "remind_at" => $birthday, "payload" => $payload]);
                }
            }

        }

        /* grunde Werte zu verwenden: https://stackoverflow.com/questions/42754075/laravel-collection-sortby-not-taking-effect */
        $notifications = $notifications->sortBy("remind_at")->values();
        return $notifications;
    }

    /**
     * @description    Bestätige falls der bekommene Wert eine dezimal Zahl ist. Die werte können als String wiedergeben werden
     *      Daher hilft diese Funktion, den tatsächlichen Typ der Variablen zu bestimmen.
     * @param $val , Der Wert den wir zum checken benötigen.
     * @return boolean;
     */
    private static function is_decimal($val) {
        return is_numeric($val) && floor($val) != $val;
    }

    /**
     * @description    gibt eine Benutzerdefinierte Antwort des Prüfers wieder.
     * @param $v , instanz des Prüfers.
     * @return \Illuminate\Http\JsonResponse;
     */
    public static function getValidatorMessage($v) {
        if (empty($v) === true) {
            return response()->json(array('status' => 'error', 'message' => "Our system failed to identify what error caused this to happen. Please contact us."), 422);
        }

        return response()->json(array('status' => 'error', 'message' => implode(" | ", $v->messages()->all())), 422);
    }


    /**
     * @description   gibt einen zufälligen Benutzername der länge x.
     * @param int $length , default länge eines zufälligen Schlüssels.
     * @return string;
     */
    public static function getRandomUsername($length = 15) {
        // englisches Alphabet
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // bekommt die länge der erlaubten Zeichenkette (characters)
        $charactersLength = strlen($characters);
        $randomString = '';
        // Schleife bis zur angegebenen Länge im Parameterabschnitt
        // Wir verwenden die rand-Funktion, um das neue zufällige Zeichen anzuhängen.
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}

?>
