<?php

/**
 * @author: Mohammad Hammado 14.07.2022, 16:00
 * @version 1.0
 * @since 1.0
 * @description    Eloquent model Implementierung
 */

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens;

    /**
     * @description   Attribute anhängen. Dies sind benutzerdefinierte Werte, die später in benutzerdefinierten Funktionen adressiert werden können
     */
    protected $appends = ["full_name"];

    /**
     * @description   Ausfüllbare Spalten, wenn wir eine direkte Erstellung Model::create oder Mode::update verwenden
     */
    protected $fillable = ["first_name", "last_name", "email", "password", "avatar", "firebase_token"];


    /**
     * @description   Versteckte Spalten, wenn wir eine Instanz des Benutzers abrufen
     */
    protected $hidden = array(
        'password',
        'updated_at',
    );

    /**
     * @description     Gibt den vollständigen Namen des Kontakts zurück, basierend auf einer Verknüpfung des Vornamens (first_name) und Nachnamens (last_name).
     * @return string;
     */
    public function getFullNameAttribute() {
        return $this->first_name . " " . $this->last_name;
    }
}
