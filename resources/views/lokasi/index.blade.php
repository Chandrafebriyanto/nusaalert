@extends('layouts.app')

@section('title', 'Kelola Lokasi')

@section('content')
<div class="flex justify-between items-end mb-6">
    <div>
        <h1 class="text-3xl font-display font-extrabold text-on-surface mb-2">Kelola Lokasi Pantauan</h1>
        <p class="text-lg text-on-surface-variant font-sans">Atur titik lokasi dan radius personal untuk sistem peringatan dini Anda.</p>
    </div>
    <button onclick="document.getElementById('modalTambahLokasi').classList.remove('hidden')" class="bg-primary text-on-primary font-sans font-bold px-4 py-2 rounded-lg shadow-sm hover:opacity-90 flex items-center gap-2">
        <span class="material-symbols-outlined">add_location</span>
        Tambah Lokasi
    </button>
</div>

<!-- List Lokasi -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($lokasi as $loc)
        <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm flex flex-col relative group">
            <div class="h-32 bg-surface-container-high relative">
                <!-- Static Map Preview Placeholder -->
                <div class="absolute inset-0 flex items-center justify-center opacity-20">
                    <span class="material-symbols-outlined text-6xl">map</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-surface to-transparent"></div>
                <div class="absolute bottom-4 left-4 flex gap-2">
                    <span class="bg-surface text-on-surface text-xs font-sans font-bold px-2 py-1 rounded shadow-sm border border-outline-variant flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">radar</span>
                        {{ $loc->radius_km }} km
                    </span>
                    <span class="{{ $loc->is_active ? 'bg-tertiary-container text-on-tertiary-container' : 'bg-surface-container-highest text-on-surface-variant' }} text-xs font-sans font-bold px-2 py-1 rounded shadow-sm border border-outline-variant flex items-center gap-1">
                        {{ $loc->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
            
            <div class="p-5 flex-1 flex flex-col gap-2">
                <h3 class="text-xl font-display font-bold text-on-surface">{{ $loc->nama_lokasi }}</h3>
                <div class="text-sm font-sans text-on-surface-variant flex items-start gap-1">
                    <span class="material-symbols-outlined text-[16px] mt-0.5">pin_drop</span>
                    <span>{{ $loc->latitude }}, {{ $loc->longitude }}</span>
                </div>
                
                <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                    <form action="{{ route('lokasi.toggle', $loc) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-sm font-sans font-bold {{ $loc->is_active ? 'text-on-surface-variant' : 'text-tertiary' }} hover:underline">
                            {{ $loc->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <form action="{{ route('lokasi.destroy', $loc) }}" method="POST" onsubmit="return confirm('Hapus lokasi ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm font-sans font-bold text-error hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-surface-container-low border border-outline-variant border-dashed rounded-xl p-12 text-center flex flex-col items-center justify-center">
            <span class="material-symbols-outlined text-6xl text-outline mb-4">location_off</span>
            <h3 class="text-xl font-display font-bold text-on-surface mb-2">Belum ada lokasi</h3>
            <p class="font-sans text-on-surface-variant mb-6">Tambahkan lokasi tempat tinggal atau keluarga Anda untuk mulai menerima notifikasi peringatan dini.</p>
            <button onclick="document.getElementById('modalTambahLokasi').classList.remove('hidden')" class="bg-primary text-on-primary font-sans font-bold px-6 py-3 rounded-lg shadow-sm hover:opacity-90">
                Tambah Lokasi Pertama
            </button>
        </div>
    @endforelse
</div>

<!-- Modal Tambah Lokasi -->
<div id="modalTambahLokasi" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('modalTambahLokasi').classList.add('hidden')"></div>
    <div class="bg-surface rounded-2xl shadow-xl border border-outline-variant w-full max-w-2xl relative z-10 flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center">
            <h2 class="text-2xl font-display font-bold text-on-surface">Tambah Titik Pantauan</h2>
            <button onclick="document.getElementById('modalTambahLokasi').classList.add('hidden')" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form action="{{ route('lokasi.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <div class="p-6 overflow-y-auto flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Form Inputs -->
                <div class="space-y-4">
                    <div>
                        <label for="nama_lokasi" class="block font-sans font-bold text-sm text-on-surface mb-1">Nama Tempat</label>
                        <input id="nama_lokasi" name="nama_lokasi" type="text" required placeholder="Cth: Rumah, Kantor, Sekolah Anak"
                               class="rounded-lg block w-full px-3 py-2 border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="latitude" class="block font-sans font-bold text-sm text-on-surface mb-1">Latitude</label>
                            <input id="latitude" name="latitude" type="number" step="any" required id="lat_input"
                                   class="rounded-lg block w-full px-3 py-2 border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-highest" readonly>
                        </div>
                        <div>
                            <label for="longitude" class="block font-sans font-bold text-sm text-on-surface mb-1">Longitude</label>
                            <input id="longitude" name="longitude" type="number" step="any" required id="lng_input"
                                   class="rounded-lg block w-full px-3 py-2 border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary bg-surface-container-highest" readonly>
                        </div>
                    </div>

                    <div>
                        <label for="radius_km" class="block font-sans font-bold text-sm text-on-surface mb-1 flex justify-between">
                            <span>Radius Peringatan (km)</span>
                            <span id="radius_val" class="text-primary font-bold">50 km</span>
                        </label>
                        <input id="radius_km" name="radius_km" type="range" min="5" max="200" value="50" class="w-full mt-2" 
                               oninput="document.getElementById('radius_val').innerText = this.value + ' km'; updateRadius(this.value)">
                        <p class="text-xs text-on-surface-variant mt-1">Sistem akan memberi tahu jika ada bencana dalam radius ini.</p>
                    </div>
                </div>

                <!-- Map Selector -->
                <div class="flex flex-col gap-2">
                    <label class="block font-sans font-bold text-sm text-on-surface">Pilih dari Peta</label>
                    <div id="pickerMap" class="w-full h-64 rounded-lg border border-outline-variant relative z-10"></div>
                    <p class="text-xs text-on-surface-variant">Klik pada peta atau geser pin untuk menentukan koordinat presisi.</p>
                </div>
            </div>
            
            <div class="p-6 border-t border-outline-variant bg-surface-container flex justify-end gap-3 mt-auto">
                <button type="button" onclick="document.getElementById('modalTambahLokasi').classList.add('hidden')" class="px-4 py-2 font-sans font-bold text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-6 py-2 font-sans font-bold text-on-primary bg-primary hover:bg-on-primary-fixed-variant rounded-lg shadow-sm transition-colors">Simpan Lokasi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let pickerMap, marker, circle;

    function initPickerMap() {
        if (pickerMap) return; // already initialized

        const defaultLat = -2.5489;
        const defaultLng = 118.0149;

        pickerMap = L.map('pickerMap').setView([defaultLat, defaultLng], 5);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OSM contributors'
        }).addTo(pickerMap);

        // Try to get user's location
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                updateMarkerPosition(lat, lng);
                pickerMap.setView([lat, lng], 10);
            });
        }

        pickerMap.on('click', function(e) {
            updateMarkerPosition(e.latlng.lat, e.latlng.lng);
        });
    }

    function updateMarkerPosition(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);

        const radius = document.getElementById('radius_km').value * 1000;

        if (marker) {
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {draggable: true}).addTo(pickerMap);
            circle = L.circle([lat, lng], {
                color: 'var(--color-primary)',
                fillColor: 'var(--color-primary)',
                fillOpacity: 0.2,
                radius: radius
            }).addTo(pickerMap);

            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateMarkerPosition(pos.lat, pos.lng);
            });
        }
    }

    window.updateRadius = function(km) {
        if(circle) {
            circle.setRadius(km * 1000);
        }
    }

    // Initialize map when modal is opened
    document.querySelector('[onclick="document.getElementById(\'modalTambahLokasi\').classList.remove(\'hidden\')"]').addEventListener('click', () => {
        setTimeout(initPickerMap, 100); // Wait for modal to render
    });
</script>
@endpush
