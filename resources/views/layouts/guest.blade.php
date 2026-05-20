@extends('layouts.base')

@section('body')
{{-- Guest Navbar --}}
<header class="bg-surface text-on-surface sticky top-0 z-50 border-b border-outline-variant shadow-sm">
    <div class="flex justify-between items-center w-full px-4 md:px-10 h-20">
        <a href="{{ route('landing') }}" class="text-3xl md:text-[32px] font-display font-extrabold tracking-tight text-primary leading-none">
            NusaAlert
        </a>
        <nav class="hidden md:flex gap-6 items-center">
            <a class="text-lg font-display font-semibold text-on-surface border-b-2 border-primary px-2 py-1" href="{{ route('landing') }}">Beranda</a>
            <a class="text-lg font-display font-semibold text-on-surface-variant hover:text-primary px-2 py-1 transition-colors" href={{route('peta')}}>Peta</a>
        </nav>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-primary text-on-primary font-sans font-bold text-sm px-5 py-2.5 rounded-lg shadow-sm hover:opacity-90 transition-opacity tracking-wide">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-primary text-on-primary font-sans font-bold text-sm px-5 py-2.5 rounded-lg shadow-sm hover:opacity-90 transition-opacity tracking-wide">
                    Masuk
                </a>
            @endauth
        </div>
    </div>
</header>

<main class="grow">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-surface text-on-surface border-t border-outline-variant">
    <div class="w-full py-8 px-4 md:px-10 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="flex flex-col gap-4">
            <div class="font-display text-lg font-bold text-primary">NusaAlert</div>
            <p class="text-base text-on-surface-variant max-w-sm">
                Sistem pemantauan dan peringatan dini bencana terpadu untuk wilayah Republik Indonesia.
            </p>
            <p class="text-base text-on-surface-variant mt-auto">
                © {{ date('Y') }} NusaAlert. Data real-time bersumber dari BMKG & BNPB.
            </p>
        </div>
        {{-- <div class="grid grid-cols-2 gap-4 md:justify-end">
            <div class="flex flex-col gap-3">
                <h4 class="font-sans font-bold text-sm text-on-surface mb-2 tracking-wide">Layanan</h4>
                <a class="font-sans font-bold text-sm text-on-surface-variant hover:text-primary transition-colors" href="{{ route('panduan-keselamatan') }}">Panduan Keselamatan</a>
                <a class="font-sans font-bold text-sm text-on-surface-variant hover:text-primary transition-colors" href="{{ route('kontak-darurat') }}">Kontak Darurat</a>
                <a class="font-sans font-bold text-sm text-on-surface-variant hover:text-primary transition-colors" href="{{ route('laporan.index') }}">Lapor Bencana</a>
            </div>
            <div class="flex flex-col gap-3">
                <h4 class="font-sans font-bold text-sm text-on-surface mb-2 tracking-wide">Informasi</h4>
                <a class="font-sans font-bold text-sm text-on-surface-variant hover:text-primary transition-colors" href="{{ route('kebijakan-privasi') }}">Kebijakan Privasi</a>
                <a class="font-sans font-bold text-sm text-on-surface-variant hover:text-primary transition-colors" href="#">Syarat Ketentuan</a>
                <a class="font-sans font-bold text-sm text-on-surface-variant hover:text-primary transition-colors" href="#">API Developer</a>
            </div>
        </div> --}}
    </div>
</footer>
@endsection
