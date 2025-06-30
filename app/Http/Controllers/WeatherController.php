<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    /**
     * The weather service instance.
     *
     * @var WeatherService
     */
    protected $weatherService;

    /**
     * Create a new controller instance.
     *
     * @param WeatherService $weatherService
     * @return void
     */
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Get current weather data for the configured location.
     *
     * @return JsonResponse
     */
    public function current(): JsonResponse
    {
        $weatherData = $this->weatherService->getCurrentWeather();
        return response()->json($weatherData);
    }

    /**
     * Get weather forecast data for the configured location.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forecast(Request $request): JsonResponse
    {
        $days = $request->input('days', 7);
        $forecastData = $this->weatherService->getForecast($days);
        return response()->json($forecastData);
    }
}
