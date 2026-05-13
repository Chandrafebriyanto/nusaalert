


{{-- Sidebar Content (shared between mobile and desktop) --}}
<div class="flex flex-col gap-8 h-full">
    {{-- Header --}}
    <div class="flex items-center gap-3 px-2 pt-2">
        <div class="w-11 h-11 bg-primary rounded-lg flex items-center justify-center text-on-primary shadow-sm">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">shield</span>
        </div>
        <div class="flex flex-col">
            <span class="font-display text-xl font-extrabold text-primary">NusaAlert</span>
            <span class="text-xs text-on-surface-variant">Vigilant & Responsive</span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex flex-col gap-1.5 grow">
        @php
            $currentRoute = request()->route()?->getName();
        @endphp

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-4 rounded-lg px-4 py-3 transition-all duration-200
                  {{ $currentRoute === 'dashboard' ? 'bg-primary-container text-on-primary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1' }}">
            <span class="material-symbols-outlined" @if($currentRoute === 'dashboard') style="font-variation-settings: 'FILL' 1;" @endif>dashboard</span>
            <span class="font-sans font-bold text-sm tracking-wide">Dashboard</span>
        </a>

        <a href="{{ route('lokasi.index') }}"
           class="flex items-center gap-4 rounded-lg px-4 py-3 transition-all duration-200
                  {{ $currentRoute === 'lokasi.index' ? 'bg-primary-container text-on-primary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1' }}">
            <span class="material-symbols-outlined" @if($currentRoute === 'lokasi.index') style="font-variation-settings: 'FILL' 1;" @endif>location_on</span>
            <span class="font-sans font-bold text-sm tracking-wide">Kelola Lokasi</span>
        </a>

        <a href="{{ route('alerts.index') }}"
           class="flex items-center gap-4 rounded-lg px-4 py-3 transition-all duration-200
                  {{ $currentRoute === 'alerts.index' ? 'bg-primary-container text-on-primary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1' }}">
            <span class="material-symbols-outlined" @if($currentRoute === 'alerts.index') style="font-variation-settings: 'FILL' 1;" @endif>history</span>
            <span class="font-sans font-bold text-sm tracking-wide">Riwayat Laporan</span>
            @php $unread = auth()->user()->unreadAlerts()->count(); @endphp
            @if($unread > 0)
                <span class="ml-auto bg-error text-on-error text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ $unread > 9 ? '9+' : $unread }}</span>
            @endif
        </a>

        <a href="{{ route('laporan.index') }}"
           class="flex items-center gap-4 rounded-lg px-4 py-3 transition-all duration-200
                  {{ $currentRoute === 'laporan.index' ? 'bg-primary-container text-on-primary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1' }}">
            <span class="material-symbols-outlined" @if($currentRoute === 'laporan.index') style="font-variation-settings: 'FILL' 1;" @endif>campaign</span>
            <span class="font-sans font-bold text-sm tracking-wide">Laporan Komunitas</span>
        </a>

        @if(auth()->user()->hasRole('admin'))
        <a href="{{ route('admin.index') }}"
           class="flex items-center gap-4 rounded-lg px-4 py-3 transition-all duration-200
                  {{ $currentRoute === 'admin.index' ? 'bg-primary-container text-on-primary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1' }}">
            <span class="material-symbols-outlined" @if($currentRoute === 'admin.index') style="font-variation-settings: 'FILL' 1;" @endif>admin_panel_settings</span>
            <span class="font-sans font-bold text-sm tracking-wide">Admin Panel</span>
        </a>
        @endif
    </nav>

    {{-- Bottom Actions --}}
    <div class="flex flex-col gap-3 mt-auto">
        <nav class="flex flex-col gap-1.5 border-t border-outline-variant pt-4">
            <div class="flex items-center gap-4 text-on-surface-variant px-4 py-3 rounded-lg">
                <span class="material-symbols-outlined">account_circle</span>
                <div class="flex flex-col">
                    <span class="font-sans font-bold text-sm">{{ auth()->user()->name }}</span>
                    <span class="text-xs text-on-surface-variant/70">{{ auth()->user()->getRoleNames()->first() ?? 'member' }}</span>
                </div>
            </div>
        </nav>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-4 text-on-surface-variant hover:bg-surface-container-high rounded-lg px-4 py-3 transition-all duration-200 hover:translate-x-1">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-sans font-bold text-sm tracking-wide">Keluar</span>
            </button>
        </form>
    </div>
</div>
