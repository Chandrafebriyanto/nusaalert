@extends('layouts.guest')

@section('title', 'Kontak Darurat')

@section('content')
<section class="w-full px-4 md:px-10 py-16 bg-surface">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('landing') }}" class="text-sm font-sans font-bold text-primary hover:underline flex items-center gap-1 mb-4">
                <span class="material-symbols-outlined text-lg">arrow_back</span> Kembali ke Beranda
            </a>
            <h1 class="text-4xl md:text-5xl font-display font-extrabold text-on-surface mb-4">Kontak Darurat</h1>
            <p class="text-lg text-on-surface-variant font-sans">Nomor penting yang perlu Anda ketahui saat terjadi bencana atau keadaan darurat.</p>
        </div>

        <!-- Emergency Notice -->
        <div class="bg-error-container text-on-error-container p-6 rounded-xl border border-error mb-8 flex items-start gap-4">
            <span class="material-symbols-outlined text-3xl mt-1" style="font-variation-settings: 'FILL' 1;">emergency</span>
            <div>
                <h2 class="font-display font-bold text-xl mb-2">Dalam Keadaan Darurat</h2>
                <p class="font-sans text-lg">Hubungi <strong class="text-2xl">112</strong> — Nomor Darurat Nasional Indonesia</p>
                <p class="font-sans text-sm mt-1 opacity-80">Layanan ini terhubung ke Polisi, Ambulans, dan Pemadam Kebakaran.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- BNPB -->
            <div class="bg-surface border border-outline-variant rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">emergency_home</span>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-on-surface">BNPB</h3>
                        <p class="text-sm text-on-surface-variant font-sans">Badan Nasional Penanggulangan Bencana</p>
                    </div>
                </div>
                <div class="space-y-2 font-sans">
                    <p class="flex items-center gap-2 text-on-surface"><span class="material-symbols-outlined text-lg text-primary">call</span> <strong>117</strong></p>
                    <p class="flex items-center gap-2 text-on-surface"><span class="material-symbols-outlined text-lg text-primary">call</span> (021) 29827444</p>
                    <p class="flex items-center gap-2 text-sm text-on-surface-variant"><span class="material-symbols-outlined text-lg text-primary">language</span> www.bnpb.go.id</p>
                </div>
            </div>

            <!-- BMKG -->
            <div class="bg-surface border border-outline-variant rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">sensors</span>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-on-surface">BMKG</h3>
                        <p class="text-sm text-on-surface-variant font-sans">Badan Meteorologi, Klimatologi, dan Geofisika</p>
                    </div>
                </div>
                <div class="space-y-2 font-sans">
                    <p class="flex items-center gap-2 text-on-surface"><span class="material-symbols-outlined text-lg text-primary">call</span> (021) 65866230</p>
                    <p class="flex items-center gap-2 text-on-surface"><span class="material-symbols-outlined text-lg text-primary">call</span> (021) 65866231</p>
                    <p class="flex items-center gap-2 text-sm text-on-surface-variant"><span class="material-symbols-outlined text-lg text-primary">language</span> www.bmkg.go.id</p>
                </div>
            </div>

            <!-- Basarnas -->
            <div class="bg-surface border border-outline-variant rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">helicopter</span>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-on-surface">BASARNAS</h3>
                        <p class="text-sm text-on-surface-variant font-sans">Badan SAR Nasional</p>
                    </div>
                </div>
                <div class="space-y-2 font-sans">
                    <p class="flex items-center gap-2 text-on-surface"><span class="material-symbols-outlined text-lg text-primary">call</span> <strong>115</strong></p>
                    <p class="flex items-center gap-2 text-on-surface"><span class="material-symbols-outlined text-lg text-primary">call</span> (021) 3483-2881</p>
                    <p class="flex items-center gap-2 text-sm text-on-surface-variant"><span class="material-symbols-outlined text-lg text-primary">language</span> www.basarnas.go.id</p>
                </div>
            </div>

            <!-- PMI -->
            <div class="bg-surface border border-outline-variant rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">local_hospital</span>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-on-surface">PMI</h3>
                        <p class="text-sm text-on-surface-variant font-sans">Palang Merah Indonesia</p>
                    </div>
                </div>
                <div class="space-y-2 font-sans">
                    <p class="flex items-center gap-2 text-on-surface"><span class="material-symbols-outlined text-lg text-primary">call</span> (021) 7992325</p>
                    <p class="flex items-center gap-2 text-sm text-on-surface-variant"><span class="material-symbols-outlined text-lg text-primary">language</span> www.pmi.or.id</p>
                </div>
            </div>

            <!-- Polisi -->
            <div class="bg-surface border border-outline-variant rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">local_police</span>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-on-surface">Kepolisian RI</h3>
                        <p class="text-sm text-on-surface-variant font-sans">Polri</p>
                    </div>
                </div>
                <div class="space-y-2 font-sans">
                    <p class="flex items-center gap-2 text-on-surface"><span class="material-symbols-outlined text-lg text-primary">call</span> <strong>110</strong></p>
                </div>
            </div>

            <!-- Pemadam Kebakaran -->
            <div class="bg-surface border border-outline-variant rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">fire_truck</span>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-on-surface">Pemadam Kebakaran</h3>
                        <p class="text-sm text-on-surface-variant font-sans">Damkar</p>
                    </div>
                </div>
                <div class="space-y-2 font-sans">
                    <p class="flex items-center gap-2 text-on-surface"><span class="material-symbols-outlined text-lg text-primary">call</span> <strong>113</strong></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
