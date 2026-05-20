@extends('layouts.guest')

@section('title', 'Pelajari Sistem')

@section('content')
<!-- Hero Section -->
<section class="relative w-full overflow-hidden bg-surface border-b border-outline-variant">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-tertiary/5"></div>
    </div>
    <div class="relative z-10 w-full px-4 md:px-10 py-16 md:py-24 max-w-4xl mx-auto text-center">
        <h1 class="text-4xl md:text-5xl font-display font-extrabold text-primary mb-4 leading-tight">Bagaimana NusaAlert Bekerja</h1>
        <p class="text-lg text-on-surface-variant max-w-2xl mx-auto font-sans">
            Sistem peringatan dini yang mengintegrasikan data lingkungan real-time dengan notifikasi personal untuk keselamatan Anda di area rawan bencana.
        </p>
    </div>
</section>

<!-- Alur Peringatan Dini -->
<section class="w-full px-4 md:px-10 py-16 bg-surface-container-lowest border-b border-outline-variant">
    <div class="max-w-5xl mx-auto">
        <div class="bg-surface rounded-xl shadow-sm border border-outline-variant p-6 md:p-10">
            <h2 class="text-3xl font-display font-bold text-on-surface mb-8 text-center">Alur Peringatan Dini</h2>

            <!-- Steps -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">sensors</span>
                    </div>
                    <h3 class="text-xl font-display font-bold text-on-surface mb-2">1. Deteksi Data</h3>
                    <p class="text-base text-on-surface-variant font-sans">
                        Sensor cuaca dan seismik dari berbagai lembaga resmi memantau kondisi lingkungan secara terus-menerus (24/7).
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">memory</span>
                    </div>
                    <h3 class="text-xl font-display font-bold text-on-surface mb-2">2. Pengolahan Cerdas</h3>
                    <p class="text-base text-on-surface-variant font-sans">
                        Sistem awan menganalisis data untuk menentukan radius ancaman dan mencocokkannya dengan lokasi terkini pengguna.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">notification_important</span>
                    </div>
                    <h3 class="text-xl font-display font-bold text-on-surface mb-2">3. Notifikasi Personal</h3>
                    <p class="text-base text-on-surface-variant font-sans">
                        Peringatan seketika dikirimkan ke perangkat pengguna yang berada di dalam radius bahaya, memberikan instruksi keselamatan yang jelas.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sumber Data Resmi -->
