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

class Reminder extends Model {

    /**
     * @description   Gibt den Tabellennamen an, da Laravel den Namen des Modells verwendet, um die Tabelle festzulegen, aber unser Name unterscheidet sich, daher müssen wir ihn festlegen
     */
    protected $table = "reminders";

    /**
     * @description   Wir haben keine „updated_at“-Spalte, die bei der Migration automatisch erstellt wird
     */
    const UPDATED_AT = null;

    /**
     * @description    Gibt den zugehörigen Benutzer zurück.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo;
     */
    public function user() {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
