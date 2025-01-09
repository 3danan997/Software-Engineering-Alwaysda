<?php

use App\Http\Controllers\Auth\UserController;

use App\Http\Controllers\Utils\ContactsController;
use App\Http\Controllers\Utils\RemindersController;
use App\Http\Controllers\Utils\StatsController;
use Illuminate\Support\Facades\Route;


/* Nicht authentifizierte Routen */
Route::prefix('user')->controller(UserController::class)->group(function () {
    /* Benutzerdetails als Objekt */
    Route::post('/register', 'registerUser');
    /* Benutzerdetails als Objekt */
    Route::post('/login', 'loginUser');
    /* Tokenroute anfordern */
    Route::post('/token', 'requestToken');
    /* Kennwort über Token-Route zurücksetzen */
    Route::post('/password', 'resetPassword');
});


/* authentifizierte Routen */
Route::group(array('middleware' => array('auth:sanctum')), function () {

    Route::prefix('user')->controller(UserController::class)->group(function () {
        /* Benutzerdetails als Objekt */
        Route::get('/fetch', 'fetchUser');
        /* Benutzerprofil */
        Route::post('/preferences', 'updateUser');
        /* Abmeldung des Benutzers, es könnte zu Nicht-Authentifizierungsrouten hinzugefügt werden. Trotzdem behandeln wir alle Authentifizierungsfehler in unserem Axios-Interceptor, daher ist es nicht erforderlich, ihn zu einer öffentlichen Route zu machen. */
        Route::post('/logout', 'logoutUser');
    });

    Route::prefix('contacts')->controller(ContactsController::class)->group(function () {
        Route::get('/', 'getContacts');
        Route::post('/', 'createContact');
        Route::post('/{id}', 'updateContact');
        Route::delete('/{id}', 'deleteContact');
    });

    Route::prefix('stats')->controller(StatsController::class)->group(function () {
        Route::get('/', 'getStats');
    });

    Route::prefix('reminders')->controller(RemindersController::class)->group(function () {
        Route::get('/today', 'getRemindersToday');
        Route::get('/', 'getReminders');
    });
});
