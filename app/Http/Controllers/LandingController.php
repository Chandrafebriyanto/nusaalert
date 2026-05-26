<?php

namespace App\Http\Controllers;

use App\Models\Bencana;
use App\Models\Laporan;
use App\Services\BmkgService;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request, BmkgService $bmkg)
    {
        $gempaTerkini = $bmkg->getGempaTerkini();
        $bencanaHariIni = Bencana::whereDate('terjadi_pada', today())->count();
        $totalBencana = Bencana::count();

        // Get active disasters for map (last 30 days)
        $bencanaAktif = Bencana::where('terjadi_pada', '>=', now()->subDays(30))
            ->orderBy('terjadi_pada', 'desc')
            ->limit(50)
            ->get();

        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'gempa_terkini' => $gempaTerkini,
                    'bencana_hari_ini' => $bencanaHariIni,
                    'total_bencana' => $totalBencana,
                    'bencana_aktif' => $bencanaAktif,
                ],
            ]);
        }

        return view('landing', compact('gempaTerkini', 'bencanaHariIni', 'totalBencana', 'bencanaAktif'));
    }

    public function learnSystem()
    {
        return view('learn-system');
    }

    public function fullMap(Request $request)
    {
        $bencanaAktif = Bencana::where('terjadi_pada', '>=', now()->subDays(30))
            ->orderBy('terjadi_pada', 'desc')
            ->limit(100)
            ->get();

        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'data' => $bencanaAktif,
                'total' => $bencanaAktif->count(),
            ]);
        }

        return view('peta', compact('bencanaAktif'));
    }

    public function panduanKeselamatan()
    {
        return view('pages.panduan-keselamatan');
    }

    public function kebijakanPrivasi()
    {
        return view('pages.kebijakan-privasi');
    }

    public function kontakDarurat()
    {
        return view('pages.kontak-darurat');
    }
}
