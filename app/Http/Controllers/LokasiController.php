<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class LokasiController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $lokasi = $user->lokasi()->orderBy('created_at', 'desc')->get();

        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'data' => $lokasi,
            ]);
        }

        return view('lokasi.index', compact('lokasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_km' => 'required|integer|min:1|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $lokasi = $user->lokasi()->create($request->only([
            'nama_lokasi', 'latitude', 'longitude', 'radius_km'
        ]));

        return $this->respondWithSuccessOrRedirect($request, 'lokasi.index', 'Lokasi berhasil ditambahkan!', ['lokasi' => $lokasi], 201);
    }

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

        return $this->respondWithSuccessOrRedirect($request, 'lokasi.index', 'Lokasi berhasil diperbarui!', ['lokasi' => $lokasi->fresh()]);
    }

    public function toggleActive(Request $request, Lokasi $lokasi)
    {
        abort_unless(Auth::id() === $lokasi->user_id, 403, 'Unauthorized');
        $lokasi->update(['is_active' => !$lokasi->is_active]);

        return $this->respondWithSuccessOrRedirect($request, 'lokasi.index', 'Status lokasi diperbarui!', ['lokasi' => $lokasi->fresh()]);
    }

    public function destroy(Request $request, Lokasi $lokasi)
    {
        abort_unless(Auth::id() === $lokasi->user_id, 403, 'Unauthorized');
        $lokasi->delete();

        return $this->respondWithSuccessOrRedirect($request, 'lokasi.index', 'Lokasi berhasil dihapus!');
    }
}
