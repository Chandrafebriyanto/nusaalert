<?php

namespace App\Http\Controllers;

use App\Models\Bencana;
use App\Models\Alert;
use App\Models\Laporan;
use App\Services\BmkgService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(BmkgService $bmkg)
    {
        $user = Auth::user();

        // User's locations
        $lokasi = $user->lokasi()->get();
        $lokasiAktif = $lokasi->where('is_active', true);

        // Latest alerts for this user
        $alertsTerbaru = Alert::where('user_id', $user->id)
            ->with('bencana')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Stats
        $totalLokasi = $lokasi->count();
        $totalAlerts = Alert::where('user_id', $user->id)->count();
        $unreadAlerts = Alert::where('user_id', $user->id)->where('status', 'sent')->count();

        // Nearby active disasters
        $bencanaAktif = Bencana::where('terjadi_pada', '>=', now()->subDays(7))
            ->orderBy('terjadi_pada', 'desc')
            ->limit(20)
            ->get();

        // Determine overall status for user
        $statusArea = 'AMAN';
        $statusColor = 'tertiary';
        if ($unreadAlerts > 0) {
            $latestAlert = $alertsTerbaru->first();
            if ($latestAlert && $latestAlert->bencana) {
                $mag = $latestAlert->bencana->magnitude;
                if ($mag >= 6) {
                    $statusArea = 'AWAS';
                    $statusColor = 'primary';
                } elseif ($mag >= 4) {
                    $statusArea = 'WASPADA';
                    $statusColor = 'alert-siaga';
                } else {
                    $statusArea = 'SIAGA';
                    $statusColor = 'alert-waspada';
                }
            }
        }

        return view('dashboard', compact(
            'lokasi', 'lokasiAktif', 'alertsTerbaru',
            'totalLokasi', 'totalAlerts', 'unreadAlerts',
            'bencanaAktif', 'statusArea', 'statusColor'
        ));
    }
}
