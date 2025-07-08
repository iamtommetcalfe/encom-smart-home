<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpaController;
use App\Http\Controllers\AlexaAuthController;

// Alexa OAuth routes
Route::get('/auth/alexa', [AlexaAuthController::class, 'redirectToAmazon'])->name('auth.alexa');
Route::get('/auth/alexa/callback', [AlexaAuthController::class, 'handleAmazonCallback'])->name('auth.alexa.callback');
Route::post('/auth/alexa/disconnect', [AlexaAuthController::class, 'disconnect'])->name('auth.alexa.disconnect');

// Serve the SPA for all routes
Route::get('/{any?}', [SpaController::class, 'index'])
    ->where('any', '.*')
    ->name('spa');
