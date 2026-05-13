<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BmkgService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.bmkg.base_url', 'https://data.bmkg.go.id/DataMKG/TEWS');
    }

    /**
     * Get latest felt earthquake (autogempa.json)
     */
    public function getGempaTerkini(): ?array
    {
        return Cache::remember('bmkg_gempa_terkini', 300, function () {
            try {
                $response = Http::timeout(10)->get($this->baseUrl . '/autogempa.json');
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['Infogempa']['gempa'] ?? null;
                }
            } catch (\Exception $e) {
                Log::error('BMKG autogempa fetch error: ' . $e->getMessage());
            }
            return null;
        });
    }

    /**
     * Get recent M5.0+ earthquakes
     */
    public function getGempaM5(): array
    {
        return Cache::remember('bmkg_gempa_m5', 300, function () {
            try {
                $response = Http::timeout(10)->get($this->baseUrl . '/gempaterkini.json');
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['Infogempa']['gempa'] ?? [];
                }
            } catch (\Exception $e) {
                Log::error('BMKG gempa M5+ fetch error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Get recent felt earthquakes list (gempadirasakan.json)
     */
    public function getGempaDirasakan(): array
    {
        return Cache::remember('bmkg_gempa_dirasakan', 300, function () {
            try {
                $response = Http::timeout(10)->get($this->baseUrl . '/gempadirasakan.json');
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['Infogempa']['gempa'] ?? [];
                }
            } catch (\Exception $e) {
                Log::error('BMKG gempa dirasakan fetch error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Parse BMKG coordinate format (e.g., "2.95 LS" to -2.95)
     */
    public static function parseLatitude(string $lat): float
    {
        $value = (float) preg_replace('/[^0-9.]/', '', $lat);
        if (str_contains(strtoupper($lat), 'LS')) {
            $value = -$value;
        }
        return $value;
    }

    public static function parseLongitude(string $lon): float
    {
        $value = (float) preg_replace('/[^0-9.]/', '', $lon);
        if (str_contains(strtoupper($lon), 'BB')) {
            $value = -$value;
        }
        return $value;
    }

    /**
     * Parse BMKG DateTime format
     */
    public static function parseDateTime(string $tanggal, string $jam): \Carbon\Carbon
    {
        // Format: "2024-05-14" and "10:45:30 WIB"
        $jamClean = preg_replace('/\s*(WIB|WITA|WIT)\s*$/i', '', $jam);
        return \Carbon\Carbon::parse($tanggal . ' ' . $jamClean, 'Asia/Jakarta');
    }

    /**
     * Calculate Haversine distance between two points (in km)
     */
    public static function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
