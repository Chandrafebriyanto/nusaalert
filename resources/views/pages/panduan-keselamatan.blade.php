@extends('layouts.guest')

@section('title', 'Panduan Keselamatan')

@section('content')
<section class="w-full px-4 md:px-10 py-16 bg-surface border-b border-outline-variant">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('landing') }}" class="text-sm font-sans font-bold text-primary hover:underline flex items-center gap-1 mb-4">
                <span class="material-symbols-outlined text-lg">arrow_back</span> Kembali ke Beranda
            </a>
            <h1 class="text-4xl md:text-5xl font-display font-extrabold text-on-surface mb-4">Panduan Keselamatan</h1>
            <p class="text-lg text-on-surface-variant font-sans">Panduan lengkap untuk menghadapi berbagai jenis bencana alam di Indonesia.</p>
        </div>

        <div class="flex flex-col gap-6">
            <!-- Gempa Bumi -->
            <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-outline-variant bg-surface-container flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">earthquake</span>
                    </div>
                    <h2 class="text-2xl font-display font-bold text-on-surface">Gempa Bumi</h2>
                </div>
                <div class="p-6 font-sans text-on-surface-variant">
                    <h3 class="font-bold text-on-surface mb-3 text-lg">Saat Terjadi Gempa:</h3>
                    <ul class="list-disc pl-6 space-y-2 mb-6">
                        <li><strong>Drop, Cover, Hold On</strong> — Jatuhkan diri, berlindung di bawah meja kokoh, pegang kaki meja.</li>
                        <li>Jauhi jendela, kaca, cermin, dan benda-benda berat yang bisa jatuh.</li>
                        <li>Jika di luar ruangan, jauhi gedung, pohon, dan tiang listrik.</li>
                        <li>Jika berkendara, berhenti di tempat terbuka, tetap di dalam kendaraan.</li>
                    </ul>
                    <h3 class="font-bold text-on-surface mb-3 text-lg">Setelah Gempa:</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Periksa diri sendiri dan orang di sekitar dari cedera.</li>
                        <li>Waspada terhadap gempa susulan.</li>
                        <li>Periksa saluran gas, listrik, dan air — matikan jika ada kerusakan.</li>
                        <li>Dengarkan informasi resmi dari BMKG melalui radio atau NusaAlert.</li>
                    </ul>
                </div>
            </div>

            <!-- Tsunami -->
            <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-outline-variant bg-surface-container flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">tsunami</span>
                    </div>
                    <h2 class="text-2xl font-display font-bold text-on-surface">Tsunami</h2>
                </div>
                <div class="p-6 font-sans text-on-surface-variant">
                    <h3 class="font-bold text-on-surface mb-3 text-lg">Tanda-Tanda Peringatan:</h3>
                    <ul class="list-disc pl-6 space-y-2 mb-6">
                        <li>Gempa kuat di area pesisir yang berlangsung lebih dari 20 detik.</li>
                        <li>Air laut surut secara drastis dan tiba-tiba.</li>
                        <li>Suara gemuruh dari arah laut.</li>
                    </ul>
                    <h3 class="font-bold text-on-surface mb-3 text-lg">Yang Harus Dilakukan:</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Segera evakuasi</strong> ke dataran tinggi minimal 30 meter di atas permukaan laut.</li>
                        <li>Jangan menunggu peringatan resmi — segera bergerak setelah merasakan gempa kuat.</li>
                        <li>Jauhi pantai, sungai, dan daerah rendah.</li>
                        <li>Jangan kembali sampai ada pernyataan aman dari BMKG/BNPB.</li>
                    </ul>
                </div>
            </div>

            <!-- Banjir -->
            <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-outline-variant bg-surface-container flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">flood</span>
                    </div>
                    <h2 class="text-2xl font-display font-bold text-on-surface">Banjir</h2>
                </div>
                <div class="p-6 font-sans text-on-surface-variant">
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Pindahkan dokumen penting dan peralatan elektronik ke tempat yang lebih tinggi.</li>
                        <li>Matikan aliran listrik jika air mulai masuk rumah.</li>
                        <li><strong>Jangan berjalan atau berkendara</strong> melalui genangan air banjir.</li>
                        <li>Siapkan tas darurat berisi makanan, air bersih, obat-obatan, dan senter.</li>
                        <li>Evakuasi ke tempat pengungsian yang telah ditentukan jika diminta.</li>
                    </ul>
                </div>
            </div>

            <!-- Cuaca Ekstrem -->
            <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-outline-variant bg-surface-container flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">thunderstorm</span>
                    </div>
                    <h2 class="text-2xl font-display font-bold text-on-surface">Cuaca Ekstrem</h2>
                </div>
                <div class="p-6 font-sans text-on-surface-variant">
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Tetap di dalam ruangan selama badai atau angin kencang.</li>
                        <li>Jauhi pohon besar, papan reklame, dan tiang listrik.</li>
                        <li>Amankan benda-benda di luar yang bisa terbang tertiup angin.</li>
                        <li>Hindari penggunaan telepon kabel saat ada petir.</li>
                        <li>Pantau informasi cuaca melalui BMKG atau NusaAlert.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
