@extends('layouts.app')

@section('title', 'Laporan Komunitas')

@section('content')
<div class="flex justify-between items-end mb-6">
    <div>
        <h1 class="text-3xl font-display font-extrabold text-on-surface mb-2">Laporan Komunitas</h1>
        <p class="text-lg text-on-surface-variant font-sans">Informasi bencana dari warga sekitar. Pantau dan laporkan kejadian di area Anda.</p>
    </div>
    <button onclick="document.getElementById('modalBuatLaporan').classList.remove('hidden')" class="bg-primary text-on-primary font-sans font-bold px-4 py-2 rounded-lg shadow-sm hover:opacity-90 flex items-center gap-2">
        <span class="material-symbols-outlined">add_comment</span>
        Buat Laporan
    </button>
</div>

<!-- Tabs -->
<div class="border-b border-outline-variant mb-6 flex gap-6">
    <button onclick="switchTab('verified')" id="tab-verified" class="pb-3 border-b-2 font-sans font-bold text-primary border-primary">Laporan Terverifikasi</button>
    <button onclick="switchTab('pending')" id="tab-pending" class="pb-3 border-b-2 font-sans font-bold text-on-surface-variant border-transparent hover:text-on-surface">Menunggu Verifikasi</button>
</div>

<!-- Verified Reports -->
<div id="content-verified" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($laporanVerified as $laporan)
        <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm flex flex-col">
            @if($laporan->foto_url)
                <img src="{{ asset('storage/' . $laporan->foto_url) }}" alt="Foto Bencana" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-surface-container-high flex items-center justify-center text-on-surface-variant opacity-50">
                    <span class="material-symbols-outlined text-5xl">image_not_supported</span>
                </div>
            @endif
            
            <div class="p-5 flex flex-col gap-3 flex-1">
                <div class="flex justify-between items-start">
                    <span class="bg-tertiary-container text-on-tertiary-container text-xs font-sans font-bold px-2 py-1 rounded tracking-wide uppercase">
                        {{ $laporan->jenis_bencana }}
                    </span>
                    <span class="text-xs font-sans text-on-surface-variant flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        {{ $laporan->created_at->diffForHumans() }}
                    </span>
                </div>
                
                <h3 class="text-lg font-display font-bold text-on-surface">{{ $laporan->wilayah ?? 'Lokasi tidak spesifik' }}</h3>
                <p class="text-sm font-sans text-on-surface-variant line-clamp-3">{{ $laporan->deskripsi }}</p>
                
                <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-secondary-container rounded-full flex items-center justify-center text-on-secondary-container">
                            <span class="material-symbols-outlined text-[14px]">person</span>
                        </div>
                        <span class="text-xs font-sans font-bold text-on-surface">{{ $laporan->user->name }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                    <a href="https://maps.google.com/?q={{ $laporan->latitude }},{{ $laporan->longitude }}" target="_blank" class="text-primary hover:text-primary-container tooltip" title="Buka di Gmaps">
                        <span class="material-symbols-outlined">map</span>
                    </a>
                    @if(auth()->user()->hasRole('admin'))
                    <form action="{{route('admin.laporan.destroy', $laporan->id)}}" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-error hover:text-error-container tooltip" title="Hapus Laporan">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </form>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-surface-container-lowest border border-outline-variant border-dashed rounded-xl p-12 text-center flex flex-col items-center justify-center">
            <span class="material-symbols-outlined text-5xl text-outline mb-2">assignment_turned_in</span>
            <p class="font-sans text-on-surface-variant">Belum ada laporan yang terverifikasi saat ini.</p>
        </div>
    @endforelse
</div>

<!-- Pending Reports -->
<div id="content-pending" class="hidden grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($laporanPending as $laporan)
        <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm flex flex-col opacity-80">
            <div class="p-5 flex flex-col gap-3 flex-1">
                <div class="flex justify-between items-start">
                    <span class="bg-surface-container-highest text-on-surface-variant text-xs font-sans font-bold px-2 py-1 rounded tracking-wide uppercase">
                        {{ $laporan->jenis_bencana }}
                    </span>
                    <span class="bg-error-container/50 text-error text-xs font-sans font-bold px-2 py-1 rounded">Pending</span>
                </div>
                
                <h3 class="text-lg font-display font-bold text-on-surface">{{ $laporan->wilayah ?? 'Lokasi tidak spesifik' }}</h3>
                <p class="text-sm font-sans text-on-surface-variant line-clamp-3">{{ $laporan->deskripsi }}</p>
                
                <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                    <span class="text-xs font-sans text-on-surface-variant">{{ $laporan->created_at->format('d/m/Y H:i') }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-sans font-bold text-on-surface">Oleh: {{ $laporan->user->name }}</span>
                        @if(auth()->user()->hasRole('admin'))
                            <div class="flex gap-2 ml-2 border-l border-outline-variant pl-2">
                                <form action="{{ route('admin.laporan.verify', $laporan) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="bg-tertiary text-on-tertiary font-sans font-bold text-xs px-2 py-1 rounded hover:opacity-90 tooltip" title="Verifikasi">
                                        <span class="material-symbols-outlined text-[14px]">check</span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.laporan.reject', $laporan) }}" method="POST" onsubmit="return confirm('Tolak laporan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-error text-on-error font-sans font-bold text-xs px-2 py-1 rounded hover:opacity-90 tooltip" title="Tolak">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-surface-container-lowest border border-outline-variant border-dashed rounded-xl p-12 text-center flex flex-col items-center justify-center">
            <span class="material-symbols-outlined text-5xl text-outline mb-2">pending_actions</span>
            <p class="font-sans text-on-surface-variant">Tidak ada laporan yang menunggu verifikasi.</p>
        </div>
    @endforelse
</div>

<!-- Modal Buat Laporan -->
<div id="modalBuatLaporan" class="hidden fixed inset-0 z-100 items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('modalBuatLaporan').classList.add('hidden')"></div>
    <div class="bg-surface rounded-2xl shadow-xl border border-outline-variant w-full max-w-lg relative z-10 flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center">
            <h2 class="text-2xl font-display font-bold text-on-surface">Buat Laporan Warga</h2>
            <button onclick="document.getElementById('modalBuatLaporan').classList.add('hidden')" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <div class="p-6 overflow-y-auto flex-1 space-y-4">
                <div>
                    <label for="jenis_bencana" class="block font-sans font-bold text-sm text-on-surface mb-1">Jenis Kejadian</label>
                    <select name="jenis_bencana" id="jenis_bencana" required class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none font-sans">
                        <option value="banjir">Banjir</option>
                        <option value="longsor">Tanah Longsor</option>
                        <option value="pohon_tumbang">Pohon Tumbang</option>
                        <option value="angin_kencang">Angin Kencang</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                
                <div>
                    <label for="deskripsi" class="block font-sans font-bold text-sm text-on-surface mb-1">Deskripsi Kejadian</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" required placeholder="Jelaskan kondisi saat ini..." class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none font-sans"></textarea>
                </div>

                <div>
                    <label for="wilayah" class="block font-sans font-bold text-sm text-on-surface mb-1">Patokan / Nama Jalan (Opsional)</label>
                    <input type="text" name="wilayah" id="wilayah" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none font-sans">
                </div>

                <div>
                    <label class="block font-sans font-bold text-sm text-on-surface mb-1">Lokasi Anda (Otomatis)</label>
                    <div class="flex gap-2">
                        <input type="text" name="latitude" id="lap_lat" required readonly placeholder="Latitude" class="w-1/2 bg-surface-container-highest border border-outline-variant rounded-lg px-3 py-2 font-sans text-sm">
                        <input type="text" name="longitude" id="lap_lng" required readonly placeholder="Longitude" class="w-1/2 bg-surface-container-highest border border-outline-variant rounded-lg px-3 py-2 font-sans text-sm">
                    </div>
                    <button type="button" onclick="getLocation()" class="mt-2 text-xs font-bold text-primary flex items-center gap-1 hover:underline">
                        <span class="material-symbols-outlined text-[14px]">my_location</span> Ambil Lokasi Saat Ini
                    </button>
                </div>

                <div>
                    <label for="foto" class="block font-sans font-bold text-sm text-on-surface mb-1">Foto Kondisi (Opsional)</label>
                    <input type="file" name="foto" id="foto" accept="image/*" class="w-full font-sans text-sm">
                </div>
            </div>
            
            <div class="p-6 border-t border-outline-variant bg-surface-container flex justify-end gap-3 mt-auto">
                <button type="button" onclick="document.getElementById('modalBuatLaporan').classList.add('hidden')" class="px-4 py-2 font-sans font-bold text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-6 py-2 font-sans font-bold text-on-primary bg-primary hover:bg-on-primary-fixed-variant rounded-lg shadow-sm transition-colors">Kirim Laporan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tab) {
        if(tab === 'verified') {
            document.getElementById('content-verified').classList.remove('hidden');
            document.getElementById('content-pending').classList.add('hidden');
            document.getElementById('tab-verified').classList.replace('text-on-surface-variant', 'text-primary');
            document.getElementById('tab-verified').classList.replace('border-transparent', 'border-primary');
            document.getElementById('tab-pending').classList.replace('text-primary', 'text-on-surface-variant');
            document.getElementById('tab-pending').classList.replace('border-primary', 'border-transparent');
        } else {
            document.getElementById('content-pending').classList.remove('hidden');
            document.getElementById('content-verified').classList.add('hidden');
            document.getElementById('tab-pending').classList.replace('text-on-surface-variant', 'text-primary');
            document.getElementById('tab-pending').classList.replace('border-transparent', 'border-primary');
            document.getElementById('tab-verified').classList.replace('text-primary', 'text-on-surface-variant');
            document.getElementById('tab-verified').classList.replace('border-primary', 'border-transparent');
        }
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('lap_lat').value = position.coords.latitude.toFixed(6);
                document.getElementById('lap_lng').value = position.coords.longitude.toFixed(6);
            }, function(error) {
                alert("Gagal mendapatkan lokasi. Pastikan izin lokasi diberikan.");
            });
        } else {
            alert("Geolocation tidak didukung oleh browser ini.");
        }
    }

    // Auto get location when modal opens
    document.querySelector('[onclick="document.getElementById(\'modalBuatLaporan\').classList.remove(\'hidden\')"]').addEventListener('click', getLocation);
</script>
@endpush
