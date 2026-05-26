<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $laporanVerified = Laporan::where('status', '!=', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $laporanPending = Laporan::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'verified' => $laporanVerified,
                    'pending' => $laporanPending,
                ],
            ]);
        }

        return view('laporan.index', compact('laporanVerified', 'laporanPending'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_bencana' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'wilayah' => 'nullable|string|max:255',
            'deskripsi' => 'required|string|max:2000',
            'foto' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['jenis_bencana', 'latitude', 'longitude', 'wilayah', 'deskripsi']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('foto')) {
            $data['foto_url'] = $request->file('foto')->store('laporan', 'public');
        }

        $laporan = Laporan::create($data);

        return $this->respondWithSuccessOrRedirect($request, 'laporan.index', 'Laporan berhasil dikirim! Menunggu verifikasi.', ['laporan' => $laporan], 201);
    }
}
