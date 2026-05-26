<?php

namespace App\Http\Controllers\Api;

use OpenApi\Annotations as OA;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Lokasi",
 *     description="Manajemen lokasi pantauan user (CRUD)"
 * )
 */
class LokasiApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/lokasi",
     *     summary="Daftar semua lokasi user",
     *     tags={"Lokasi"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar lokasi berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Lokasi"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $lokasi = Auth::user()->lokasi()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $lokasi,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/lokasi",
     *     summary="Tambah lokasi baru",
     *     tags={"Lokasi"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama_lokasi","latitude","longitude","radius_km"},
     *             @OA\Property(property="nama_lokasi", type="string", example="Rumah Utama"),
     *             @OA\Property(property="latitude", type="number", format="float", example=-6.2088),
     *             @OA\Property(property="longitude", type="number", format="float", example=106.8456),
     *             @OA\Property(property="radius_km", type="integer", example=50)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Lokasi berhasil ditambahkan"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_km' => 'required|integer|min:1|max:500',
        ]);

        $lokasi = Auth::user()->lokasi()->create($request->only([
            'nama_lokasi', 'latitude', 'longitude', 'radius_km'
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Lokasi berhasil ditambahkan.',
            'data' => $lokasi,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/lokasi/{id}",
     *     summary="Detail lokasi",
     *     tags={"Lokasi"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detail lokasi"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Lokasi tidak ditemukan")
     * )
     */
    public function show(Lokasi $lokasi)
    {
        abort_unless(Auth::id() === $lokasi->user_id, 403, 'Unauthorized');

        return response()->json([
            'status' => 'success',
            'data' => $lokasi,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/lokasi/{id}",
     *     summary="Update lokasi",
     *     tags={"Lokasi"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_lokasi", type="string"),
     *             @OA\Property(property="latitude", type="number", format="float"),
     *             @OA\Property(property="longitude", type="number", format="float"),
     *             @OA\Property(property="radius_km", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Lokasi berhasil diperbarui"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function update(Request $request, Lokasi $lokasi)
    {
        abort_unless(Auth::id() === $lokasi->user_id, 403, 'Unauthorized');

        $request->validate([
            'nama_lokasi' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'radius_km' => 'sometimes|integer|min:1|max:500',
            'is_active' => 'sometimes|boolean',
        ]);

        $lokasi->update($request->only([
            'nama_lokasi', 'latitude', 'longitude', 'radius_km', 'is_active'
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Lokasi berhasil diperbarui.',
            'data' => $lokasi->fresh(),
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/lokasi/{id}/toggle",
     *     summary="Toggle status aktif lokasi",
     *     tags={"Lokasi"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Status lokasi berhasil diubah"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function toggleActive(Lokasi $lokasi)
    {
        abort_unless(Auth::id() === $lokasi->user_id, 403, 'Unauthorized');

        $lokasi->update(['is_active' => !$lokasi->is_active]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status lokasi diperbarui.',
            'data' => $lokasi->fresh(),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/lokasi/{id}",
     *     summary="Hapus lokasi",
     *     tags={"Lokasi"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Lokasi berhasil dihapus"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function destroy(Lokasi $lokasi)
    {
        abort_unless(Auth::id() === $lokasi->user_id, 403, 'Unauthorized');

        $lokasi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Lokasi berhasil dihapus.',
        ]);
    }
}
