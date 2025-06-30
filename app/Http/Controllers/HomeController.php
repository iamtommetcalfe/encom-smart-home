<?php

namespace App\Http\Controllers;

use App\Services\BinCollectionService;
use App\Services\WeatherService;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        return view('welcome');
    }

    /**
     * Get dashboard data for the SPA.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardData()
    {
        // Get bin collection data
        $binCollectionService = app(BinCollectionService::class);
        $nextCollections = $binCollectionService->getNextCollections();

        // Get weather data
        $weatherService = app(WeatherService::class);
        $currentWeather = $weatherService->getCurrentWeather();
        $forecastWeather = $weatherService->getForecast(4);

        return response()->json([
            'binCollections' => $nextCollections,
            'currentWeather' => $currentWeather,
            'forecastWeather' => $forecastWeather,
        ]);
    }
}
