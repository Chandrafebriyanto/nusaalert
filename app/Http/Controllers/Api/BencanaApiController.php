<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bencana;
use App\Services\BmkgService;
use Illuminate\Http\Request;

class BencanaApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Bencana::orderBy('terjadi_pada', 'desc');

        if ($request->filled('jenis')) {
            $query->where('jenis_bencana', $request->jenis);
        }

        if ($request->filled('days')) {
            $query->where('terjadi_pada', '>=', now()->subDays((int)$request->days));
        }

        return response()->json($query->paginate(20));
    }

    public function show(Bencana $bencana)
    {
        return response()->json($bencana);
    }

    public function gempaTerkini(BmkgService $bmkg)
    {
        $data = $bmkg->getGempaTerkini();
        return response()->json(['data' => $data]);
    }

    public function bencanaAktif()
    {
        $bencana = Bencana::where('terjadi_pada', '>=', now()->subDays(30))
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
                'terjadi_pada' => $b->terjadi_pada->toISOString(),
                'terjadi_pada_human' => $b->terjadi_pada->diffForHumans(),
            ]);

        return response()->json(['data' => $bencana]);
    }
}
