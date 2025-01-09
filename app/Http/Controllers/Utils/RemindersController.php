<?php

/**
 * @description    RemindersController Klasse. Gibt die Erinnerungen von x Tagen zurück. Verantwortlich für die Ansicht "Erinnerungen".
 */


namespace App\Http\Controllers\Utils;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Misc\Contact;
use App\Models\Misc\Reminder;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RemindersController extends Controller {

    /**
     * @description    Erhalten Sie die Erinnerungen der folgenden 7 Tage, einschließlich desselben Tages.
     * @param $request, Instanz der Anforderung, die die Methode aufgerufen hat (Hat Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function getReminders(Request $request) {
        // Rufen Sie die Methode des Helfers auf
        $reminders = Helper::getNotifications(Auth::id());
        // Geben Sie die Antwort zurück
        return response()->json(array("status" => "success",  "reminders" => $reminders), 200);
    }

    /**
     * @description    Erhalten Sie die Erinnerungen am selben Tag. Diese Funktion ist für das Versenden von Benachrichtigungen auf der Plattform verantwortlich
     * @param $request, Instanz der Anforderung, die die Methode aufgerufen hat (Hat Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function getRemindersToday(Request $request) {
        // Erhalten Sie Erinnerungen, die nur ab heute gültig sind. Nimm keine alten
        $reminders = Reminder::where("user_id", Auth::id())->with("user")->where("updated_at", "<>", null)->whereDate("updated_at", ">=", Carbon::now()->startOfDay());
        // Klonen Sie die Reminders-Instanz, damit wir im Hauptaufruf nichts ändern.
        $reminders_update = clone $reminders;
        // Holen Sie sich diese Erinnerungen
        $reminders = $reminders->get();
        // Legen Sie "updated_at" als null fest. Das bedeutet, dass die Erinnerung abgerufen und als Benachrichtigung angezeigt wurde
        // Damit wir es nicht erneut als angezeigte Benachrichtigung duplizieren und erneut anzeigen müssen
        $reminders_update->update([
            "updated_at" => null
        ]);


        return response()->json(array("status" => "success",  "reminders" => $reminders), 200);
    }
}
