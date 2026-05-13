<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alert;
use App\Models\Bencana;
use App\Models\Laporan;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $alertsToday = Alert::whereDate('created_at', today())->count();
        $laporanPending = Laporan::where('status', 'pending')->count();

        $users = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $laporan = Laporan::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentBencana = Bencana::orderBy('terjadi_pada', 'desc')
            ->limit(5)
            ->get();

        return view('admin.index', compact(
            'totalUsers', 'alertsToday', 'laporanPending',
            'users', 'laporan', 'recentBencana'
        ));
    }

    public function verifyLaporan(Laporan $laporan)
    {
        $laporan->update([
            'status' => 'verified',
            'verified_by' => auth()->id(),
        ]);

        return redirect()->route('admin.index')->with('success', 'Laporan berhasil diverifikasi.');
    }

    public function rejectLaporan(Laporan $laporan)
    {
        $laporan->delete();
        return redirect()->route('admin.index')->with('success', 'Laporan ditolak.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,member,reporter',
        ]);

        $user->syncRoles([$request->role]);
        return redirect()->route('admin.index')->with('success', 'Role user diperbarui.');
    }
}
