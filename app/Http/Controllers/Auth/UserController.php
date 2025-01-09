<?php

/**
 * @description    UserController class. Hilft bei verwandten Aktionen bezüglich des User model.
 */


namespace App\Http\Controllers\Auth;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\JobEmail;
use App\Models\User\PasswordReset;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller {

    /**
     * @description   Registrierung des Benutzers.
     * @param $request, Instanz des Request hat die Methode aufgerufen  (Besitzt die Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function registerUser(Request $request) {

        // Validiert  die erforderlichen, obligatorischen Spalten
        $v = Validator::make($request->all(), array(
            'first_name' => 'required',
            'last_name' => 'required',
            // unique:users bedeutet, dass die E-Mail basierend auf der Tabelle „users“ eindeutig sein muss und die Spalte in diesem Fall „email“ wäre, mit der abgeglichen werden soll
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ));

        // Validator hat die fehlende erforderliche Werte gefunden
        if ($v->fails()) {
            return Helper::getValidatorMessage($v);
        }

        // Anforderung des Passworts prüfen (Mindestlänge)

        if(strlen($request->password) <= Helper::PASSWORD_MIN_LENGTH) {
            return response()->json(array('status' => 'failure', 'message' => "Das Passwort ist zu kurz! Bitte geben Sie mindestens 8 Zeichen ein..."), 500);
        }

        // Erstellung eines neue Instanz des Benutzers
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->saveOrFail();

        // Rückgabe der Ergebnisse
        return response()->json(array('status' => 'success', 'message' => "Das Konto wurde erstellt."), 200);
    }

    /**
     * @description    Anmeldung des Benutzer
     * @param $request, Instanz des Request hat die Methode aufgerufen  (Besitzt die Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function loginUser(Request $request) {
        $v = Validator::make($request->all(), array(
            'email' => 'required',
            'password' => 'required',
        ));

        if ($v->fails()) {
            return Helper::getValidatorMessage($v);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json(array('status' => 'success', 'message' => "Benutzer ist angemeldet!"), 200);
        }

        return response()->json(array('status' => 'failure', 'message' => "Die Logindaten sind falsch."), 500);
    }

    /**
     * @description    Token Anfrage des Benutzer
     * @param $request, Instanz des Request hat die Methode aufgerufen  (Besitzt die Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function requestToken(Request $request) {
        $v = Validator::make($request->all(), array(
            'email' => 'required',
        ));

        if ($v->fails()) {
            return Helper::getValidatorMessage($v);
        }

        /* Checkt ob die Email existiert*/
        $user = User::where("email", $request->email)->first();
        if (empty($user) === true) {
            return response()->json(array('status' => 'success', 'message' => "Das Token wurde an die angegebene E-Mail geschickt!"), 200);
        }

        try {
            DB::transaction(function () use ($request, $user) {
                /* frühere Token stornieren */
                PasswordReset::where("user_id", $user->id)->update(["used_at" => Carbon::now()]);

                $passwordReset = new PasswordReset();
                $passwordReset->user_id = $user->id;
                $passwordReset->token = bin2hex(random_bytes(16));
                $passwordReset->used_at = null;
                $passwordReset->saveOrFail();

                $this->dispatch(new JobEmail($user->id, "request_token", $passwordReset->id));
            });
        } catch (\Throwable $e) {
            return response()->json(array("status" => "failure", "message" => "Das Token könnte nicht erstellt werden. Grund: " . $e->getMessage()), 500);
        }

        return response()->json(array('status' => 'success', 'message' => "Das Token wurde an die angegebene E-Mail geschickt!"), 200);
    }

    /**
     * @description    Passwort zurücksetzen.
     * @param $request, Instanz des Request hat die Methode aufgerufen  (Besitzt die Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function resetPassword(Request $request) {
        $v = Validator::make($request->all(), array(
            'email' => 'required',
            'token' => 'required',
            'password' => 'required',
        ));

        if ($v->fails()) {
            return Helper::getValidatorMessage($v);
        }

        /* Checkt ob die Email existiert*/
        $user = User::where("email", $request->email)->first();
        if (empty($user) === true) {
            return response()->json(array('status' => 'failure', 'message' => "Die Logindaten sind falsch!"), 500);
        }

        $passwordReset = PasswordReset::where("token", $request->token)->where("user_id", $user->id)->where("used_at", null)->count();
        if($passwordReset > 0) {
            if(strlen($request->password) <= Helper::PASSWORD_MIN_LENGTH) {
                return response()->json(array('status' => 'failure', 'message' => "Das Passwort ist zu kurz! Bitte geben Sie mindestens 8 Zeichen ein.."), 500);
            }
            $user->password = bcrypt($request->password);
            $user->saveOrFail();
            PasswordReset::where("token", $request->token)->where("user_id", $user->id)->where("used_at", null)->update(["used_at" => Carbon::now()]);
        } else {
            return response()->json(array('status' => 'failure', 'message' => "Entweder wurde das Token schon verwendet oder das Token stimmt nicht mit unserem Eintrag überein. Bitte die Webseite erneut aufrufen und das Token anfragen."), 500);
        }


        return response()->json(array('status' => 'success', 'message' => "Das Passwort wurde geändert!"), 200);
    }

    /**
     * @description    Aktualisierung des angemeldeten Benutzers.
     * @param $request, Instanz des Request hat die Methode aufgerufen  (Besitzt die Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function updateUser(Request $request) {
        $user = User::where('id', Auth::id())->first();
        if (empty($user) === true) {
            return response()->json(array(
                'status' => 'error',
                'message' => "Benutzer konnte nicht gefunden werden!"
            ), 500);
        }

        $avatar_changed = ($request->avatar_changed === "true");
        if ($avatar_changed === true) {
            if($request->file("avatar") !== null) {

                if(empty($user->avatar) === false) {
                    Storage::disk("public")->delete("avatars" ."/" . $user->avatar);
                }

                $array = explode('.', $request->file("avatar")->getClientOriginalName());
                $ext = end($array);

                $avatar = Str::random(32) . "." . $ext;
                $result = $request->file("avatar")->storeAs("avatars", $avatar, "public");
                if (empty($result) === false) {
                    $user->avatar = $avatar;
                }
            } else {
                if(empty($user->avatar) === false) {
                    Storage::disk("public")->delete("avatars" ."/" . $user->avatar);
                    $user->avatar = null;
                }
            }
        }

        if (empty($request->password) === false) {
            if(strlen($request->password) <= Helper::PASSWORD_MIN_LENGTH) {
                return response()->json(array('status' => 'failure', 'message' => "Das Passwort ist zu kurz! Bitte geben Sie mindestens 8 Zeichen ein..."), 500);
            }
            $user->password = bcrypt($request->password);
        }

        if (empty($request->first_name) === false) {
            $user->first_name = $request->first_name;
        }

        if (empty($request->last_name) === false) {
            $user->last_name = $request->last_name;
        }

        $user->saveOrFail();

        return response()->json(array(
            'status' => 'success',
            'message' => "Ihr Profil wurde bearbeitet!",
            "user" => $user
        ), 200);
    }

    /**
     * @description    abmelden des authentifizierten Benutzers.
     * @param $request, Instanz des Request hat die Methode aufgerufen  (Besitzt die Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function logoutUser(Request $request) {
        if(!Auth::hasUser()) {
            return response()->json(array(
                'status' => 'failure'
            ), 401);
        }

        // Löscht die Token des Benutzers
        // Wir verwenden Auth::user(), weil es der angemeldete Benutzer ist, der diese Abmeldeanforderung anfordert
        Auth::user()->tokens()->delete();
        // Logout it from the guard `web` which access the API
        Auth::guard('web')->logout();
        // Sitzungstoken entfernen
        if($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        return response()->json(array(
            'status' => 'success'
        ), 200);
    }

    /**
     * @description    Ruft den angemeldeten Benutzer auf.
     * @param $request, Instanz des Request hat die Methode aufgerufen  (Besitzt die Benutzerinformationen).
     * @return \Illuminate\Http\JsonResponse;
     */
    public function fetchUser(Request $request) {
        // Benutzer abrufen
        $user = User::find(Auth::id());
        // Setzt den Benutzer in der Auth Klasse ein
        Auth::setUser($user);

        // Prüfen ob wir einen firebase_token in der Anfrage haben
        // Wenn ja, aktualisieren wir es in der Datenbank
        // firebase_token wird verwendet, um Benachrichtigungen an den Browser/Android-Client zu senden
        if(empty($request->firebase_token) === false) {
            User::where("id", Auth::id())->update(["firebase_token" => $request->firebase_token]);
        }

        // den Benutzer in einer json-Antwort zurückgeben
        return response()->json(array("user" => $user), 200);
    }
}
