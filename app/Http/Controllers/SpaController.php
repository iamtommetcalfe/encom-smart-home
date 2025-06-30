<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SpaController extends Controller
{
    /**
     * Serve the Single Page Application.
     *
     * @return View
     */
    public function index(): View
    {
        return view('spa');
    }
}
