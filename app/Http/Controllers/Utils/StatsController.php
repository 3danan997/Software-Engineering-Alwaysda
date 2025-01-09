<?php

/**
 * @description    StatsController Klasse. Gibt die Statistiken anderer Modelle zurück. Verantwortlich für die Statistikansicht.
 */

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

class StatsController extends Controller {

    /**
     * @description    Holen Sie sich die Gesamtstatistik.
     * @param $request, Instanz der Anforderung, die die Methode aufgerufen hat (Hat Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function getStats(Request $request) {
        // Erstellen Sie ein Platzhalter-Array
        $stats = array();

        // Erstellen Sie eine Referenz der Kontakte
        // Erhalten Sie nur das, was vom angemeldeten Benutzer erstellt wurde!
        $contacts = Contact::where("user_id", Auth::id());

        // Übergeben Sie die Zählung
        $stats["contacts_count"] = $contacts->count();

        // Rufen Sie die Einstellungen der Erinnerungen für jeden Kontakt ab
        $reminders_count = 0;

        // Durchschleifen Sie die Kontakte
        foreach($contacts->get() as $contact) {
            if(empty($contact->notifications) === false)
               $reminders_count += sizeof($contact->notifications);
        }
        // Übergeben Sie die Zählung
        $stats["reminders_count"] = $reminders_count;

        // Erhalten Sie die bevorstehenden Benachrichtigungen der nächsten 7 Tage
        $notifications_current_week_count = sizeof(Helper::getNotifications(Auth::id(), 7));
        // Wenn die Antwort null ist, sagen wir, dass die Anzahl 0 ist
        if(empty($notifications_current_week_count) === true) $notifications_current_week_count = 0;
        // Übergeben Sie die Zählung
        $stats["notifications_current_week_count"] = $notifications_current_week_count;
        // Geben Sie die Gesamtantwort des Arrays zurück und wandeln Sie das Array in ein Objekt um, damit wir direkt am Front-End darauf zugreifen können
        return response()->json(array("status" => "success",  "stats" => (object) $stats), 200);
    }

}
