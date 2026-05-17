@extends('layouts.guest')

@section('title', 'Kebijakan Privasi')

@section('content')
<section class="w-full px-4 md:px-10 py-16 bg-surface">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('landing') }}" class="text-sm font-sans font-bold text-primary hover:underline flex items-center gap-1 mb-4">
                <span class="material-symbols-outlined text-lg">arrow_back</span> Kembali ke Beranda
            </a>
            <h1 class="text-4xl md:text-5xl font-display font-extrabold text-on-surface mb-4">Kebijakan Privasi</h1>
            <p class="text-lg text-on-surface-variant font-sans">Terakhir diperbarui: {{ date('d F Y') }}</p>
        </div>

        <div class="bg-surface border border-outline-variant rounded-xl shadow-sm p-6 md:p-10 font-sans text-on-surface-variant space-y-8">

            <div>
                <h2 class="text-2xl font-display font-bold text-on-surface mb-4">1. Pendahuluan</h2>
                <p>NusaAlert ("kami") berkomitmen untuk melindungi privasi Anda. Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan layanan peringatan dini bencana kami.</p>
            </div>

            <div>
                <h2 class="text-2xl font-display font-bold text-on-surface mb-4">2. Informasi yang Kami Kumpulkan</h2>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Informasi Akun:</strong> Nama, email, nomor telepon saat Anda mendaftar.</li>
                    <li><strong>Data Lokasi:</strong> Koordinat lokasi yang Anda tambahkan secara sukarela untuk pemantauan (rumah, kantor, dll).</li>
                    <li><strong>Data Perangkat:</strong> Jenis browser, sistem operasi, dan informasi teknis untuk optimasi layanan.</li>
                    <li><strong>Laporan Komunitas:</strong> Informasi bencana yang Anda laporkan beserta foto (jika ada).</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-display font-bold text-on-surface mb-4">3. Penggunaan Data Lokasi</h2>
                <div class="bg-tertiary-fixed text-on-tertiary-fixed p-4 rounded-lg border border-tertiary mb-4 flex items-start gap-3">
                    <span class="material-symbols-outlined mt-0.5" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                    <p class="font-sans"><strong>Komitmen Kami:</strong> Kami TIDAK melacak pergerakan Anda secara real-time. Data lokasi yang Anda daftarkan hanya digunakan untuk mencocokkan dengan radius peringatan bencana.</p>
                </div>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Lokasi disimpan sebagai koordinat statis yang Anda tentukan sendiri.</li>
                    <li>Tidak ada pelacakan GPS di latar belakang.</li>
                    <li>Data lokasi hanya diproses di server kami untuk kalkulasi jarak bencana.</li>
                    <li>Anda dapat menghapus lokasi kapan saja dari dashboard.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-display font-bold text-on-surface mb-4">4. Keamanan Data</h2>
                <p>Kami menggunakan standar keamanan industri untuk melindungi data Anda:</p>
                <ul class="list-disc pl-6 space-y-2 mt-3">
                    <li>Enkripsi SSL/TLS untuk seluruh transmisi data.</li>
                    <li>Hashing password menggunakan bcrypt.</li>
                    <li>Akses database dibatasi hanya untuk personel berwenang.</li>
                    <li>Audit keamanan berkala.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-display font-bold text-on-surface mb-4">5. Berbagi Data</h2>
                <p>Kami <strong>tidak menjual</strong> data pribadi Anda kepada pihak ketiga. Data hanya dibagikan dalam kondisi:</p>
                <ul class="list-disc pl-6 space-y-2 mt-3">
                    <li>Untuk memenuhi kewajiban hukum (permintaan resmi dari pihak berwenang).</li>
                    <li>Laporan komunitas yang telah diverifikasi (tanpa data pribadi pelapor) untuk kepentingan keselamatan publik.</li>
                    <li>Data statistik anonim untuk peningkatan layanan.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-display font-bold text-on-surface mb-4">6. Hak Pengguna</h2>
                <p>Anda memiliki hak untuk:</p>
                <ul class="list-disc pl-6 space-y-2 mt-3">
                    <li>Mengakses dan memperbarui informasi pribadi Anda.</li>
                    <li>Menghapus akun dan seluruh data terkait.</li>
                    <li>Menonaktifkan atau menghapus lokasi pantauan.</li>
                    <li>Menolak notifikasi peringatan (tidak disarankan).</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-display font-bold text-on-surface mb-4">7. Kontak</h2>
                <p>Jika Anda memiliki pertanyaan mengenai kebijakan privasi ini, silakan hubungi kami melalui:</p>
                <p class="mt-2"><strong>Email:</strong> privacy@nusaalert.id</p>
            </div>
        </div>
    </div>
</section>
@endsection
