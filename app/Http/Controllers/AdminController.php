<?php

namespace App\Http\Controllers;

use App\Models\Bencana;
use App\Models\Alert;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\User;
use App\Jobs\SendDisasterAlertJob;
use App\Services\BmkgService;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasRole('admin'), 403, 'Akses admin diperlukan.');
        $totalUsers = User::count();
        $totalBencana = Bencana::count();
        $totalAlerts = Alert::count();
        $totalLaporan = Laporan::count();

        $laporanPending = Laporan::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $latestBencana = Bencana::orderBy('terjadi_pada', 'desc')
            ->limit(10)
            ->get();

        $users = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $roles = Role::all();

        $data = compact(
            'totalUsers', 'totalBencana', 'totalAlerts', 'totalLaporan',
            'laporanPending', 'latestBencana', 'users', 'roles'
        );

        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        }

        return view('admin.index', $data);
    }

    public function verifyLaporan(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);
        $laporan->update(['status' => 'verified']);
        return $this->respondWithSuccessOrBack($request, 'Laporan berhasil diverifikasi.', ['laporan' => $laporan->fresh()]);
    }

    public function rejectLaporan(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasRole('admin'), 403, 'Akses admin diperlukan.');

        $laporan = Laporan::findOrFail($id);

        try {
            if($laporan->foto_url){
                Storage::disk('public')->delete($laporan->foto_url);
            }
            $laporan->delete();
            return $this->respondWithSuccessOrBack($request, 'Laporan berhasil ditolak dan dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error rejecting laporan: ' . $e->getMessage());
            return redirect()->back()->withErrors('Gagal menolak laporan: terjadi kesalahan sistem.');
        }
    }

    public function updateRole(Request $request, $id)
    {
        $userModel = User::findOrFail($id);
        $request->validate(['role' => 'required|string|exists:roles,name']);
        $userModel->syncRoles([$request->role]);
        return $this->respondWithSuccessOrBack($request, "Role user {$userModel->name} diperbarui ke {$request->role}.", ['user' => $userModel->fresh()->load('roles')]);
    }

    /**
     * Admin manual bencana creation
     * Creates a bencana record and auto-generates alerts for nearby users
     */
    public function storeBencana(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasRole('admin'), 403, 'Akses admin diperlukan.');

        $request->validate([
            'jenis_bencana' => 'required|string|in:gempa,tsunami,banjir,cuaca_ekstrem,gunung_api,tanah_longsor',
            'magnitude' => 'nullable|numeric|min:0|max:10',
            'kedalaman_km' => 'nullable|numeric|min:0',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'wilayah' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
        ]);

        // Create unique event ID for manual entry
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

                try {
                    SendDisasterAlertJob::dispatch($alert);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Failed to dispatch alert job for Alert #{$alert->id}: " . $e->getMessage());
                }
                $alertCount++;
            }
        }

        $message = "Bencana berhasil ditambahkan ke peta. {$alertCount} alert otomatis dikirim ke pengguna terdekat.";

        return $this->respondWithSuccessOrRedirect($request, 'admin.index', $message, ['bencana' => $bencana, 'alert_count' => $alertCount], 201);
    }

    public function destroyLaporan(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasRole('admin'), 403, 'Akses admin diperlukan.');

        $laporan = Laporan::findOrFail($id);

        try {
            if($laporan->foto_url){
                Storage::disk('public')->delete($laporan->foto_url);
            }

            $laporan->delete();
            return $this->respondWithSuccessOrBack($request, 'Laporan Komunitas berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error destroying laporan: ' . $e->getMessage());
            return redirect()->back()->withErrors('Gagal menghapus laporan: terjadi kesalahan sistem.');
        }
    }

    public function destroyBencana(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasRole('admin'), 403, 'Akses admin diperlukan.');

        $bencana = Bencana::findOrFail($id);

        // Delete related alerts first to avoid foreign key constraint errors
        $bencana->alerts()->delete();

        $bencana->delete();
        return $this->respondWithSuccessOrBack($request, 'Data Bencana berhasil dihapus.');
    }
}
