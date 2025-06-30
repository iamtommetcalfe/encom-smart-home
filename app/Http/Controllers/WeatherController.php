<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    /**
     * Get the latitude from environment variables.
     *
     * @return float
     */
    protected function getLatitude(): float
    {
        return (float) env('WEATHER_LATITUDE', 52.6833);
    }

    /**
     * Get the longitude from environment variables.
     *
     * @return float
     */
    protected function getLongitude(): float
    {
        return (float) env('WEATHER_LONGITUDE', -1.5333);
    }

    /**
     * Get the location name from environment variables.
     *
     * @return string
     */
    protected function getLocation(): string
    {
        return env('WEATHER_LOCATION', 'Unknown Location');
    }

    /**
     * Get current weather data for the configured location.
     *
     * @return JsonResponse
     */
    public function current(): JsonResponse
    {
        // Cache the weather data for 30 minutes to avoid excessive API calls
        $weatherData = Cache::remember('weather_current', 1800, function () {
            return $this->fetchCurrentWeather();
        });

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

        // Cache the forecast data for 1 hour to avoid excessive API calls
        $forecastData = Cache::remember("weather_forecast_{$days}", 3600, function () use ($days) {
            return $this->fetchForecast($days);
        });

        return response()->json($forecastData);
    }

    /**
     * Fetch current weather data from the Open-Meteo API.
     *
     * @return array
     */
    protected function fetchCurrentWeather(): array
    {
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m,wind_direction_10m',
            'timezone' => 'Europe/London',
        ]);

        if ($response->successful()) {
            $data = $response->json();

            // Map weather code to a human-readable description
            $weatherDescription = $this->getWeatherDescription($data['current']['weather_code'] ?? 0);

            return [
                'current' => [
                    'temperature' => $data['current']['temperature_2m'] ?? null,
                    'temperature_unit' => $data['current_units']['temperature_2m'] ?? '°C',
                    'apparent_temperature' => $data['current']['apparent_temperature'] ?? null,
                    'humidity' => $data['current']['relative_humidity_2m'] ?? null,
                    'precipitation' => $data['current']['precipitation'] ?? null,
                    'weather_code' => $data['current']['weather_code'] ?? null,
                    'weather_description' => $weatherDescription,
                    'wind_speed' => $data['current']['wind_speed_10m'] ?? null,
                    'wind_direction' => $data['current']['wind_direction_10m'] ?? null,
                    'time' => $data['current']['time'] ?? null,
                ],
                'location' => $this->getLocation(),
            ];
        }

        return [
            'error' => 'Failed to fetch weather data',
        ];
    }

    /**
     * Fetch weather forecast data from the Open-Meteo API.
     *
     * @param int $days
     * @return array
     */
    protected function fetchForecast(int $days): array
    {
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum,weather_code',
            'timezone' => 'Europe/London',
            'forecast_days' => $days,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $forecast = [];

            if (isset($data['daily']) && isset($data['daily']['time'])) {
                $count = count($data['daily']['time']);

                for ($i = 0; $i < $count; $i++) {
                    $weatherDescription = $this->getWeatherDescription($data['daily']['weather_code'][$i] ?? 0);

                    $forecast[] = [
                        'date' => $data['daily']['time'][$i] ?? null,
                        'max_temperature' => $data['daily']['temperature_2m_max'][$i] ?? null,
                        'min_temperature' => $data['daily']['temperature_2m_min'][$i] ?? null,
                        'precipitation_sum' => $data['daily']['precipitation_sum'][$i] ?? null,
                        'weather_code' => $data['daily']['weather_code'][$i] ?? null,
                        'weather_description' => $weatherDescription,
                    ];
                }
            }

            return [
                'forecast' => $forecast,
                'location' => $this->getLocation(),
            ];
        }

        return [
            'error' => 'Failed to fetch forecast data',
        ];
    }

    /**
     * Get a human-readable description for a weather code.
     *
     * @param int $code
     * @return string
     */
    protected function getWeatherDescription(int $code): string
    {
        $descriptions = [
            0 => 'Clear Sky',
            1 => 'Mainly Clear',
            2 => 'Partly Cloudy',
            3 => 'Overcast',
            45 => 'Fog',
            48 => 'Depositing Rime Fog',
            51 => 'Light Drizzle',
            53 => 'Moderate Drizzle',
            55 => 'Dense Drizzle',
            56 => 'Light Freezing Drizzle',
            57 => 'Dense Freezing Drizzle',
            61 => 'Slight Rain',
            63 => 'Moderate Rain',
            65 => 'Heavy Rain',
            66 => 'Light Freezing Rain',
            67 => 'Heavy Freezing Rain',
            71 => 'Slight Snow Fall',
            73 => 'Moderate Snow Fall',
            75 => 'Heavy Snow Fall',
            77 => 'Snow Grains',
            80 => 'Slight Rain Showers',
            81 => 'Moderate Rain Showers',
            82 => 'Violent Rain Showers',
            85 => 'Slight Snow Showers',
            86 => 'Heavy Snow Showers',
            95 => 'Thunderstorm',
            96 => 'Thunderstorm with Slight Hail',
            99 => 'Thunderstorm with Heavy Hail',
        ];

        return $descriptions[$code] ?? 'Unknown';
    }
}
