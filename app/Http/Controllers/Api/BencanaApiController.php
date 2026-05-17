<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bencana;
use App\Services\BmkgService;
use Illuminate\Http\Request;

class BencanaApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Bencana::orderBy('terjadi_pada', 'desc');

        if ($request->filled('jenis')) {
            $query->where('jenis_bencana', $request->jenis);
        }

        if ($request->filled('days')) {
            $query->where('terjadi_pada', '>=', now()->subDays((int)$request->days));
        }

        if ($request->filled('sumber')) {
            $query->where('sumber_api', $request->sumber);
        }

        if ($request->filled('min_magnitude')) {
            $query->where('magnitude', '>=', (float) $request->min_magnitude);
        }

        $data = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $data->items(),
            'meta' => [
                'total' => $data->total(),
                'page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'last_page' => $data->lastPage(),
            ],
        ]);
    }

    public function show(Bencana $bencana)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $bencana->id,
                'event_id' => $bencana->event_id,
                'jenis_bencana' => $bencana->jenis_bencana,
                'magnitude' => $bencana->magnitude,
                'kedalaman_km' => $bencana->kedalaman_km,
                'latitude' => (float) $bencana->latitude,
                'longitude' => (float) $bencana->longitude,
                'wilayah' => $bencana->wilayah,
                'sumber_api' => $bencana->sumber_api,
                'severity' => $this->getSeverityLevel($bencana),
                'terjadi_pada' => $bencana->terjadi_pada->toISOString(),
                'terjadi_pada_human' => $bencana->terjadi_pada->diffForHumans(),
            ],
        ]);
    }

    public function gempaTerkini(BmkgService $bmkg)
    {
        $data = $bmkg->getGempaTerkini();
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'cached_at' => now()->toISOString(),
        ]);
    }

    public function bencanaAktif(Request $request)
    {
        $days = $request->integer('days', 30);

        $bencana = Bencana::where('terjadi_pada', '>=', now()->subDays($days))
            ->orderBy('terjadi_pada', 'desc')
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'event_id' => $b->event_id,
                'jenis' => $b->jenis_bencana,
                'magnitude' => $b->magnitude,
                'kedalaman' => $b->kedalaman_km,
                'lat' => (float) $b->latitude,
                'lng' => (float) $b->longitude,
                'wilayah' => $b->wilayah,
                'sumber' => $b->sumber_api,
                'severity' => $this->getSeverityLevel($b),
                'terjadi_pada' => $b->terjadi_pada->toISOString(),
                'terjadi_pada_human' => $b->terjadi_pada->diffForHumans(),
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $bencana,
            'total' => $bencana->count(),
        ]);
    }

    /**
     * Find disasters near a given coordinate within a radius
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:500',
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;
        $radius = (float) ($request->radius ?? 100); // Default 100 km
        $days = (int) ($request->days ?? 30);

        $bencana = Bencana::where('terjadi_pada', '>=', now()->subDays($days))
            ->orderBy('terjadi_pada', 'desc')
            ->get()
            ->filter(function ($b) use ($lat, $lng, $radius) {
                $distance = BmkgService::haversineDistance($lat, $lng, (float) $b->latitude, (float) $b->longitude);
                $b->distance_km = $distance;
                return $distance <= $radius;
            })
            ->sortBy('distance_km')
            ->values()
            ->map(fn($b) => [
                'id' => $b->id,
                'jenis' => $b->jenis_bencana,
                'magnitude' => $b->magnitude,
                'lat' => (float) $b->latitude,
                'lng' => (float) $b->longitude,
                'wilayah' => $b->wilayah,
                'severity' => $this->getSeverityLevel($b),
                'distance_km' => round($b->distance_km, 2),
                'terjadi_pada' => $b->terjadi_pada->toISOString(),
                'terjadi_pada_human' => $b->terjadi_pada->diffForHumans(),
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $bencana,
            'query' => [
                'lat' => $lat,
                'lng' => $lng,
                'radius_km' => $radius,
                'days' => $days,
            ],
            'total' => $bencana->count(),
        ]);
    }

    /**
     * Determine severity level based on disaster type and magnitude
     */
    private function getSeverityLevel(Bencana $bencana): string
    {
        if ($bencana->jenis_bencana === 'tsunami') {
            return 'awas';
        }

        if ($bencana->magnitude) {
            if ($bencana->magnitude >= 6) return 'awas';
            if ($bencana->magnitude >= 4) return 'siaga';
            return 'waspada';
        }

        // Non-earthquake types without magnitude
        return 'siaga';
    }
}
