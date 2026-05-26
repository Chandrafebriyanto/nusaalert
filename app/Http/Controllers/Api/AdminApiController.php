<?php

namespace App\Http\Controllers\Api;

use OpenApi\Annotations as OA;

use App\Http\Controllers\Controller;
use App\Models\Bencana;
use App\Models\Alert;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\User;
use App\Jobs\SendDisasterAlertJob;
use App\Services\BmkgService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="Endpoint admin: statistik, manajemen laporan, user, bencana"
 * )
 */
class AdminApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/admin/dashboard",
     *     summary="Statistik dashboard admin",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data statistik admin",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Akses admin diperlukan")
     * )
     */
    public function dashboard()
    {
        $user = Auth::user();
        abort_unless($user->hasRole('admin'), 403, 'Akses admin diperlukan.');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_users' => User::count(),
                'total_bencana' => Bencana::count(),
                'total_alerts' => Alert::count(),
                'total_laporan' => Laporan::count(),
                'laporan_pending' => Laporan::where('status', 'pending')->count(),
                'bencana_hari_ini' => Bencana::whereDate('terjadi_pada', today())->count(),
                'latest_bencana' => Bencana::orderBy('terjadi_pada', 'desc')->limit(5)->get(),
                'latest_laporan_pending' => Laporan::where('status', 'pending')
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
            ],
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/admin/laporan/{id}/verify",
     *     summary="Verifikasi laporan komunitas",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Laporan berhasil diverifikasi"),
     *     @OA\Response(response=403, description="Akses admin diperlukan")
     * )
     */
    public function verifyLaporan(Laporan $laporan)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403, 'Akses admin diperlukan.');

        $laporan->update(['status' => 'verified']);

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil diverifikasi.',
            'data' => $laporan->fresh()->load('user'),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/laporan/{id}/reject",
     *     summary="Tolak dan hapus laporan komunitas",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Laporan berhasil ditolak"),
     *     @OA\Response(response=403, description="Akses admin diperlukan")
     * )
     */
    public function rejectLaporan(Laporan $laporan)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403, 'Akses admin diperlukan.');

        $laporan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil ditolak dan dihapus.',
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/admin/users/{id}/role",
     *     summary="Update role user",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role"},
     *             @OA\Property(property="role", type="string", example="member")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Role user berhasil diperbarui"),
     *     @OA\Response(response=403, description="Akses admin diperlukan")
     * )
     */
    public function updateRole(Request $request, User $user)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403, 'Akses admin diperlukan.');

        $request->validate(['role' => 'required|string|exists:roles,name']);
        $user->syncRoles([$request->role]);

        return response()->json([
            'status' => 'success',
            'message' => "Role user {$user->name} diperbarui ke {$request->role}.",
            'data' => [
                'user' => $user->fresh()->load('roles'),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/bencana",
     *     summary="Tambah data bencana manual",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"jenis_bencana","latitude","longitude","wilayah"},
     *             @OA\Property(property="jenis_bencana", type="string", enum={"gempa","tsunami","banjir","cuaca_ekstrem","gunung_api","tanah_longsor"}),
     *             @OA\Property(property="magnitude", type="number", format="float", example=5.2),
     *             @OA\Property(property="kedalaman_km", type="number", format="float", example=10),
     *             @OA\Property(property="latitude", type="number", format="float", example=-6.2088),
     *             @OA\Property(property="longitude", type="number", format="float", example=106.8456),
     *             @OA\Property(property="wilayah", type="string", example="Jakarta Selatan"),
     *             @OA\Property(property="deskripsi", type="string", example="Gempa terasa kuat")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Bencana berhasil ditambahkan"),
     *     @OA\Response(response=403, description="Akses admin diperlukan")
     * )
     */
    public function storeBencana(Request $request)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403, 'Akses admin diperlukan.');

        $request->validate([
            'jenis_bencana' => 'required|string|in:gempa,tsunami,banjir,cuaca_ekstrem,gunung_api,tanah_longsor',
            'magnitude' => 'nullable|numeric|min:0|max:10',
            'kedalaman_km' => 'nullable|numeric|min:0',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'wilayah' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
        ]);

        $eventId = 'manual-' . Str::uuid()->toString();

        $bencana = Bencana::create([
            'user_id' => Auth::id(),
            'event_id' => $eventId,
            'jenis_bencana' => $request->jenis_bencana,
            'magnitude' => $request->magnitude,
            'kedalaman_km' => $request->kedalaman_km,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'wilayah' => $request->wilayah,
            'sumber_api' => 'manual_admin',
            'raw_data' => [
                'source' => 'manual',
                'admin' => Auth::user()->name,
                'deskripsi' => $request->deskripsi,
                'created_at' => now()->toISOString(),
            ],
            'terjadi_pada' => now(),
        ]);

        // Auto-generate alerts for affected users
        $alertCount = 0;
        $lokasiAktif = Lokasi::where('is_active', true)->get();

        foreach ($lokasiAktif as $lokasi) {
            $jarak = BmkgService::haversineDistance(
                (float) $bencana->latitude, (float) $bencana->longitude,
                (float) $lokasi->latitude, (float) $lokasi->longitude
            );

            if ($jarak <= $lokasi->radius_km) {
                $alert = Alert::create([
                    'user_id' => $lokasi->user_id,
                    'bencana_id' => $bencana->id,
                    'lokasi_id' => $lokasi->id,
                    'jarak_km' => round($jarak, 2),
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                SendDisasterAlertJob::dispatch($alert);
                $alertCount++;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Bencana berhasil ditambahkan. {$alertCount} alert dikirim ke pengguna terdekat.",
            'data' => $bencana,
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/bencana/{id}",
     *     summary="Hapus data bencana",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Bencana berhasil dihapus"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function destroyBencana(Bencana $bencana)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403, 'Akses admin diperlukan.');

        $bencana->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data bencana berhasil dihapus.',
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/laporan/{id}",
     *     summary="Hapus laporan komunitas",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Laporan berhasil dihapus"),
     *     @OA\Response(response=403, description="Akses admin diperlukan")
     * )
     */
    public function destroyLaporan(Laporan $laporan)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403, 'Akses admin diperlukan.');

        if ($laporan->foto_url) {
            Storage::disk('public')->delete($laporan->foto_url);
        }

        $laporan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan komunitas berhasil dihapus.',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/users",
     *     summary="Daftar semua user",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Daftar user berhasil diambil")
     * )
     */
    public function users(Request $request)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403, 'Akses admin diperlukan.');

        $users = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $users->items(),
            'meta' => [
                'total' => $users->total(),
                'page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }
}
