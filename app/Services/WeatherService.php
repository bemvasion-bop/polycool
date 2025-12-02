<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');

        if (!$this->apiKey) {
            return; // allow system to load but skip weather
        }
    }


    /**
     * Main call from ProjectController
     */
    public function getForecast(string $location)
    {
        if (!$location || trim($location) === '') {
            return null;
        }

        $cacheKey = 'forecast_' . md5($location);

        // Cache for 1 hour
        return Cache::remember($cacheKey, 3600, function () use ($location) {

            // Step 1: Convert city → lat/lon
            $coords = $this->geocode($location);
            if (!$coords) return null;

            // Step 2: Get 5-day forecast
            $forecast = $this->fetchForecast($coords['lat'], $coords['lon']);
            if (!$forecast) return null;

            // Step 3: Compute warnings
            $risk = $this->computeRisk($forecast);

            return [
                'location' => $location,
                'forecast' => $forecast,
                'risk'     => $risk,
            ];
        });
    }


    private function geocode($query)
    {
        $url = "https://api.openweathermap.org/geo/1.0/direct";

        $res = Http::get($url, [
            'q'     => $query,
            'limit' => 1,
            'appid' => $this->apiKey
        ]);

        if (!$res->successful() || empty($res[0])) return null;

        return [
            'lat' => $res[0]['lat'],
            'lon' => $res[0]['lon'],
        ];
    }


    private function fetchForecast($lat, $lon)
    {
        $url = "https://api.openweathermap.org/data/2.5/forecast";

        $res = Http::get($url, [
            'lat'   => $lat,
            'lon'   => $lon,
            'appid' => $this->apiKey,
            'units' => 'metric'
        ]);

        if (!$res->successful()) return null;

        $data = $res->json();

        // OpenWeather returns 40 entries (3-hour increments)
        $daily = [];
        foreach ($data['list'] as $entry) {

            $date = substr($entry['dt_txt'], 0, 10);
            if (!isset($daily[$date])) {

                $daily[$date] = [
                    'date'      => $date,
                    'temp_min'  => round($entry['main']['temp_min']),
                    'temp_max'  => round($entry['main']['temp_max']),
                    'condition' => $entry['weather'][0]['description'],
                    'icon'      => $entry['weather'][0]['icon'],
                    'rain'      => isset($entry['rain']['3h']) ? 1 : 0,
                ];
            }
        }

        // Only 5 days
        return array_slice(array_values($daily), 0, 5);
    }


    private function computeRisk($forecast)
    {
        $rainDays = collect($forecast)->where('rain', 1)->count();

        if ($rainDays >= 3) return 'high';
        if ($rainDays == 2) return 'moderate';

        return 'low';
    }
}
