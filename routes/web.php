<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WidgetController;
use App\Http\Controllers\BinCollectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WeatherController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('widgets', WidgetController::class);
Route::patch('widgets/{widget}/position', [WidgetController::class, 'updatePosition'])->name('widgets.update-position');
Route::patch('widgets/{widget}/size', [WidgetController::class, 'updateSize'])->name('widgets.update-size');

// Bin Collection Routes
Route::get('/api/bin-collections/upcoming', [BinCollectionController::class, 'upcoming'])->name('bin-collections.upcoming');
Route::get('/api/bin-collections/next', [BinCollectionController::class, 'nextCollections'])->name('bin-collections.next');

// Weather Routes
Route::get('/api/weather/current', [WeatherController::class, 'current'])->name('weather.current');
Route::get('/api/weather/forecast', [WeatherController::class, 'forecast'])->name('weather.forecast');
