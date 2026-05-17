@extends('layouts.app')

@section('title', 'Admin Panel')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-display font-extrabold text-on-surface mb-2">Admin Panel</h1>
    <p class="text-lg text-on-surface-variant font-sans">Kelola sistem, verifikasi laporan, dan pantau aktivitas platform.</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface border border-outline-variant rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">group</span>
        </div>
        <p class="text-3xl font-display font-extrabold text-on-surface">{{ $totalUsers }}</p>
        <p class="text-sm font-sans text-on-surface-variant">Total Pengguna</p>
    </div>
    <div class="bg-surface border border-outline-variant rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">warning</span>
        </div>
        <p class="text-3xl font-display font-extrabold text-on-surface">{{ $totalBencana }}</p>
        <p class="text-sm font-sans text-on-surface-variant">Total Bencana</p>
    </div>
    <div class="bg-surface border border-outline-variant rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">notifications</span>
        </div>
        <p class="text-3xl font-display font-extrabold text-on-surface">{{ $totalAlerts }}</p>
        <p class="text-sm font-sans text-on-surface-variant">Total Alert</p>
    </div>
    <div class="bg-surface border border-outline-variant rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">campaign</span>
        </div>
        <p class="text-3xl font-display font-extrabold text-on-surface">{{ $totalLaporan }}</p>
        <p class="text-sm font-sans text-on-surface-variant">Total Laporan</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Tambah Bencana Manual --}}
    <div class="bg-surface border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant bg-surface-container flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">add_location_alt</span>
            <h2 class="text-xl font-display font-bold text-on-surface">Peringatan Bencana</h2>
        </div>

        <form action="{{ route('admin.bencana.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-sans font-bold text-sm text-on-surface mb-1">Jenis Bencana <span class="text-error">*</span></label>
                    <select name="jenis_bencana" required
                            class="rounded-lg w-full px-3 py-2 border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">— Pilih Jenis —</option>
                        <option value="gempa">Gempa Bumi</option>
                        <option value="tsunami">Tsunami</option>
                        <option value="banjir">Banjir</option>
                        <option value="cuaca_ekstrem">Cuaca Ekstrem</option>
                        <option value="gunung_api">Gunung Api</option>
                        <option value="tanah_longsor">Tanah Longsor</option>
                    </select>
                </div>
                <div>
                    <label class="block font-sans font-bold text-sm text-on-surface mb-1">Wilayah <span class="text-error">*</span></label>
                    <input name="wilayah" type="text" required placeholder="Cth: Selatan Cianjur, Jawa Barat"
                           class="rounded-lg w-full px-3 py-2 border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block font-sans font-bold text-sm text-on-surface mb-1">Magnitude</label>
                    <input name="magnitude" type="number" step="0.1" min="0" max="10" placeholder="Opsional"
                           class="rounded-lg w-full px-3 py-2 border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block font-sans font-bold text-sm text-on-surface mb-1">Kedalaman (km)</label>
                    <input name="kedalaman_km" type="number" step="0.1" min="0" placeholder="Opsional"
                           class="rounded-lg w-full px-3 py-2 border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            {{-- Map Picker --}}
            <div class="mb-4">
                <label class="block font-sans font-bold text-sm text-on-surface mb-1">Koordinat — Klik peta untuk memilih <span class="text-error">*</span></label>
                <div id="adminBencanaMap" class="w-full h-48 rounded-lg border border-outline-variant mb-2"></div>
                <div class="grid grid-cols-2 gap-4">
                    <input name="latitude" id="admin_lat" type="number" step="any" required readonly placeholder="Latitude"
                           class="rounded-lg w-full px-3 py-2 border border-outline-variant bg-surface-container text-on-surface text-sm focus:outline-none">
                    <input name="longitude" id="admin_lng" type="number" step="any" required readonly placeholder="Longitude"
                           class="rounded-lg w-full px-3 py-2 border border-outline-variant bg-surface-container text-on-surface text-sm focus:outline-none">
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-sans font-bold text-sm text-on-surface mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="2" placeholder="Informasi tambahan (opsional)"
                          class="rounded-lg w-full px-3 py-2 border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
            </div>

            <button type="submit" class="w-full bg-primary text-on-primary font-sans font-bold py-3 rounded-lg shadow-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">add_alert</span>
                Tambahkan Bencana & Kirim Alert
            </button>
            <p class="text-xs text-on-surface-variant mt-2 text-center">Alert akan otomatis dikirim ke pengguna yang berada dalam radius bencana.</p>
        </form>
    </div>

    {{-- Laporan Pending --}}
    <div class="bg-surface border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant bg-surface-container flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">pending_actions</span>
            <h2 class="text-xl font-display font-bold text-on-surface">Laporan Menunggu Verifikasi</h2>
            @if($laporanPending->count() > 0)
                <span class="ml-auto bg-error text-on-error text-xs font-bold rounded-full px-2 py-0.5">{{ $laporanPending->count() }}</span>
            @endif
        </div>
        <div class="p-6 flex flex-col gap-3 max-h-96 overflow-y-auto">
            @forelse($laporanPending as $laporan)
                <div class="border border-outline-variant rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="font-display font-bold text-on-surface text-sm">{{ strtoupper($laporan->jenis_bencana) }}</span>
                            <p class="text-xs text-on-surface-variant">oleh {{ $laporan->user->name }} &mdash; {{ $laporan->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <p class="text-sm font-sans text-on-surface-variant mb-3">{{ Str::limit($laporan->deskripsi, 120) }}</p>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.laporan.verify', $laporan) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="bg-tertiary text-on-tertiary font-sans font-bold text-xs px-3 py-1.5 rounded-lg hover:opacity-90">Verifikasi</button>
                        </form>
                        <form action="{{ route('admin.laporan.reject', $laporan) }}" method="POST" onsubmit="return confirm('Tolak laporan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-error text-on-error font-sans font-bold text-xs px-3 py-1.5 rounded-lg hover:opacity-90">Tolak</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-4xl text-outline mb-2">check_circle</span>
                    <p class="font-sans text-on-surface-variant">Tidak ada laporan yang menunggu verifikasi.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Bencana Terkini & User Management --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
    {{-- Latest Bencana --}}
    <div class="bg-surface border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant bg-surface-container flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">earthquake</span>
            <h2 class="text-xl font-display font-bold text-on-surface">Bencana Terkini</h2>
        </div>
        <div class="divide-y divide-outline-variant max-h-96 overflow-y-auto">
            @forelse($latestBencana as $bencana)
                <div class="p-4 flex items-start gap-3">
                    <div class="w-10 h-10 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center text-sm font-bold shrink-0">
                        {{ $bencana->magnitude ? 'M'.$bencana->magnitude : strtoupper(substr($bencana->jenis_bencana, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-display font-bold text-on-surface text-sm truncate">{{ strtoupper($bencana->jenis_bencana) }} — {{ $bencana->wilayah }}</p>
                        <p class="text-xs text-on-surface-variant font-sans">{{ $bencana->terjadi_pada->diffForHumans() }} • {{ $bencana->sumber_api }}</p>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="font-sans text-on-surface-variant">Belum ada data bencana.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- User Management --}}
    <div class="bg-surface border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant bg-surface-container flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">manage_accounts</span>
            <h2 class="text-xl font-display font-bold text-on-surface">Manajemen Pengguna</h2>
        </div>
        <div class="divide-y divide-outline-variant max-h-96 overflow-y-auto">
            @foreach($users as $user)
                <div class="p-4 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="font-display font-bold text-on-surface text-sm truncate">{{ $user->name }}</p>
                        <p class="text-xs text-on-surface-variant font-sans">{{ $user->email }}</p>
                    </div>
                    <form action="{{ route('admin.users.role', $user) }}" method="POST" class="flex items-center gap-2 shrink-0">
                        @csrf @method('PATCH')
                        <select name="role" class="text-xs border border-outline-variant rounded-lg px-2 py-1 bg-surface font-sans focus:outline-none focus:ring-1 focus:ring-primary">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->roles->first()?->name === $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="text-primary hover:underline text-xs font-sans font-bold">Ubah</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('adminBencanaMap').setView([-2.5489, 118.0149], 5);
        let marker = null;

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OSM contributors'
        }).addTo(map);

        map.on('click', function(e) {
            document.getElementById('admin_lat').value = e.latlng.lat.toFixed(6);
            document.getElementById('admin_lng').value = e.latlng.lng.toFixed(6);

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, { draggable: true }).addTo(map);
                marker.on('dragend', function(ev) {
                    const pos = ev.target.getLatLng();
                    document.getElementById('admin_lat').value = pos.lat.toFixed(6);
                    document.getElementById('admin_lng').value = pos.lng.toFixed(6);
                });
            }
        });
    });
</script>
@endpush
