<?php

namespace App\Http\Controllers;

use App\Models\Bencana;
use App\Models\Alert;
use App\Models\Laporan;
use App\Services\BmkgService;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request, BmkgService $bmkg)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // User's locations
        $lokasi = $user->lokasi()->get();
        $lokasiAktif = $lokasi->where('is_active', true)->values();

        // Latest alerts for this user
        $alertsTerbaru = Alert::where('user_id', $user->id)
            ->with(['bencana', 'lokasi'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Stats
        $totalLokasi = $lokasi->count();
        $totalAlerts = Alert::where('user_id', $user->id)->count();
        $unreadAlerts = Alert::where('user_id', $user->id)->where('status', 'sent')->count();

        // Nearby active disasters (last 7 days)
        $bencanaAktif = Bencana::where('terjadi_pada', '>=', now()->subDays(7))
            ->orderBy('terjadi_pada', 'desc')
            ->limit(20)
            ->get();

        // Determine overall status for user - REAL-TIME proximity check
        $statusArea = 'AMAN';
        $statusColor = 'tertiary';

        // First check existing unread alerts
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

        // Additionally, do real-time proximity check against ALL active bencana
        // This catches cases where PollBmkg hasn't run yet but bencana data exists
        if ($statusArea === 'AMAN' && $lokasiAktif->count() > 0 && $bencanaAktif->count() > 0) {
            $closestBencana = null;
            $closestDistance = PHP_FLOAT_MAX;

            foreach ($bencanaAktif as $bencana) {
                if (!$bencana->latitude || !$bencana->longitude) continue;

                foreach ($lokasiAktif as $loc) {
                    $jarak = BmkgService::haversineDistance(
                        (float)$bencana->latitude, (float)$bencana->longitude,
                        (float)$loc->latitude, (float)$loc->longitude
                    );

                    if ($jarak <= $loc->radius_km && $jarak < $closestDistance) {
                        $closestDistance = $jarak;
                        $closestBencana = $bencana;
                    }
                }
            }

            if ($closestBencana) {
                $mag = $closestBencana->magnitude ?? 0;
                if ($mag >= 6) {
                    $statusArea = 'AWAS';
                    $statusColor = 'primary';
                } elseif ($mag >= 4) {
                    $statusArea = 'WASPADA';
                    $statusColor = 'alert-siaga';
                } elseif ($mag > 0) {
                    $statusArea = 'SIAGA';
                    $statusColor = 'alert-waspada';
                } else {
                    // Non-earthquake disaster (banjir, etc.)
                    $statusArea = 'SIAGA';
                    $statusColor = 'alert-waspada';
                }
            }
        }

        $data = compact(
            'lokasi', 'lokasiAktif', 'alertsTerbaru',
            'totalLokasi', 'totalAlerts', 'unreadAlerts',
            'bencanaAktif', 'statusArea', 'statusColor'
        );

        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'lokasi' => $lokasi,
                    'lokasi_aktif' => $lokasiAktif->values(),
                    'alerts_terbaru' => $alertsTerbaru,
                    'stats' => [
                        'total_lokasi' => $totalLokasi,
                        'total_alerts' => $totalAlerts,
                        'unread_alerts' => $unreadAlerts,
                    ],
                    'bencana_aktif' => $bencanaAktif,
                    'status_area' => $statusArea,
                    'status_color' => $statusColor,
                ],
            ]);
        }

        return view('dashboard', $data);
    }

    /**
     * AJAX endpoint: check proximity of user's locations to active disasters
     * Used for real-time notification popup polling
     */
    public function checkProximity()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $lokasiAktif = $user->lokasi()->where('is_active', true)->get();

        if ($lokasiAktif->isEmpty()) {
            return response()->json(['alerts' => [], 'status' => 'safe']);
        }

        $bencanaAktif = Bencana::where('terjadi_pada', '>=', now()->subDays(7))
            ->orderBy('terjadi_pada', 'desc')
            ->limit(50)
            ->get();

        $proximityAlerts = [];

        foreach ($bencanaAktif as $bencana) {
            if (!$bencana->latitude || !$bencana->longitude) continue;

            foreach ($lokasiAktif as $loc) {
                $jarak = BmkgService::haversineDistance(
                    (float)$bencana->latitude, (float)$bencana->longitude,
                    (float)$loc->latitude, (float)$loc->longitude
                );

                if ($jarak <= $loc->radius_km) {
                    $severity = 'waspada';
                    $mag = $bencana->magnitude ?? 0;
                    if ($bencana->jenis_bencana === 'tsunami' || $mag >= 6) $severity = 'awas';
                    elseif ($mag >= 4) $severity = 'siaga';

                    $proximityAlerts[] = [
                        'id' => $bencana->id,
                        'jenis' => $bencana->jenis_bencana,
                        'magnitude' => $bencana->magnitude,
                        'wilayah' => $bencana->wilayah,
                        'jarak_km' => round($jarak, 1),
                        'lokasi_nama' => $loc->nama_lokasi,
                        'severity' => $severity,
                        'terjadi_pada' => $bencana->terjadi_pada->toISOString(),
                        'terjadi_pada_human' => $bencana->terjadi_pada->diffForHumans(),
                    ];
                }
            }
        }

        // Deduplicate by bencana id (pick closest)
        $unique = collect($proximityAlerts)
            ->groupBy('id')
            ->map(fn($group) => $group->sortBy('jarak_km')->first())
            ->values();

        $highestSeverity = 'safe';
        foreach ($unique as $alert) {
            if ($alert['severity'] === 'awas') { $highestSeverity = 'awas'; break; }
            if ($alert['severity'] === 'siaga') $highestSeverity = 'siaga';
            elseif ($highestSeverity !== 'siaga' && $alert['severity'] === 'waspada') $highestSeverity = 'waspada';
        }

        return response()->json([
            'alerts' => $unique,
            'status' => $highestSeverity,
            'unread_count' => Alert::where('user_id', $user->id)->where('status', 'sent')->count(),
        ]);
    }
}
