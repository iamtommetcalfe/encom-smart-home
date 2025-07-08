<?php

namespace App\Http\Controllers;

use App\Services\BinCollectionService;
use App\Services\WeatherService;
use App\Services\SmartHomeService;
use App\Services\SmartHomeWidgetService;
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
     * The smart home service instance.
     *
     * @var SmartHomeService
     */
    protected $smartHomeService;

    /**
     * The smart home widget service instance.
     *
     * @var SmartHomeWidgetService
     */
    protected $smartHomeWidgetService;

    /**
     * Create a new controller instance.
     *
     * @param BinCollectionService $binCollectionService
     * @param WeatherService $weatherService
     * @param SmartHomeService $smartHomeService
     * @param SmartHomeWidgetService $smartHomeWidgetService
     * @return void
     */
    public function __construct(
        BinCollectionService $binCollectionService,
        WeatherService $weatherService,
        SmartHomeService $smartHomeService,
        SmartHomeWidgetService $smartHomeWidgetService
    ) {
        $this->binCollectionService = $binCollectionService;
        $this->weatherService = $weatherService;
        $this->smartHomeService = $smartHomeService;
        $this->smartHomeWidgetService = $smartHomeWidgetService;
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

        // Get smart devices for the widget
        $widgetConfigs = $this->smartHomeWidgetService->getWidgetConfigs();
        $widgetConfig = $widgetConfigs->first();
        $smartDevices = [];

        if ($widgetConfig) {
            $smartDevices = $this->smartHomeWidgetService->getDevicesForWidget($widgetConfig->id);
        }

        return response()->json([
            'binCollections' => $nextCollections,
            'currentWeather' => $currentWeather,
            'forecastWeather' => $forecastWeather,
            'smartDevices' => $smartDevices,
        ]);
    }
}
