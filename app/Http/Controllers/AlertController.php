<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Alert::with(['bencana', 'lokasi'])
            ->orderBy('created_at', 'desc');

        // Filter by disaster type
        if ($request->filled('jenis')) {
            $query->whereHas('bencana', function ($q) use ($request) {
                $q->where('jenis_bencana', $request->jenis);
            });
        }

        // Filter by date
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $alerts = $query->paginate(15);

        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'data' => $alerts->items(),
                'meta' => [
                    'total' => $alerts->total(),
                    'page' => $alerts->currentPage(),
                    'per_page' => $alerts->perPage(),
                    'last_page' => $alerts->lastPage(),
                ],
            ]);
        }

        return view('alerts.index', compact('alerts'));
    }

    public function markAsRead(Request $request, Alert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            if ($this->wantsJson($request)) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
            }
            abort(403);
        }

        $alert->update([
            'status' => 'read',
            'read_at' => now(),
        ]);

        return $this->respondWithSuccessOrBack($request, 'Alert ditandai telah dibaca.', ['alert' => $alert->fresh()]);
    }

    public function markAllRead(Request $request)
    {
        $count = Alert::where('user_id', Auth::id())
            ->where('status', 'sent')
            ->update([
                'status' => 'read',
                'read_at' => now(),
            ]);

        return $this->respondWithSuccessOrBack($request, "Semua alert ditandai telah dibaca. ({$count} alert)");
    }

    /**
     * AJAX endpoint: return latest unread alerts for notification popup
     */
    public function latestAlerts()
    {
        $alerts = Alert::where('user_id', Auth::id())
            ->where('status', 'sent')
            ->with(['bencana', 'lokasi'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'jenis' => $a->bencana->jenis_bencana ?? 'unknown',
                'wilayah' => $a->bencana->wilayah ?? '',
                'magnitude' => $a->bencana->magnitude,
                'jarak_km' => $a->jarak_km,
                'lokasi_nama' => $a->lokasi->nama_lokasi ?? '',
                'created_at' => $a->created_at->diffForHumans(),
            ]);

        return response()->json([
            'alerts' => $alerts,
            'count' => $alerts->count(),
        ]);
    }
}
