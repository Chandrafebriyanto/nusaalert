@extends('layouts.base')

@section('title', 'Peta Bencana')

@section('body')
<div class="flex flex-col h-screen overflow-hidden">
    <!-- TopAppBar -->
    <header class="sticky top-0 w-full flex items-center justify-between px-4 md:px-6 py-3 bg-surface border-b border-outline-variant shadow-sm z-50">
        <div class="flex items-center gap-4">
            <a href="{{ route('landing') }}" class="text-2xl md:text-3xl font-display font-extrabold tracking-tight text-primary leading-none">
                NusaAlert
            </a>
            <!-- Search Bar -->
            <div class="hidden md:flex relative max-w-md w-full ml-4">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-xl">search</span>
                <input type="text" id="mapSearchInput"
                       class="w-full pl-12 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-full font-sans text-base focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all"
                       placeholder="Cari lokasi atau jenis bencana...">
            </div>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-primary text-on-primary font-sans font-bold text-sm px-5 py-2.5 rounded-lg shadow-sm hover:opacity-90 transition-opacity">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-primary text-on-primary font-sans font-bold text-sm px-5 py-2.5 rounded-lg shadow-sm hover:opacity-90 transition-opacity">
                    Masuk
                </a>
            @endauth
        </div>
    </header>

    <!-- Mobile Search -->
    <div class="px-4 py-3 bg-surface border-b border-outline-variant md:hidden z-40 relative">
        <div class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
            <input type="text" id="mapSearchInputMobile"
                   class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border-b-2 border-outline-variant font-sans text-base focus:outline-none focus:border-primary transition-colors bg-transparent rounded-none"
                   placeholder="Cari lokasi...">
        </div>
    </div>

    <!-- Map Container -->
    <div class="flex-1 relative w-full bg-surface-container-highest overflow-hidden">
        <div id="fullMap" class="w-full h-full z-0"></div>

        <!-- Floating Filter Controls -->
        <div class="hidden md:flex absolute top-4 left-4 gap-2 z-20">
            <button onclick="filterMap('all')" class="filter-btn active bg-surface px-4 py-2 rounded-full border border-primary text-primary font-sans font-bold text-sm shadow-sm hover:bg-surface-container-low transition-colors flex items-center gap-2" data-filter="all">
                <span class="material-symbols-outlined text-lg">layers</span>
                Semua
            </button>
            <button onclick="filterMap('gempa')" class="filter-btn bg-surface px-4 py-2 rounded-full border border-outline-variant text-on-surface-variant font-sans font-bold text-sm shadow-sm hover:bg-surface-container-low hover:text-primary transition-colors flex items-center gap-2" data-filter="gempa">
                <span class="w-2 h-2 rounded-full bg-primary"></span>
                Gempa Bumi
            </button>
            <button onclick="filterMap('banjir')" class="filter-btn bg-surface px-4 py-2 rounded-full border border-outline-variant text-on-surface-variant font-sans font-bold text-sm shadow-sm hover:bg-surface-container-low hover:text-primary transition-colors flex items-center gap-2" data-filter="banjir">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                Banjir
            </button>
            <button onclick="filterMap('cuaca_ekstrem')" class="filter-btn bg-surface px-4 py-2 rounded-full border border-outline-variant text-on-surface-variant font-sans font-bold text-sm shadow-sm hover:bg-surface-container-low hover:text-primary transition-colors flex items-center gap-2" data-filter="cuaca_ekstrem">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Cuaca Ekstrem
            </button>
        </div>

        <!-- Map Legend -->
        <div class="absolute bottom-6 left-4 bg-surface/95 backdrop-blur-md p-4 rounded-xl border border-outline-variant shadow-sm z-20 hidden md:block">
            <h3 class="font-sans font-bold text-xs text-on-surface mb-3 uppercase tracking-wider">Status Alert</h3>
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-primary"></div>
                    <span class="font-sans text-sm text-on-surface-variant">Awas (Kritis)</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    <span class="font-sans text-sm text-on-surface-variant">Siaga (Menengah)</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                    <span class="font-sans text-sm text-on-surface-variant">Waspada (Rendah)</span>
                </div>
            </div>
        </div>

        <!-- Active Alerts Panel -->
        <div class="absolute bottom-0 w-full md:w-96 md:right-4 md:top-4 md:bottom-6 md:h-auto z-20 flex flex-col pointer-events-none">
            <div class="bg-surface/95 backdrop-blur-xl border border-outline-variant shadow-lg md:rounded-2xl rounded-t-2xl flex-1 flex flex-col overflow-hidden pointer-events-auto max-h-[40vh] md:max-h-full">
                <!-- Panel Header -->
                <div class="p-4 border-b border-outline-variant bg-surface-container flex justify-between items-center">
                    <div>
                        <h2 class="font-display font-bold text-lg text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">campaign</span>
                            Kejadian Aktif
                        </h2>
                        <p class="font-sans text-sm text-on-surface-variant" id="alertCountLabel">{{ count($bencanaAktif) }} Kejadian Terkini</p>
                    </div>
                    <button class="md:hidden text-on-surface-variant p-2" onclick="this.closest('.pointer-events-auto').classList.toggle('max-h-[40vh]')">
                        <span class="material-symbols-outlined">expand_less</span>
                    </button>
                </div>

                <!-- Alert List -->
                <div class="flex-1 overflow-y-auto p-4 flex flex-col gap-3" id="alertListPanel">
                    @forelse($bencanaAktif as $bencana)
                        <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden relative shadow-sm group hover:border-primary transition-colors cursor-pointer bencana-card"
                             data-jenis="{{ $bencana->jenis_bencana }}"
                             data-lat="{{ $bencana->latitude }}"
                             data-lng="{{ $bencana->longitude }}"
                             onclick="flyToMarker({{ $bencana->latitude }}, {{ $bencana->longitude }})">
                            @php
                                $severity = 'outline';
                                $severityLabel = 'WASPADA';
                                $severityClass = 'bg-surface-variant text-on-surface-variant';
                                if ($bencana->magnitude) {
                                    if ($bencana->magnitude >= 6) {
                                        $severity = 'primary';
                                        $severityLabel = 'AWAS';
                                        $severityClass = 'bg-error-container text-on-error-container';
                                    } elseif ($bencana->magnitude >= 4) {
                                        $severity = 'amber-500';
                                        $severityLabel = 'SIAGA';
                                        $severityClass = 'bg-amber-100 text-amber-800';
                                    }
                                }
                            @endphp
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-{{ $severity }}"></div>
                            <div class="p-4 pl-5">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-sans font-bold {{ $severityClass }} uppercase">{{ $severityLabel }}</span>
                                    <span class="text-xs font-sans text-on-surface-variant">{{ $bencana->terjadi_pada->diffForHumans() }}</span>
                                </div>
                                <h4 class="font-display text-base font-bold text-on-surface mb-1">
                                    {{ strtoupper($bencana->jenis_bencana) }} {{ $bencana->magnitude ? 'M ' . $bencana->magnitude : '' }}
                                </h4>
                                <p class="font-sans text-sm text-on-surface-variant flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">location_on</span>
                                    {{ $bencana->wilayah ?? 'Lokasi tidak diketahui' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center flex flex-col items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-outline mb-2">check_circle</span>
                            <p class="font-sans text-on-surface-variant">Tidak ada kejadian bencana aktif saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('fullMap', {
            zoomControl: false
        }).setView([-2.5489, 118.0149], 5);

        L.control.zoom({ position: 'topright' }).addTo(map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        const bencanaData = @json($bencanaAktif);
        const markers = [];

        function getMarkerIcon(jenis, magnitude) {
            let color = '#9ca3af'; // default gray
            let pulseClass = '';

            if (jenis === 'gempa') {
                color = magnitude >= 6 ? '#b71422' : (magnitude >= 4 ? '#f59e0b' : '#eab308');
                if (magnitude >= 5) pulseClass = 'animate-ping';
            } else if (jenis === 'banjir') {
                color = '#3b82f6';
            } else if (jenis === 'cuaca_ekstrem') {
                color = '#f59e0b';
            } else if (jenis === 'tsunami') {
                color = '#b71422';
                pulseClass = 'animate-ping';
            }

            return L.divIcon({
                html: `<div class="relative flex items-center justify-center">
                    <div class="w-6 h-6 rounded-full border-2 border-white shadow-lg" style="background: ${color}"></div>
                    ${pulseClass ? `<div class="absolute inset-0 rounded-full ${pulseClass} opacity-50" style="background: ${color}"></div>` : ''}
                </div>`,
                className: '',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
        }

        bencanaData.forEach(bencana => {
            if (bencana.latitude && bencana.longitude) {
                const icon = getMarkerIcon(bencana.jenis_bencana, bencana.magnitude);
                const marker = L.marker([bencana.latitude, bencana.longitude], { icon: icon })
                    .addTo(map)
                    .bindPopup(`
                        <div class="font-sans min-w-[200px]">
                            <strong class="font-display text-lg block mb-1">${bencana.jenis_bencana.toUpperCase()}</strong>
                            <span class="text-sm text-gray-600 block mb-2">${bencana.wilayah || 'Lokasi tidak diketahui'}</span>
                            ${bencana.magnitude ? `<div class="text-sm"><strong>Magnitude:</strong> ${bencana.magnitude}</div>` : ''}
                            ${bencana.kedalaman_km ? `<div class="text-sm"><strong>Kedalaman:</strong> ${bencana.kedalaman_km} km</div>` : ''}
                            <small class="text-gray-500 mt-2 block">${new Date(bencana.terjadi_pada).toLocaleString('id-ID')}</small>
                        </div>
                    `);
                marker._bencanaData = bencana;
                markers.push(marker);
            }
        });

        // Filter function
        window.filterMap = function(jenis) {
            // Update button states
            document.querySelectorAll('.filter-btn').forEach(btn => {
                if (btn.dataset.filter === jenis) {
                    btn.classList.add('border-primary', 'text-primary');
                    btn.classList.remove('border-outline-variant', 'text-on-surface-variant');
                } else {
                    btn.classList.remove('border-primary', 'text-primary');
                    btn.classList.add('border-outline-variant', 'text-on-surface-variant');
                }
            });

            // Filter markers
            markers.forEach(marker => {
                if (jenis === 'all' || marker._bencanaData.jenis_bencana === jenis) {
                    map.addLayer(marker);
                } else {
                    map.removeLayer(marker);
                }
            });

            // Filter sidebar cards
            document.querySelectorAll('.bencana-card').forEach(card => {
                if (jenis === 'all' || card.dataset.jenis === jenis) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            // Update count
            const visibleCards = document.querySelectorAll('.bencana-card:not([style*="display: none"])');
            document.getElementById('alertCountLabel').textContent = visibleCards.length + ' Kejadian Terkini';
        };

        // Fly to marker function
        window.flyToMarker = function(lat, lng) {
            map.flyTo([lat, lng], 10, { duration: 1.5 });
        };
    });
</script>
@endpush
