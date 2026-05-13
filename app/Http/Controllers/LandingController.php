<?php

namespace App\Http\Controllers;

use App\Models\Bencana;
use App\Models\Laporan;
use App\Services\BmkgService;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index(BmkgService $bmkg)
    {
        $gempaTerkini = $bmkg->getGempaTerkini();
        $bencanaHariIni = Bencana::whereDate('terjadi_pada', today())->count();
        $totalBencana = Bencana::count();

        // Get active disasters for map (last 30 days)
        $bencanaAktif = Bencana::where('terjadi_pada', '>=', now()->subDays(30))
            ->orderBy('terjadi_pada', 'desc')
            ->limit(50)
            ->get();

        return view('landing', compact('gempaTerkini', 'bencanaHariIni', 'totalBencana', 'bencanaAktif'));
    }
}
