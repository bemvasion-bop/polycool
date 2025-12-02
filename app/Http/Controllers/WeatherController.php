<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    /**
     * Fetch 5-day weather forecast based on project location
     */
    public function getForecast($location)
    {
        if (!$location) {
            return null;
        }

        // Cache key (per location)
        $cacheKey = 'weather_' . md5($location);

        // Cache for 30 minutes
        return Cache::remember($cacheKey, 1800, function () use ($location) {

            // Step 1: Convert location to latitude/longitude
            $geo = $this->geocode($location);
            if (!$geo) return null;

            // Step 2: Fetch weather from OpenWeather
            $weather = $this->fetchOpenWeather($geo['lat'], $geo['lon']);
            if (!$weather) return null;

            // Step 3: Fetch PAGASA alerts
            $pagasaAlerts = $this->fetchPagasaAlerts($location);

            // Step 4: Merge
            foreach ($weather['daily'] as $i => $day) {
                $weather['daily'][$i]['pagasa_alert'] = $pagasaAlerts[$day['date']] ?? null;
            }

            return $weather;
        });
    }

    /**
     * Geocode (convert "Davao City" into lat/lon)
     */
    private function geocode($location)
    {
        $apiKey = env('OPENWEATHER_KEY');

        $response = Http::get("http://api.openweathermap.org/geo/1.0/direct", [
            'q' => $location,
            'limit' => 1,
            'appid' => $apiKey,
        ]);

        if ($response->failed()) return null;
        if (!isset($response[0])) return null;

        return [
            'lat' => $response[0]['lat'],
            'lon' => $response[0]['lon'],
        ];
    }

    /**
     * Fetch forecast from OpenWeather One Call API
     */
    private function fetchOpenWeather($lat, $lon)
    {
        $apiKey = env('OPENWEATHER_KEY');

        $response = Http::get("https://api.openweathermap.org/data/2.5/onecall", [
            'lat' => $lat,
            'lon' => $lon,
            'exclude' => 'minutely,hourly,current,alerts',
            'appid' => $apiKey,
            'units' => 'metric'
        ]);

        if ($response->failed()) return null;

        $data = $response->json();

        // Convert 7-day into 5-day forecast
        $daily = [];
        foreach (array_slice($data['daily'], 0, 5) as $day) {
            $daily[] = [
                'date'        => date('Y-m-d', $day['dt']),
                'temp_max'    => $day['temp']['max'],
                'temp_min'    => $day['temp']['min'],
                'icon'        => $day['weather'][0]['icon'],
                'description' => $day['weather'][0]['description'],
                'rain_chance' => ($day['pop'] ?? 0) * 100, // probability of precipitation %
            ];
        }

        return ['daily' => $daily];
    }

    /**
     * Fetch PAGASA Rainfall / Tropical Cyclone Alerts
     *
     * NOTE:
     * PAGASA does not have a perfect free API.
     * We use their JSON endpoints where possible.
     */
    private function fetchPagasaAlerts($location)
    {
        try {
            // Example using PAGASA Rainfall Warning JSON (public)
            $response = Http::get("https://bagong.pagasa.dost.gov.ph/api/sev/rainfall");

            if ($response->failed()) return [];

            $alerts = $response->json()['data'] ?? [];

            $output = [];

            foreach ($alerts as $alert) {
                // Match alert to location (fuzzy match)
                if (stripos($alert['location'], $location) !== false) {

                    $color = strtoupper($alert['color'] ?? '');

                    $warning = match ($color) {
                        'RED'    => 'Red Warning (Serious Flooding)',
                        'ORANGE' => 'Orange Warning (Flooding Threat)',
                        'YELLOW' => 'Yellow Warning (Heavy Rain)',
                        default  => null
                    };

                    if ($warning) {
                        // Apply alert to **all upcoming days**
                        for ($i = 0; $i < 5; $i++) {
                            $date = now()->addDays($i)->format('Y-m-d');
                            $output[$date] = $warning;
                        }
                    }
                }
            }

            return $output;
        } catch (\Exception $e) {
            return [];
        }
    }
}
