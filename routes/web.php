<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Log;


Route::get('/{any}', array(HomeController::class, 'index'))->where('any', '^(?!api\/)[\/\w\.-]*');
