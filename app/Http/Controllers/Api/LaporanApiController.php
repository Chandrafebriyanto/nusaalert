<?php

namespace App\Http\Controllers\Api;

use OpenApi\Annotations as OA;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Laporan Komunitas",
 *     description="Laporan bencana dari komunitas/masyarakat"
 * )
 */
class LaporanApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/laporan",
     *     summary="Daftar laporan komunitas",
     *     tags={"Laporan Komunitas"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string", enum={"pending","verified","rejected"})),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar laporan berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Laporan::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporan = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $laporan->items(),
            'meta' => [
                'total' => $laporan->total(),
                'page' => $laporan->currentPage(),
                'per_page' => $laporan->perPage(),
                'last_page' => $laporan->lastPage(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/laporan",
     *     summary="Kirim laporan bencana baru",
     *     tags={"Laporan Komunitas"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"jenis_bencana","latitude","longitude","deskripsi"},
     *                 @OA\Property(property="jenis_bencana", type="string", example="banjir"),
     *                 @OA\Property(property="latitude", type="number", format="float", example=-6.2088),
     *                 @OA\Property(property="longitude", type="number", format="float", example=106.8456),
     *                 @OA\Property(property="wilayah", type="string", example="Jakarta Selatan"),
     *                 @OA\Property(property="deskripsi", type="string", example="Banjir setinggi 50cm di area perumahan"),
     *                 @OA\Property(property="foto", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Laporan berhasil dikirim"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_bencana' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'wilayah' => 'nullable|string|max:255',
            'deskripsi' => 'required|string|max:2000',
            'foto' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['jenis_bencana', 'latitude', 'longitude', 'wilayah', 'deskripsi']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('foto')) {
            $data['foto_url'] = $request->file('foto')->store('laporan', 'public');
        }

        $laporan = Laporan::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil dikirim. Menunggu verifikasi.',
            'data' => $laporan->load('user'),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/laporan/{id}",
     *     summary="Detail laporan",
     *     tags={"Laporan Komunitas"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detail laporan")
     * )
     */
    public function show(Laporan $laporan)
    {
        return response()->json([
            'status' => 'success',
            'data' => $laporan->load('user'),
        ]);
    }
}
