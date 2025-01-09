<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model {

    /**
     * @description   Gibt den Tabellennamen an, da Laravel den Namen des Modells verwendet, um die Tabelle festzulegen, aber unser Name unterscheidet sich, daher müssen wir ihn festlegen
     */
    protected $table = "passwords_resets";

    /**
     * @description   Ausfüllbare Spalten, wenn wir eine direkte Erstellung Model::create oder Mode::update verwenden
     */
    protected $fillable = ["used_at"];

    /**
     * @description   Wir haben keine „updated_at“-Spalte, die bei der Migration automatisch erstellt wird
     */
    const UPDATED_AT = null;
}
