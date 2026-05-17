<?php

namespace App\Http\Controllers;

use App\Models\Bencana;
use App\Models\Alert;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\User;
use App\Jobs\SendDisasterAlertJob;
use App\Services\BmkgService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
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

        return view('admin.index', compact(
            'totalUsers', 'totalBencana', 'totalAlerts', 'totalLaporan',
            'laporanPending', 'latestBencana', 'users', 'roles'
        ));
    }

    public function verifyLaporan(Laporan $laporan)
    {
        $laporan->update(['status' => 'verified']);
        return redirect()->back()->with('success', 'Laporan berhasil diverifikasi.');
    }

    public function rejectLaporan(Laporan $laporan)
    {
        $laporan->delete();
        return redirect()->back()->with('success', 'Laporan berhasil ditolak dan dihapus.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);
        $user->syncRoles([$request->role]);
        return redirect()->back()->with('success', "Role user {$user->name} diperbarui ke {$request->role}.");
    }

    /**
     * Admin manual bencana creation
     * Creates a bencana record and auto-generates alerts for nearby users
     */
    public function storeBencana(Request $request)
    {
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

        return redirect()->route('admin.index')
            ->with('success', "Bencana berhasil ditambahkan ke peta. {$alertCount} alert otomatis dikirim ke pengguna terdekat.");
    }
}
