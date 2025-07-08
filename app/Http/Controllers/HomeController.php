<?php

namespace App\Http\Controllers;

use App\Services\BinCollectionService;
use App\Services\WeatherService;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * The bin collection service instance.
     *
     * @var BinCollectionService
     */
    protected $binCollectionService;

    /**
     * The weather service instance.
     *
     * @var WeatherService
     */
    protected $weatherService;

    /**
     * Create a new controller instance.
     *
     * @param BinCollectionService $binCollectionService
     * @param WeatherService $weatherService
     * @return void
     */
    public function __construct(BinCollectionService $binCollectionService, WeatherService $weatherService)
    {
        $this->binCollectionService = $binCollectionService;
        $this->weatherService = $weatherService;
    }

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
     * @return JsonResponse
     */
    public function dashboardData(): JsonResponse
    {
        // Get bin collection data
        $nextCollections = $this->binCollectionService->getNextCollections();

        // Get weather data
        $currentWeather = $this->weatherService->getCurrentWeather();
        $forecastWeather = $this->weatherService->getForecast(4);

        return response()->json([
            'binCollections' => $nextCollections,
            'currentWeather' => $currentWeather,
            'forecastWeather' => $forecastWeather,
        ]);
    }
}
