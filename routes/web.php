<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpaController;

// Serve the SPA for all routes
Route::get('/{any?}', [SpaController::class, 'index'])
    ->where('any', '.*')
    ->name('spa');
