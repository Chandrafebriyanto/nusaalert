@extends('layouts.app')

@section('title', 'Admin Panel')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-display font-extrabold text-on-surface mb-2">Admin Panel</h1>
    <p class="text-lg text-on-surface-variant font-sans">Sistem Manajemen NusaAlert</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-center">
        <span class="text-sm font-sans font-bold text-on-surface-variant uppercase tracking-wider mb-2">Total Pengguna</span>
        <div class="text-4xl font-display font-extrabold text-on-surface">{{ $totalUsers }}</div>
    </div>
    
    <div class="bg-surface p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-center">
        <span class="text-sm font-sans font-bold text-on-surface-variant uppercase tracking-wider mb-2">Alerts Dikirim (Hari Ini)</span>
        <div class="text-4xl font-display font-extrabold text-primary">{{ $alertsToday }}</div>
    </div>
    
    <div class="bg-surface p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-center relative">
        @if($laporanPending > 0)
            <div class="absolute top-4 right-4 w-3 h-3 bg-error rounded-full"></div>
        @endif
        <span class="text-sm font-sans font-bold text-on-surface-variant uppercase tracking-wider mb-2">Laporan Menunggu Validasi</span>
        <div class="text-4xl font-display font-extrabold text-error">{{ $laporanPending }}</div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
    <!-- Kolom Kiri: Verifikasi Laporan & Bencana -->
    <div class="flex flex-col gap-8">
        
        <!-- Verifikasi Laporan -->
        <div class="bg-surface border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-outline-variant bg-surface-container flex justify-between items-center">
                <h2 class="font-display font-bold text-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">rule</span>
                    Verifikasi Laporan Warga
                </h2>
                <span class="bg-error text-on-error text-xs font-bold px-2 py-1 rounded-full">{{ $laporan->count() }} Pending</span>
            </div>
            
            <div class="divide-y divide-outline-variant flex-1 max-h-100 overflow-y-auto">
                @forelse($laporan as $lap)
                    <div class="p-4 flex flex-col gap-2">
                        <div class="flex justify-between items-start">
                            <span class="font-sans font-bold text-sm bg-surface-container-highest px-2 py-1 rounded">{{ $lap->jenis_bencana }}</span>
                            <span class="text-xs text-on-surface-variant">{{ $lap->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm font-sans line-clamp-2">{{ $lap->deskripsi }}</p>
                        <span class="text-xs font-sans text-on-surface-variant">Oleh: {{ $lap->user->name }} | {{ $lap->wilayah }}</span>
                        
                        <div class="flex gap-2 mt-2">
                            <form action="{{ route('admin.laporan.verify', $lap) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full bg-tertiary text-on-tertiary text-sm font-bold py-1.5 rounded flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">check</span> Setujui
                                </button>
                            </form>
                            <form action="{{ route('admin.laporan.reject', $lap) }}" method="POST" class="flex-1" onsubmit="return confirm('Tolak laporan ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full bg-surface-container border border-error text-error text-sm font-bold py-1.5 rounded flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">close</span> Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-on-surface-variant font-sans text-sm">
                        Tidak ada laporan warga yang perlu diverifikasi.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Log Bencana Terakhir -->
        <div class="bg-surface border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-outline-variant bg-surface-container">
                <h2 class="font-display font-bold text-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">sensors</span>
                    Data Bencana Terbaru
                </h2>
            </div>
            
            <div class="divide-y divide-outline-variant">
                @foreach($recentBencana as $bencana)
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-container text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined">warning</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-sans font-bold">{{ strtoupper($bencana->jenis_bencana) }} {{ $bencana->magnitude ? 'M'.$bencana->magnitude : '' }}</span>
                                <span class="text-xs text-on-surface-variant">{{ $bencana->wilayah }}</span>
                            </div>
                        </div>
                        <span class="text-xs text-on-surface-variant text-right">
                            {{ $bencana->terjadi_pada->format('d/m/Y H:i') }}<br>
                            Src: {{ $bencana->sumber_api }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Kolom Kanan: Manajemen Pengguna -->
    <div class="bg-surface border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col">
        <div class="p-4 border-b border-outline-variant bg-surface-container">
            <h2 class="font-display font-bold text-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">group</span>
                Manajemen Pengguna
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left font-sans text-sm">
                <thead class="bg-surface-container-lowest border-b border-outline-variant">
                    <tr>
                        <th class="p-4 font-bold text-on-surface-variant">Nama / Email</th>
                        <th class="p-4 font-bold text-on-surface-variant">Role</th>
                        <th class="p-4 font-bold text-on-surface-variant">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @foreach($users as $user)
                        <tr class="hover:bg-surface-container-lowest">
                            <td class="p-4">
                                <div class="font-bold text-on-surface">{{ $user->name }}</div>
                                <div class="text-xs text-on-surface-variant">{{ $user->email }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs font-bold 
                                    {{ $user->hasRole('admin') ? 'bg-error-container text-error' : 'bg-surface-container-high text-on-surface-variant' }}">
                                    {{ $user->getRoleNames()->first() ?? 'member' }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.role', $user) }}" method="POST" class="flex gap-2 items-center">
                                        @csrf @method('PATCH')
                                        <select name="role" class="bg-surface border border-outline-variant rounded px-2 py-1 text-xs" onchange="this.form.submit()">
                                            <option value="member" {{ $user->hasRole('member') ? 'selected' : '' }}>Member</option>
                                            <option value="reporter" {{ $user->hasRole('reporter') ? 'selected' : '' }}>Reporter</option>
                                            <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                @else
                                    <span class="text-xs text-on-surface-variant italic">Anda (Admin)</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-outline-variant bg-surface-container-lowest">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
