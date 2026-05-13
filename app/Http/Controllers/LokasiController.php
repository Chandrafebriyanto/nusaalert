<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = Auth::user()->lokasi()->orderBy('created_at', 'desc')->get();
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

        Auth::user()->lokasi()->create($request->only([
            'nama_lokasi', 'latitude', 'longitude', 'radius_km'
        ]));

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil ditambahkan!');
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $this->authorize('update', $lokasi);

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

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil diperbarui!');
    }

    public function toggleActive(Lokasi $lokasi)
    {
        $this->authorize('update', $lokasi);
        $lokasi->update(['is_active' => !$lokasi->is_active]);
        return redirect()->route('lokasi.index')->with('success', 'Status lokasi diperbarui!');
    }

    public function destroy(Lokasi $lokasi)
    {
        $this->authorize('delete', $lokasi);
        $lokasi->delete();
        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil dihapus!');
    }
}
