@extends('layouts.app')

@section('title', 'Riwayat Laporan')

@section('content')
<div class="flex justify-between items-end mb-6">
    <div>
        <h1 class="text-3xl font-display font-extrabold text-on-surface mb-2">Riwayat Peringatan</h1>
        <p class="text-lg text-on-surface-variant font-sans">Semua notifikasi bencana yang masuk dalam radius pantauan Anda.</p>
    </div>
    @if(auth()->user()->unreadAlerts()->count() > 0)
        <form action="{{ route('alerts.markAllRead') }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="bg-surface border-2 border-primary text-primary font-sans font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-primary-container transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined">done_all</span>
                Tandai Semua Dibaca
            </button>
        </form>
    @endif
</div>

<!-- Filters -->
<div class="bg-surface p-4 rounded-xl border border-outline-variant mb-6 shadow-sm flex flex-wrap gap-4 items-end">
    <form action="{{ route('alerts.index') }}" method="GET" class="flex flex-wrap gap-4 items-end w-full">
        <div class="flex-1 min-w-50">
            <label for="jenis" class="block text-sm font-sans font-bold text-on-surface mb-1">Jenis Bencana</label>
            <select name="jenis" id="jenis" class="w-full bg-surface-container-highest border border-outline-variant rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none font-sans text-on-surface">
                <option value="">Semua Bencana</option>
                <option value="gempa" {{ request('jenis') === 'gempa' ? 'selected' : '' }}>Gempa Bumi</option>
                <option value="tsunami" {{ request('jenis') === 'tsunami' ? 'selected' : '' }}>Tsunami</option>
                <option value="cuaca_ekstrem" {{ request('jenis') === 'cuaca_ekstrem' ? 'selected' : '' }}>Cuaca Ekstrem</option>
                <option value="banjir" {{ request('jenis') === 'banjir' ? 'selected' : '' }}>Banjir</option>
            </select>
        </div>
        <div class="flex-1 min-w-50">
            <label for="tanggal" class="block text-sm font-sans font-bold text-on-surface mb-1">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}" class="w-full bg-surface-container-highest border border-outline-variant rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none font-sans text-on-surface">
        </div>
        <div>
            <button type="submit" class="bg-secondary text-on-secondary font-sans font-bold px-6 py-2 rounded-lg hover:bg-secondary-container hover:text-on-secondary-container transition-colors h-10.5">
                Filter
            </button>
            @if(request()->hasAny(['jenis', 'tanggal']))
                <a href="{{ route('alerts.index') }}" class="ml-2 text-primary font-bold hover:underline text-sm font-sans">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Timeline / List -->
<div class="bg-surface rounded-xl border border-outline-variant shadow-sm overflow-hidden">
    @if($alerts->count() > 0)
        <div class="divide-y divide-outline-variant">
            @foreach($alerts as $alert)
                <div class="p-4 md:p-6 flex flex-col md:flex-row gap-6 hover:bg-surface-container-lowest transition-colors relative {{ $alert->status === 'sent' ? 'bg-primary-container/10' : '' }}">
                    @if($alert->status === 'sent')
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>
                    @endif
                    
                    <!-- Icon & Time -->
                    <div class="flex flex-row md:flex-col items-center md:items-start gap-4 md:w-48 shrink-0">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center shadow-sm 
                            {{ $alert->status === 'sent' ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface-variant' }}">
                            @php
                                $icon = 'warning';
                                if($alert->bencana->jenis_bencana === 'gempa') $icon = 'seismograph'; // or similar icon
                                if($alert->bencana->jenis_bencana === 'tsunami') $icon = 'tsunami';
                                if($alert->bencana->jenis_bencana === 'cuaca_ekstrem') $icon = 'thunderstorm';
                                if($alert->bencana->jenis_bencana === 'banjir') $icon = 'flood';
                            @endphp
                            <span class="material-symbols-outlined">{{ $icon }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-sans font-bold text-on-surface">{{ $alert->created_at->format('d M Y') }}</span>
                            <span class="font-sans text-sm text-on-surface-variant">{{ $alert->created_at->format('H:i') }} WIB</span>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="flex-1 flex flex-col gap-2">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="bg-surface-container-highest text-on-surface-variant text-xs font-sans font-bold px-2 py-1 rounded tracking-wide uppercase">
                                {{ $alert->bencana->jenis_bencana }}
                            </span>
                            @if($alert->bencana->magnitude)
                                <span class="bg-error-container text-on-error-container text-xs font-sans font-bold px-2 py-1 rounded">
                                    M {{ $alert->bencana->magnitude }}
                                </span>
                            @endif
                        </div>
                        <h3 class="font-display text-xl font-bold text-on-surface leading-snug">
                            {{ $alert->bencana->wilayah }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-6 mt-2">
                            <div class="flex items-center gap-2 text-sm font-sans text-on-surface-variant">
                                <span class="material-symbols-outlined text-[18px]">location_on</span>
                                <span>Terdeteksi {{ $alert->jarak_km }} km dari <strong>{{ $alert->lokasi->nama_lokasi }}</strong></span>
                            </div>
                            @if($alert->bencana->kedalaman_km)
                                <div class="flex items-center gap-2 text-sm font-sans text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">vertical_align_bottom</span>
                                    <span>Kedalaman {{ $alert->bencana->kedalaman_km }} km</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action -->
                    <div class="md:w-32 flex flex-row md:flex-col items-center md:items-end justify-between md:justify-start gap-4">
                        <span class="text-xs font-sans font-bold px-2 py-1 rounded-full border {{ $alert->status === 'read' ? 'border-outline text-on-surface-variant' : 'border-primary text-primary bg-primary-container' }}">
                            {{ $alert->status === 'read' ? 'Dibaca' : 'Baru' }}
                        </span>
                        
                        @if($alert->status === 'sent')
                            <form action="{{ route('alerts.read', $alert) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="p-2 text-primary hover:bg-primary-container rounded-full transition-colors tooltip" title="Tandai sudah dibaca">
                                    <span class="material-symbols-outlined">mark_email_read</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="p-4 border-t border-outline-variant bg-surface-container-lowest">
            {{ $alerts->links() }}
        </div>
    @else
        <div class="p-12 text-center flex flex-col items-center justify-center">
            <span class="material-symbols-outlined text-6xl text-outline mb-4">history</span>
            <h3 class="text-xl font-display font-bold text-on-surface mb-2">Tidak ada riwayat alert</h3>
            <p class="font-sans text-on-surface-variant">Belum ada peringatan bencana yang masuk dalam radius lokasi Anda dengan filter saat ini.</p>
        </div>
    @endif
</div>
@endsection
