@extends('layouts.guest')

@section('content')
<!-- Hero Section -->
<section class="relative w-full overflow-hidden bg-surface border-b border-outline-variant">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img alt="Hero Background" class="w-full h-full object-cover opacity-10 mix-blend-multiply grayscale text-transparent" src="https://images.unsplash.com/photo-1594328574751-2475e7a93ef0?q=80&w=1200&auto=format&fit=crop" />
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 w-full px-4 md:px-10 py-16 md:py-24 flex flex-col md:flex-row items-center gap-10">
        <div class="flex-1 max-w-2xl flex flex-col gap-6">
            <span class="inline-block px-4 py-1.5 bg-error-container text-on-error-container font-sans font-bold text-sm rounded-full self-start">
                Sistem Peringatan Dini Bencana
            </span>
            <h1 class="text-5xl md:text-6xl font-display font-extrabold text-on-surface leading-tight tracking-tight">
                Siaga <span class="text-primary">Sebelum</span> Bencana
            </h1>
            <p class="text-lg text-on-surface-variant max-w-xl font-sans">
                Dapatkan notifikasi real-time dari BMKG & BNPB langsung di perangkat Anda. Pantau potensi ancaman dan pastikan keselamatan keluarga dengan informasi yang akurat dan cepat.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 mt-4">
                <a href="{{ route('register') }}" class="bg-primary text-on-primary font-sans font-bold text-lg px-8 py-4 rounded-lg shadow-md hover:bg-primary-container hover:text-on-primary-container transition-colors flex items-center justify-center gap-2">
                    Daftar Sekarang
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
                <a href="{{ route('learn-system') }}" class="bg-surface border-2 border-outline-variant text-on-surface font-sans font-bold text-lg px-8 py-4 rounded-lg hover:border-primary hover:text-primary transition-colors flex items-center justify-center gap-2">
                    Pelajari Sistem
                </a>
            </div>
        </div>

        <!-- Hero Visual / Mockup -->
        <div class="flex-1 w-full max-w-lg mt-10 md:mt-0 relative">
            <div class="bg-surface border border-outline shadow-xl rounded-xl p-6 relative z-10 transform md:rotate-2 hover:rotate-0 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-outline-variant">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">warning</span>
                        <span class="font-sans font-bold text-sm text-primary tracking-wide">PERINGATAN DINI</span>
                    </div>
                    <span class="text-xs text-on-surface-variant font-sans">Baru saja</span>
                </div>
                @if($gempaTerkini)
                    <h3 class="text-xl font-display font-bold text-on-surface mb-2">Potensi Gempa Bumi</h3>
                    <p class="text-base text-on-surface-variant font-sans mb-4">
                        Magnitude {{ $gempaTerkini['Magnitude'] }} terdeteksi di kedalaman {{ $gempaTerkini['Kedalaman'] }}. {{ $gempaTerkini['Wilayah'] }}
                    </p>
                @else
                    <h3 class="text-xl font-display font-bold text-on-surface mb-2">Simulasi Gempa Bumi</h3>
                    <p class="text-base text-on-surface-variant font-sans mb-4">Magnitude 5.8 terdeteksi di kedalaman 10km. Harap jauhi area pantai.</p>
                @endif

                <div class="bg-error-container text-on-error-container px-4 py-3 rounded-lg font-sans font-bold text-sm flex items-center justify-center gap-3">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                    </span>
                    Live Monitoring Active
                </div>
            </div>
            <!-- Decorative back card -->
            <div class="absolute inset-0 bg-surface-container-high border border-outline-variant shadow-md rounded-xl transform -rotate-3 scale-95 z-0 translate-y-4"></div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="w-full px-4 md:px-10 py-16 bg-surface-container-lowest border-b border-outline-variant">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-display font-bold text-on-surface mb-8">Status Nasional Saat Ini</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stat Card 1 -->
            <div class="bg-surface border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col gap-2 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <span class="material-symbols-outlined text-6xl">public</span>
                </div>
                <span class="font-sans font-bold text-sm text-on-surface-variant tracking-wide">Jumlah Bencana Hari Ini</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-display font-extrabold text-primary">{{ $bencanaHariIni }}</span>
                    <span class="text-base font-sans text-on-surface-variant">Kejadian</span>
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="bg-surface border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col gap-2 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <span class="material-symbols-outlined text-6xl">sensors</span>
                </div>
                <span class="font-sans font-bold text-sm text-on-surface-variant tracking-wide">Total Data Bencana</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-display font-extrabold text-on-surface">{{ $totalBencana }}</span>
                    <span class="text-base font-sans text-on-surface-variant">Record</span>
                </div>
            </div>

            <!-- Stat Card 3 -->
            <div class="bg-surface border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col gap-2 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <span class="material-symbols-outlined text-6xl">update</span>
                </div>
                <span class="font-sans font-bold text-sm text-on-surface-variant tracking-wide">Status Sistem</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-2xl font-display font-extrabold text-tertiary flex items-center gap-2">
                        <span class="material-symbols-outlined">check_circle</span>
                        Online & Sinkron
                    </span>
                </div>
                <div class="text-sm font-sans text-on-surface-variant mt-2">Update setiap 5 menit</div>
            </div>
        </div>
    </div>
</section>

<!-- Interactive Map Preview Section -->
<section id="peta" class="w-full px-4 md:px-10 py-16 bg-surface">
    <div class="max-w-7xl mx-auto flex flex-col gap-8">
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <h2 class="text-3xl font-display font-bold text-on-surface mb-2">Peta Bencana Aktif</h2>
                <p class="text-lg text-on-surface-variant font-sans max-w-2xl">Pantauan visual kejadian bencana dalam 30 hari terakhir. Silakan login untuk melihat fitur radius personal.</p>
            </div>
            <a href="{{ route('peta') }}" class="bg-surface border border-outline-variant text-on-surface font-sans font-bold text-sm px-6 py-3 rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined">map</span>
                Buka Peta Penuh
            </a>
        </div>

        <div class="w-full h-125 bg-surface-container rounded-2xl border-2 border-outline-variant overflow-hidden shadow-sm relative z-10" id="publicMap">
            <!-- Leaflet map will be mounted here -->
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize public map
        const map = L.map('publicMap').setView([-2.5489, 118.0149], 5); 

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        const bencanaData = @json($bencanaAktif);

        // Custom icon for earthquakes
        const gempaIcon = L.divIcon({
            html: `<div class="w-6 h-6 bg-primary rounded-full border-2 border-white shadow-md flex items-center justify-center relative">
                    <div class="absolute inset-0 bg-primary rounded-full animate-ping opacity-50"></div>
                   </div>`,
            className: '',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        // Add markers
        bencanaData.forEach(bencana => {
            if(bencana.latitude && bencana.longitude) {
                let icon = gempaIcon; // Default to gempa for now

                const marker = L.marker([bencana.latitude, bencana.longitude], {icon: icon})
                    .addTo(map)
                    .bindPopup(`
                        <div class="font-sans">
                            <strong class="font-display text-lg">${bencana.jenis_bencana.toUpperCase()}</strong><br>
                            <span class="text-sm text-on-surface-variant">${bencana.wilayah}</span><br>
                            ${bencana.magnitude ? `Magnitude: ${bencana.magnitude}<br>` : ''}
                            ${bencana.kedalaman_km ? `Kedalaman: ${bencana.kedalaman_km} km<br>` : ''}
                            <small class="text-on-surface-variant mt-2 block">${new Date(bencana.terjadi_pada).toLocaleString('id-ID')}</small>
                        </div>
                    `);
            }
        });
    });
</script>
@endpush
