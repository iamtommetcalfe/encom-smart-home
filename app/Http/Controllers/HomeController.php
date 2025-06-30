<?php

namespace App\Http\Controllers;

use App\Models\BinCollection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

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
        // Get the next collection for each bin type
        $binTypes = BinCollection::select('bin_type')
            ->distinct()
            ->pluck('bin_type');

        $nextCollections = [];

        foreach ($binTypes as $binType) {
            $collection = BinCollection::nextCollectionForType($binType);

            if ($collection) {
                $nextCollections[] = [
                    'id' => $collection->id,
                    'collection_date' => $collection->collection_date->format('Y-m-d'),
                    'bin_type' => $collection->bin_type,
                    'color' => $collection->color,
                    'days_until' => $collection->daysUntilCollection(),
                    'days_until_human' => $collection->daysUntilCollectionForHumans(),
                ];
            }
        }

        // Sort by days until collection
        usort($nextCollections, function ($a, $b) {
            return $a['days_until'] - $b['days_until'];
        });

        // Get weather data
        $weatherController = new WeatherController();
        $currentWeather = $weatherController->current()->getData(true);
        $forecastWeather = $weatherController->forecast(new Request(['days' => 4]))->getData(true);

        return response()->json([
            'binCollections' => $nextCollections,
            'currentWeather' => $currentWeather,
            'forecastWeather' => $forecastWeather,
        ]);
    }
}
