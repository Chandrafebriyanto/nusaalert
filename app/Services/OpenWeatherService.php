<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenWeatherService
{
    protected string $baseUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.owm.base_url', 'https://api.openweathermap.org');
        $this->apiKey = config('services.owm.api_key');
    }

    /**
     * Check if the service is configured with an API key
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Get current weather data for given coordinates
     */
    public function getWeatherByCoords(float $lat, float $lng): ?array
    {
        if (!$this->isConfigured()) {
            Log::debug('OpenWeatherMap: API key not configured, skipping weather check.');
            return null;
        }

        $cacheKey = "owm_weather_{$lat}_{$lng}";

        return Cache::remember($cacheKey, 600, function () use ($lat, $lng) {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}/data/2.5/weather", [
                    'lat' => $lat,
                    'lon' => $lng,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lang' => 'id',
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::warning('OWM API error: ' . $response->status() . ' - ' . $response->body());
            } catch (\Exception $e) {
                Log::error('OWM fetch error: ' . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Check if weather conditions at given coordinates are extreme
     * Returns threat info if extreme, null if safe
     */
    public function checkExtremeWeather(float $lat, float $lng): ?array
    {
        $data = $this->getWeatherByCoords($lat, $lng);
        if (!$data) return null;

        $threats = [];

        // Check rainfall (rain.1h > 50mm is very heavy)
        $rain1h = $data['rain']['1h'] ?? 0;
        if ($rain1h > 50) {
            $threats[] = [
                'type' => 'heavy_rain',
                'label' => 'Hujan Sangat Lebat',
                'value' => "{$rain1h} mm/jam",
                'severity' => $rain1h > 100 ? 'awas' : 'siaga',
            ];
        }

        // Check wind speed (> 90 km/h is hurricane force)
        $windSpeed = ($data['wind']['speed'] ?? 0) * 3.6; // m/s to km/h
        if ($windSpeed > 63) { // Severe storm
            $threats[] = [
                'type' => 'strong_wind',
                'label' => 'Angin Kencang',
                'value' => round($windSpeed) . " km/jam",
                'severity' => $windSpeed > 90 ? 'awas' : 'siaga',
            ];
        }

        // Check weather condition codes (thunderstorm = 2xx)
        $weatherId = $data['weather'][0]['id'] ?? 0;
        if ($weatherId >= 200 && $weatherId < 300) {
            $threats[] = [
                'type' => 'thunderstorm',
                'label' => 'Badai Petir',
                'value' => $data['weather'][0]['description'] ?? 'Thunderstorm',
                'severity' => $weatherId <= 210 ? 'waspada' : 'siaga',
            ];
        }

        return !empty($threats) ? [
            'location' => $data['name'] ?? 'Unknown',
            'lat' => $lat,
            'lng' => $lng,
            'threats' => $threats,
            'raw_data' => $data,
        ] : null;
    }
}
