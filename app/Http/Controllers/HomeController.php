<?php

/**
 * @description HomeController class wird nur für den Aufruf der main view verwendet.
 */

namespace App\Http\Controllers;

class HomeController extends Controller {

    /**
     * @description gibt die main view der Website zurück. Wir verwenden SPA, daher geben wir die main Blades zurück.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index() {
        return view("home");
    }
}
