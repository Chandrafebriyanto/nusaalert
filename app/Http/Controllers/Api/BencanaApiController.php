<?php

namespace App\Http\Controllers\Api;

use OpenApi\Annotations as OA;

use App\Http\Controllers\Controller;
use App\Models\Bencana;
use App\Services\BmkgService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Bencana",
 *     description="Data bencana publik: daftar, detail, nearby, gempa BMKG terkini, peta bencana aktif"
 * )
 */
class BencanaApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/bencana",
     *     summary="Daftar semua bencana (paginated)",
     *     tags={"Bencana"},
     *     @OA\Parameter(name="jenis", in="query", required=false, @OA\Schema(type="string"), description="Filter jenis bencana: gempa, tsunami, banjir, cuaca_ekstrem, dll"),
     *     @OA\Parameter(name="days", in="query", required=false, @OA\Schema(type="integer"), description="Filter bencana dalam N hari terakhir"),
     *     @OA\Parameter(name="sumber", in="query", required=false, @OA\Schema(type="string"), description="Filter sumber data: bmkg, manual_admin, komunitas"),
     *     @OA\Parameter(name="min_magnitude", in="query", required=false, @OA\Schema(type="number"), description="Filter minimum magnitude"),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=20), description="Jumlah data per halaman"),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar bencana berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Bencana")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/bencana/{id}",
     *     summary="Detail bencana berdasarkan ID",
     *     tags={"Bencana"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Detail bencana",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Bencana")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Bencana tidak ditemukan")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/gempa/terkini",
     *     summary="Data gempa terkini langsung dari BMKG API",
     *     tags={"Bencana"},
     *     @OA\Response(
     *         response=200,
     *         description="Data gempa terkini dari BMKG",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="cached_at", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function gempaTerkini(BmkgService $bmkg)
    {
        $data = $bmkg->getGempaTerkini();
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'cached_at' => now()->toISOString(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/peta/bencana-aktif",
     *     summary="Bencana aktif untuk peta (N hari terakhir)",
     *     tags={"Bencana"},
     *     @OA\Parameter(name="days", in="query", required=false, @OA\Schema(type="integer", default=30), description="Periode hari"),
     *     @OA\Response(
     *         response=200,
     *         description="Data bencana aktif untuk peta",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
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
     * @OA\Get(
     *     path="/api/v1/bencana/nearby",
     *     summary="Cari bencana terdekat dari koordinat",
     *     tags={"Bencana"},
     *     @OA\Parameter(name="lat", in="query", required=true, @OA\Schema(type="number", format="float"), description="Latitude"),
     *     @OA\Parameter(name="lng", in="query", required=true, @OA\Schema(type="number", format="float"), description="Longitude"),
     *     @OA\Parameter(name="radius", in="query", required=false, @OA\Schema(type="number", default=100), description="Radius pencarian (km)"),
     *     @OA\Parameter(name="days", in="query", required=false, @OA\Schema(type="integer", default=30), description="Periode hari"),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar bencana terdekat",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="query", type="object"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