<section class="w-full px-4 md:px-10 py-16 bg-surface border-b border-outline-variant">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-display font-bold text-on-surface mb-2">Sumber Data Resmi</h2>
        <div class="w-24 h-1 bg-primary rounded-full mb-8"></div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- BMKG -->
            <div class="bg-surface border border-outline-variant p-6 rounded-xl shadow-sm flex items-start gap-4 hover:shadow-md transition-shadow group">
                <div class="bg-surface-container-highest p-3 rounded-lg text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">tsunami</span>
                </div>
                <div>
                    <h4 class="text-lg font-display font-bold text-on-surface mb-1">BMKG</h4>
                    <p class="text-sm text-on-surface-variant font-sans">Data gempa bumi, tsunami, dan cuaca ekstrem secara real-time.</p>
                </div>
            </div>

            <!-- BNPB -->
            <div class="bg-surface border border-outline-variant p-6 rounded-xl shadow-sm flex items-start gap-4 hover:shadow-md transition-shadow group">
                <div class="bg-surface-container-highest p-3 rounded-lg text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                </div>
                <div>
                    <h4 class="text-lg font-display font-bold text-on-surface mb-1">BNPB</h4>
                    <p class="text-sm text-on-surface-variant font-sans">Informasi status siaga bencana nasional dan laporan kerusakan wilayah.</p>
                </div>
            </div>

            <!-- OpenWeatherMap -->
            <div class="bg-surface border border-outline-variant p-6 rounded-xl shadow-sm flex items-start gap-4 hover:shadow-md transition-shadow group">
                <div class="bg-surface-container-highest p-3 rounded-lg text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">partly_cloudy_day</span>
                </div>
                <div>
                    <h4 class="text-lg font-display font-bold text-on-surface mb-1">OpenWeatherMap</h4>
                    <p class="text-sm text-on-surface-variant font-sans">Kondisi atmosfer hiperlokal untuk prediksi banjir dan badai skala kecil.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
{{-- <section class="w-full px-4 md:px-10 py-16 bg-surface-container-lowest">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl font-display font-bold text-on-surface mb-8 text-center">Pertanyaan Umum</h2>

        <div class="flex flex-col gap-4">
            <details class="group bg-surface rounded-xl border border-outline-variant shadow-sm overflow-hidden">
                <summary class="flex cursor-pointer items-center justify-between gap-4 p-5 text-on-surface font-display font-bold text-lg select-none list-none [&::-webkit-details-marker]:hidden">
                    Apakah lokasi saya dilacak setiap saat?
                    <span class="material-symbols-outlined text-on-surface-variant transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </summary>
                <div class="px-5 pb-5 font-sans text-on-surface-variant border-t border-outline-variant pt-4">
                    Kami mengutamakan privasi Anda. Lokasi hanya diakses secara lokal di perangkat Anda untuk mencocokkan dengan radius peringatan darurat. Kami tidak menyimpan histori perjalanan Anda di server kami.
                </div>
            </details>

            <details class="group bg-surface rounded-xl border border-outline-variant shadow-sm overflow-hidden">
                <summary class="flex cursor-pointer items-center justify-between gap-4 p-5 text-on-surface font-display font-bold text-lg select-none list-none [&::-webkit-details-marker]:hidden">
                    Seberapa cepat notifikasi tiba setelah gempa?
                    <span class="material-symbols-outlined text-on-surface-variant transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </summary>
                <div class="px-5 pb-5 font-sans text-on-surface-variant border-t border-outline-variant pt-4">
                    Sistem kami dirancang untuk meneruskan peringatan dari sensor BMKG dalam waktu kurang dari 5 detik setelah data diterima oleh server kami, meminimalisir jeda waktu krusial.
                </div>
            </details>

            <details class="group bg-surface rounded-xl border border-outline-variant shadow-sm overflow-hidden">
                <summary class="flex cursor-pointer items-center justify-between gap-4 p-5 text-on-surface font-display font-bold text-lg select-none list-none [&::-webkit-details-marker]:hidden">
                    Bagaimana cara menambahkan lokasi yang ingin dipantau?
                    <span class="material-symbols-outlined text-on-surface-variant transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </summary>
                <div class="px-5 pb-5 font-sans text-on-surface-variant border-t border-outline-variant pt-4">
                    Setelah mendaftar dan login, buka menu "Kelola Lokasi" di sidebar. Klik "Tambah Lokasi", pilih titik di peta atau masukkan koordinat, atur radius peringatan, dan simpan. Anda bisa menambahkan beberapa lokasi seperti rumah, kantor, atau sekolah anak.
                </div>
            </details>

            <details class="group bg-surface rounded-xl border border-outline-variant shadow-sm overflow-hidden">
                <summary class="flex cursor-pointer items-center justify-between gap-4 p-5 text-on-surface font-display font-bold text-lg select-none list-none [&::-webkit-details-marker]:hidden">
                    Jenis bencana apa saja yang dipantau?
                    <span class="material-symbols-outlined text-on-surface-variant transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </summary>
                <div class="px-5 pb-5 font-sans text-on-surface-variant border-t border-outline-variant pt-4">
                    NusaAlert memantau gempa bumi, tsunami, banjir, cuaca ekstrem, dan erupsi gunung api. Data bersumber dari BMKG, BNPB, dan OpenWeatherMap untuk memastikan cakupan yang komprehensif.
                </div>
            </details>

            <details class="group bg-surface rounded-xl border border-outline-variant shadow-sm overflow-hidden">
                <summary class="flex cursor-pointer items-center justify-between gap-4 p-5 text-on-surface font-display font-bold text-lg select-none list-none [&::-webkit-details-marker]:hidden">
                    Apakah layanan NusaAlert gratis?
                    <span class="material-symbols-outlined text-on-surface-variant transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </summary>
                <div class="px-5 pb-5 font-sans text-on-surface-variant border-t border-outline-variant pt-4">
                    Ya, NusaAlert sepenuhnya gratis untuk digunakan oleh seluruh masyarakat Indonesia. Kami percaya bahwa keselamatan dari bencana alam adalah hak semua orang, tanpa terkecuali.
                </div>
            </details>
        </div>
    </div>
</section> --}}
@endsection
