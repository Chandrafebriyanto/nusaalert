@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-display font-extrabold text-on-surface mb-2">Dashboard Personal</h1>
    <p class="text-lg text-on-surface-variant font-sans">Pantau situasi di sekitar lokasi Anda secara real-time.</p>
</div>

<!-- Status Area & Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Overall Status Card -->
    <div class="md:col-span-2 bg-{{ $statusColor }} text-on-{{ str_replace('alert-', '', $statusColor) }} p-6 rounded-xl shadow-sm border border-outline-variant relative overflow-hidden flex flex-col justify-center">
        @if($statusArea === 'AMAN')
            <div class="absolute -right-5 -top-5 opacity-20">
                <span class="material-symbols-outlined text-[120px]" style="font-variation-settings: 'FILL' 1;">verified_user</span>
            </div>
        @else
            <div class="absolute -right-5 -top-5 opacity-20">
                <span class="material-symbols-outlined text-[120px]" style="font-variation-settings: 'FILL' 1;">warning</span>
            </div>
            <div class="absolute inset-0 bg-white/10 animate-pulse"></div>
        @endif
        
        <div class="relative z-10">
            <h2 class="text-sm font-sans font-bold uppercase tracking-wider mb-1 opacity-90">Status Area Pantauan</h2>
            <div class="text-5xl font-display font-extrabold">{{ $statusArea }}</div>
            <p class="mt-2 text-sm font-sans opacity-90">
                @if($statusArea === 'AMAN')
                    Tidak ada ancaman signifikan di sekitar lokasi Anda saat ini.
                @else
                    Terdapat peringatan bencana di sekitar Anda. Segera periksa riwayat peringatan.
                @endif
            </p>
        </div>
    </div>

    <!-- Stat: Lokasi Aktif -->
    <div class="bg-surface p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-center">
        <span class="text-sm font-sans font-bold text-on-surface-variant uppercase tracking-wider mb-2">Lokasi Aktif</span>
        <div class="flex items-end gap-2">
            <span class="text-4xl font-display font-extrabold text-on-surface">{{ $lokasiAktif->count() }}</span>
            <span class="text-base text-on-surface-variant pb-1">/ {{ $totalLokasi }}</span>
        </div>
        <a href="{{ route('lokasi.index') }}" class="mt-4 text-sm font-sans font-bold text-primary hover:underline">Kelola Lokasi &rarr;</a>
    </div>

    <!-- Stat: Unread Alerts -->
    <div class="bg-surface p-6 rounded-xl shadow-sm border border-outline-variant flex flex-col justify-center relative">
        @if($unreadAlerts > 0)
            <div class="absolute top-4 right-4 w-3 h-3 bg-error rounded-full animate-ping"></div>
            <div class="absolute top-4 right-4 w-3 h-3 bg-error rounded-full"></div>
        @endif
        <span class="text-sm font-sans font-bold text-on-surface-variant uppercase tracking-wider mb-2">Peringatan Baru</span>
        <div class="flex items-end gap-2">
            <span class="text-4xl font-display font-extrabold {{ $unreadAlerts > 0 ? 'text-error' : 'text-on-surface' }}">{{ $unreadAlerts }}</span>
        </div>
        <a href="{{ route('alerts.index') }}" class="mt-4 text-sm font-sans font-bold text-primary hover:underline">Lihat Riwayat &rarr;</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Map Section -->
    <div class="lg:col-span-2 flex flex-col gap-4">
        <h2 class="text-xl font-display font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">map</span>
            Peta Radius Pantauan
        </h2>
        <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm h-100 relative z-10" id="userMap">
            <!-- Map container -->
        </div>
    </div>

    <!-- Recent Alerts -->
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-display font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">notifications</span>
                Peringatan Terkini
            </h2>
            <a href="{{ route('alerts.index') }}" class="text-sm font-sans font-bold text-primary hover:underline">Lihat Semua</a>
        </div>
        
        <div class="flex flex-col gap-3">
            @forelse($alertsTerbaru as $alert)
                <div class="bg-surface border {{ $alert->status === 'sent' ? 'border-primary shadow-md' : 'border-outline-variant' }} rounded-xl p-4 transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-sans font-bold px-2 py-1 rounded bg-surface-container-highest text-on-surface-variant">
                            {{ $alert->bencana->jenis_bencana }}
                        </span>
                        <span class="text-xs font-sans text-on-surface-variant">{{ $alert->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 class="font-sans font-bold text-on-surface mb-1">{{ $alert->bencana->wilayah }}</h3>
                    <p class="text-sm font-sans text-on-surface-variant mb-2">Jarak: {{ $alert->jarak_km }} km dari {{ $alert->lokasi->nama_lokasi }}</p>
                    
                    @if($alert->status === 'sent')
                        <form action="{{ route('alerts.read', $alert) }}" method="POST" class="mt-2 text-right">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs font-sans font-bold text-primary hover:underline">Tandai Dibaca</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="bg-surface-container-low border border-outline-variant border-dashed rounded-xl p-8 text-center flex flex-col items-center justify-center">
                    <span class="material-symbols-outlined text-4xl text-outline mb-2">done_all</span>
                    <p class="font-sans text-on-surface-variant">Tidak ada peringatan baru.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('userMap').setView([-2.5489, 118.0149], 5);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OSM contributors'
        }).addTo(map);

        const lokasiData = @json($lokasiAktif);
        const bencanaData = @json($bencanaAktif);
        
        const bounds = [];

        // Draw user locations and their radius
        lokasiData.forEach(lokasi => {
            const latlng = [lokasi.latitude, lokasi.longitude];
            
            // Marker
            L.marker(latlng, {
                icon: L.divIcon({
                    html: `<div class="w-4 h-4 bg-tertiary rounded-full border-2 border-white shadow-md"></div>`,
                    className: ''
                })
            }).addTo(map).bindPopup(`<b>${lokasi.nama_lokasi}</b>`);

            // Radius Circle
            L.circle(latlng, {
                color: 'var(--color-tertiary)',
                fillColor: 'var(--color-tertiary)',
                fillOpacity: 0.1,
                radius: lokasi.radius_km * 1000 // convert to meters
            }).addTo(map);

            bounds.push(latlng);
        });

        // Draw active disasters
        bencanaData.forEach(bencana => {
            if(bencana.latitude && bencana.longitude) {
                const latlng = [bencana.latitude, bencana.longitude];
                L.marker(latlng, {
                    icon: L.divIcon({
                        html: `<div class="w-5 h-5 bg-primary rounded-full border-2 border-white shadow-md relative">
                                <div class="absolute inset-0 bg-primary rounded-full animate-ping opacity-50"></div>
                               </div>`,
                        className: ''
                    })
                }).addTo(map).bindPopup(`<b>${bencana.jenis_bencana}</b><br>${bencana.wilayah}`);
                bounds.push(latlng);
            }
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    });
</script>
@endpush
