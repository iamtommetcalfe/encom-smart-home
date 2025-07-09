<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WidgetController;
use App\Http\Controllers\BinCollectionController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SmartHomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Weather Routes
Route::get('/weather/current', [WeatherController::class, 'current']);
Route::get('/weather/forecast', [WeatherController::class, 'forecast']);

// Bin Collection Routes
Route::get('/bin-collections/upcoming', [BinCollectionController::class, 'upcoming']);
Route::get('/bin-collections/next', [BinCollectionController::class, 'nextCollections']);

// Widget Routes
Route::get('/widgets', [WidgetController::class, 'indexApi']);
Route::post('/widgets', [WidgetController::class, 'storeApi']);
Route::get('/widgets/{widget}', [WidgetController::class, 'showApi']);
Route::put('/widgets/{widget}', [WidgetController::class, 'updateApi']);
Route::delete('/widgets/{widget}', [WidgetController::class, 'destroyApi']);
Route::patch('/widgets/{widget}/position', [WidgetController::class, 'updatePosition']);
Route::patch('/widgets/{widget}/size', [WidgetController::class, 'updateSize']);

// Dashboard Data Route
Route::get('/dashboard', [HomeController::class, 'dashboardData']);

// Smart Home Platform Routes
Route::get('/smart-home/platforms', [SmartHomeController::class, 'getPlatforms']);
Route::get('/smart-home/platforms/{id}', [SmartHomeController::class, 'getPlatform']);
Route::post('/smart-home/platforms', [SmartHomeController::class, 'storePlatform']);
Route::put('/smart-home/platforms/{id}', [SmartHomeController::class, 'updatePlatform']);
Route::delete('/smart-home/platforms/{id}', [SmartHomeController::class, 'deletePlatform']);
Route::post('/smart-home/platforms/{id}/connect', [SmartHomeController::class, 'connectAndSyncPlatform']);
Route::post('/smart-home/platforms/{id}/refresh', [SmartHomeController::class, 'refreshPlatformDevices']);

// Smart Home Device Routes
Route::get('/smart-home/devices', [SmartHomeController::class, 'getDevices']);
Route::get('/smart-home/platforms/{platformId}/devices', [SmartHomeController::class, 'getDevicesForPlatform']);
Route::get('/smart-home/devices/{id}', [SmartHomeController::class, 'getDevice']);
Route::post('/smart-home/devices', [SmartHomeController::class, 'storeDevice']);
Route::put('/smart-home/devices/{id}', [SmartHomeController::class, 'updateDevice']);
Route::delete('/smart-home/devices/{id}', [SmartHomeController::class, 'deleteDevice']);
Route::post('/smart-devices/{id}/toggle', [SmartHomeController::class, 'toggleDevice']);

// Smart Device Widget Routes
Route::get('/smart-home/widget-config', [SmartHomeController::class, 'getWidgetConfig']);
Route::post('/smart-home/widget-config', [SmartHomeController::class, 'saveWidgetConfig']);
