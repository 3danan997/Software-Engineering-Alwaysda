<?php

/**
 * @author: Mohammad Hammado 14.07.2022, 16:00
 * @version 1.0
 * @since 1.0
 * @description    Eloquent model Implementierung
 */

namespace App\Models\Misc;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model {

    /**
     * @description   Gibt den Tabellennamen an, da Laravel den Namen des Modells verwendet, um die Tabelle festzulegen, aber unser Name unterscheidet sich, daher müssen wir ihn festlegen
     */
    protected $table = "contacts";

    /**
     * @description   Attribute anhängen. Dies sind benutzerdefinierte Werte, die später in benutzerdefinierten Funktionen adressiert werden können
     */
    protected $appends = ["full_name", "notifications"];

    /**
     * @description     Gibt den vollständigen Namen des Kontakts zurück, basierend auf einer Verknüpfung des Vornamens (first_name) und Nachnamens (last_name).
     * @return string;
     */
    public function getFullNameAttribute() {
        return $this->first_name . " " . $this->last_name;
    }

    /**
     * @description    Wir verwenden keine Relation, stattdessen verwenden wir eine json string, um die Erinnerungseinstellungen zu analysieren. Wir müssen es analysieren um es zur späteren Verwendung an das Front-End zurückgeben zu können .
     * @return object|null;
     */
    public function getNotificationsAttribute() {
        if(empty($this->reminders) === true) return null;
        try {
            return json_decode($this->reminders);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @description    Gibt den zugehörigen Benutzer zurück
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo;
     */
    public function user() {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
