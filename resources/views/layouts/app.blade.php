@extends('layouts.base')

@section('body')
{{-- Mobile TopAppBar --}}
<header class="lg:hidden bg-primary text-on-primary w-full sticky top-0 z-50 border-b-4 border-primary-fixed-dim shadow-lg flex justify-between items-center px-4 h-20">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">shield</span>
        <span class="text-2xl font-display font-extrabold tracking-tight">NusaAlert</span>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('alerts.index') }}" class="hover:bg-primary-fixed-dim/20 transition-colors p-2 rounded-full relative">
            <span class="material-symbols-outlined">notifications_active</span>
            @php $unread = auth()->user()->unreadAlerts()->count(); @endphp
            @if($unread > 0)
                <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-error text-on-error text-xs font-bold rounded-full flex items-center justify-center">{{ $unread > 9 ? '9+' : $unread }}</span>
            @endif
        </a>
        <button onclick="document.getElementById('mobileSidebar').classList.toggle('hidden')" class="hover:bg-primary-fixed-dim/20 transition-colors p-2 rounded-full">
            <span class="material-symbols-outlined">menu</span>
        </button>
    </div>
</header>

{{-- Mobile Sidebar Overlay --}}
<div id="mobileSidebar" class="hidden lg:hidden fixed inset-0 z-60">
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('mobileSidebar').classList.add('hidden')"></div>
    <nav class="absolute left-0 top-0 h-full w-72 bg-surface shadow-xl p-4 flex flex-col overflow-y-auto">
        @include('components.sidebar-content')
    </nav>
</div>

{{-- Desktop Sidebar --}}
<aside class="hidden lg:flex flex-col h-screen p-4 fixed left-0 top-0 bg-surface border-r border-outline-variant shadow-md w-72 z-40 justify-between">
    @include('components.sidebar-content')
</aside>

{{-- Main Content --}}
<main class="flex-1 lg:ml-72 flex flex-col min-h-screen">
    <div class="p-4 lg:p-10 flex-1 flex flex-col gap-6">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-tertiary-fixed text-on-tertiary-fixed p-4 rounded-lg flex items-center gap-2 shadow-sm border border-tertiary">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                <span class="font-sans font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-error-container text-on-error-container p-4 rounded-lg flex flex-col gap-1 shadow-sm border border-error">
                <div class="flex items-center gap-2 font-bold">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">error</span>
                    Terjadi kesalahan:
                </div>
                <ul class="list-disc pl-6 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    {{-- Footer --}}
    <footer class="w-full py-8 px-4 md:px-10 grid grid-cols-1 md:grid-cols-2 gap-6 bg-surface-container-highest border-t border-outline-variant mt-auto">
        <div class="flex flex-col gap-2">
            <span class="font-display text-lg font-bold text-primary">NusaAlert</span>
            <p class="text-base text-on-surface">© {{ date('Y') }} NusaAlert. Data real-time bersumber dari BMKG & BNPB.</p>
        </div>
        {{-- <div class="flex flex-wrap md:justify-end gap-x-6 gap-y-2">
            <a class="font-sans font-bold text-sm text-on-surface-variant hover:text-primary transition-colors" href="{{ route('panduan-keselamatan') }}">Panduan Keselamatan</a>
            <a class="font-sans font-bold text-sm text-on-surface-variant hover:text-primary transition-colors" href="{{ route('kebijakan-privasi') }}">Kebijakan Privasi</a>
            <a class="font-sans font-bold text-sm text-on-surface-variant hover:text-primary transition-colors" href="{{ route('kontak-darurat') }}">Kontak Darurat</a>
        </div> --}}
    </footer>
</main>

{{-- Notification Popup System --}}
@include('components.notification-popup')
@endsection
