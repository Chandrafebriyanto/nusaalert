# 🌋 NusaAlert — Sistem Peringatan Dini Bencana Alam Personal
### Dokumentasi Project Pemrograman API — Laravel

---

## 1. LATAR BELAKANG

Indonesia merupakan salah satu negara dengan tingkat kerawanan bencana alam tertinggi di dunia. Secara geografis, Indonesia terletak di pertemuan tiga lempeng tektonik besar, yaitu Lempeng Indo-Australia, Lempeng Eurasia, dan Lempeng Pasifik — yang dikenal sebagai *Ring of Fire* (Cincin Api Pasifik). Kondisi ini menjadikan Indonesia sangat rentan terhadap berbagai jenis bencana alam, mulai dari gempa bumi, tsunami, hingga letusan gunung berapi.

Berdasarkan data dari Badan Meteorologi, Klimatologi, dan Geofisika (BMKG), Indonesia mencatat rata-rata **lebih dari 6.000 kejadian gempa bumi setiap tahunnya**, dengan sekitar 500 di antaranya tergolong dalam kategori yang dapat dirasakan oleh masyarakat. Sementara itu, Badan Nasional Penanggulangan Bencana (BNPB) mencatat bahwa pada tahun 2022 terjadi **3.544 kejadian bencana alam** di seluruh Indonesia, menyebabkan lebih dari 3,5 juta jiwa terdampak.

Salah satu permasalahan kritis yang dihadapi masyarakat Indonesia adalah **keterlambatan akses informasi bencana**. Meskipun BMKG telah memiliki sistem deteksi dan peringatan dini yang canggih, informasi tersebut seringkali tidak sampai secara cepat dan mudah dipahami oleh masyarakat umum. Masyarakat di daerah terpencil, misalnya, sering kali baru mengetahui informasi bencana melalui media sosial atau dari mulut ke mulut — yang berpotensi menyebarkan informasi yang tidak akurat.

Kasus Gempa Cianjur pada November 2022 (M5,6) yang menewaskan 335 orang dan Tsunami Selat Sunda pada Desember 2018 yang menewaskan 437 orang menjadi bukti nyata bahwa **gap antara data ilmiah yang dimiliki lembaga resmi dengan aksesibilitas informasi di tingkat masyarakat** masih sangat lebar.

**NusaAlert** hadir sebagai solusi berbasis teknologi yang menjembatani data resmi bencana dari BMKG dengan kebutuhan masyarakat akan informasi yang cepat, akurat, dan personal. Dengan memanfaatkan Laravel sebagai backend API dan mengintegrasikan berbagai API publik resmi pemerintah, NusaAlert bertujuan untuk membangun ekosistem informasi kebencanaan yang inklusif, real-time, dan dapat diandalkan.

---

## 2. RUMUSAN MASALAH

Berdasarkan latar belakang yang telah diuraikan, rumusan masalah dalam project NusaAlert adalah sebagai berikut:

1. **Bagaimana merancang sistem API berbasis Laravel** yang mampu mengintegrasikan data bencana real-time dari BMKG, OpenWeatherMap, dan Nominatim OpenStreetMap secara efisien?

2. **Bagaimana membangun sistem autentikasi yang aman dan berlapis** menggunakan JWT Authorization, Basic Auth, API Key, dan OAuth2 untuk melindungi data pengguna dan menjamin integritas akses sistem?

3. **Bagaimana merancang struktur database yang optimal** dengan relasi antar tabel yang tepat menggunakan fitur migrasi Laravel untuk mendukung sistem peringatan dini berbasis lokasi?

4. **Bagaimana mengimplementasikan sistem notifikasi personal** yang mampu mengirimkan peringatan bencana kepada pengguna berdasarkan radius lokasi tempat tinggal secara real-time?

5. **Bagaimana memastikan keakuratan dan relevansi informasi bencana** yang diterima pengguna agar tidak terjadi false alarm yang berlebihan?

---

## 3. TUJUAN

### Tujuan Umum
Membangun platform API berbasis Laravel yang mengintegrasikan data bencana alam real-time dari sumber resmi pemerintah Indonesia dan mendistribusikannya sebagai notifikasi personal kepada masyarakat berdasarkan lokasi mereka.

### Tujuan Khusus
1. Merancang dan mengimplementasikan RESTful API dengan Laravel yang mengintegrasikan minimal 3 API eksternal gratis (BMKG, OpenWeatherMap, Nominatim).
2. Mengimplementasikan sistem autentikasi berlapis dengan JWT, Basic Auth, API Key, dan OAuth2.
3. Merancang skema database relasional yang mendukung manajemen pengguna, lokasi, dan log bencana.
4. Membangun sistem polling otomatis yang mengambil data BMKG secara berkala menggunakan Laravel Scheduler.
5. Mengimplementasikan sistem radius berbasis geolokasi untuk filtering peringatan yang relevan per pengguna.
6. Menyediakan dashboard peta bencana yang dapat diakses secara publik maupun terautentikasi.

---

## 4. MANFAAT

### Manfaat bagi Masyarakat
- Mendapatkan informasi bencana alam secara **cepat dan personal** berdasarkan lokasi tempat tinggal
- Mengurangi risiko keterlambatan evakuasi akibat ketidaktahuan adanya bencana di sekitar wilayah tempat tinggal
- Memiliki akses ke histori bencana di daerahnya untuk keperluan kesiapsiagaan mandiri
- Dapat melaporkan kondisi bencana lokal yang belum terdeteksi oleh sensor resmi

### Manfaat bagi Pemerintah & Instansi
- Tersedianya kanal distribusi informasi kebencanaan yang lebih luas dan terotomasi
- Data laporan komunitas dapat melengkapi data sensor resmi untuk situasi bencana lokal
- API terbuka memungkinkan integrasi dengan sistem lain (BPBD, Pemda, Rumah Sakit)
- Meningkatkan jangkauan program kesiapsiagaan bencana nasional

### Manfaat Akademik
- Implementasi praktis konsep REST API, JWT, OAuth2, dan geolocation dalam satu sistem terpadu
- Pengalaman mengintegrasikan API publik resmi pemerintah Indonesia dalam sistem nyata
- Pemahaman mendalam tentang sistem real-time berbasis polling dan scheduler

---

## 5. ANALISIS PENGGUNA

### Segmentasi Pengguna

#### a. Guest (Pengguna Tidak Terautentikasi)
- **Profil:** Masyarakat umum yang mengakses sistem tanpa registrasi
- **Kebutuhan:** Melihat peta bencana aktif, membaca informasi gempa terbaru
- **Akses:** Dashboard publik, peta bencana, histori bencana 30 hari terakhir
- **Batasan:** Tidak dapat menerima notifikasi personal, tidak dapat membuat laporan

#### b. Member (Pengguna Terregistrasi)
- **Profil:** Warga masyarakat yang mendaftar untuk mendapatkan notifikasi personal
- **Kebutuhan:** Notifikasi bencana di radius lokasi, tracking histori bencana pribadi
- **Akses:** Semua fitur guest + notifikasi personal, manajemen lokasi, pelaporan komunitas
- **Autentikasi:** JWT Token (Login/Register)

#### c. Reporter (Anggota Komunitas Terverifikasi)
- **Profil:** Relawan atau warga aktif yang telah terverifikasi identitasnya
- **Kebutuhan:** Melaporkan kondisi lapangan, upload foto, update status bencana lokal
- **Akses:** Semua fitur member + laporan terverifikasi, API Key khusus untuk integrasi
- **Autentikasi:** API Key + JWT

#### d. Admin
- **Profil:** Pengelola sistem (operator BMKG, BPBD, atau pengelola platform)
- **Kebutuhan:** Kelola laporan komunitas, moderasi data, monitoring sistem, manajemen user
- **Akses:** Full access — semua endpoint termasuk manajemen user dan konfigurasi sistem
- **Autentikasi:** OAuth2 (Admin Panel) + JWT

#### e. Third-Party Developer (Integrasi Eksternal)
- **Profil:** Pengembang aplikasi lain yang ingin mengintegrasikan data bencana NusaAlert
- **Kebutuhan:** Mengambil data bencana melalui API untuk digunakan di platform lain
- **Akses:** Endpoint data publik dengan rate limiting
- **Autentikasi:** API Key (OAuth2 Client Credentials)

---

## 6. KEBUTUHAN PERANGKAT KERAS

### Untuk Pengembangan (Development Environment)
| Komponen | Spesifikasi Minimum | Spesifikasi Rekomendasi |
|---|---|---|
| **Processor** | Intel Core i3 / AMD Ryzen 3 | Intel Core i5 / AMD Ryzen 5 |
| **RAM** | 4 GB | 8 GB atau lebih |
| **Storage** | 20 GB HDD | 50 GB SSD |
| **Koneksi Internet** | 5 Mbps | 10 Mbps atau lebih |
| **Sistem Operasi** | Windows 10 / macOS 10.15 / Ubuntu 20.04 | Windows 11 / macOS 13 / Ubuntu 22.04 |

### Untuk Production Server (Deployment)
| Komponen | Spesifikasi Minimum |
|---|---|
| **CPU** | 2 vCPU |
| **RAM** | 2 GB |
| **Storage** | 20 GB SSD |
| **Bandwidth** | 1 TB/bulan |
| **OS** | Ubuntu 22.04 LTS |

---

## 7. KEBUTUHAN PERANGKAT LUNAK

### Stack Utama
| Perangkat Lunak | Versi | Fungsi |
|---|---|---|
| **PHP** | 8.2+ | Bahasa pemrograman server-side |
| **Laravel** | 11.x | Framework backend API utama |
| **MySQL** | 8.0+ | Database Management System |
| **Visual Studio Code** | Latest | Code editor utama |
| **Composer** | 2.x | Package manager PHP |
| **Node.js** | 20.x LTS | Build tools (Vite, npm) |

### Laravel Packages Tambahan
| Package | Fungsi |
|---|---|
| `laravel/sanctum` | JWT & API Token Authentication |
| `league/oauth2-server` | OAuth2 Implementation |
| `tymon/jwt-auth` | JWT Authorization |
| `guzzlehttp/guzzle` | HTTP Client untuk call external API |
| `spatie/laravel-permission` | Role & Permission Management |
| `laravel/telescope` | API Debugging & Monitoring |

### Tools Pendukung
| Tools | Fungsi |
|---|---|
| **Postman** | Testing & dokumentasi API endpoint |
| **Git** | Version control |
| **TablePlus / DBeaver** | GUI untuk manajemen database MySQL |
| **XAMPP / Laragon** | Local server environment |

### Ekstensi VS Code yang Direkomendasikan
- PHP Intelephense
- Laravel Extension Pack
- GitLens
- Thunder Client (API Testing)
- MySQL (database explorer)
- DotENV

---

## 8. ARSITEKTUR INFORMASI & SITEMAP

### Struktur Informasi Sistem

```
NusaAlert API System
│
├── PUBLIC ENDPOINTS (No Auth Required)
│   ├── GET /api/bencana                  → Daftar bencana terbaru
│   ├── GET /api/bencana/{id}             → Detail bencana
│   ├── GET /api/gempa/terkini            → Gempa terbaru dari BMKG
│   ├── GET /api/cuaca/{kota}             → Cuaca kota dari OWM
│   └── GET /api/peta/bencana-aktif       → Data peta bencana aktif
│
├── AUTHENTICATED ENDPOINTS (JWT Required)
│   ├── Auth
│   │   ├── POST /api/auth/register       → Registrasi user baru
│   │   ├── POST /api/auth/login          → Login & dapat JWT token
│   │   ├── POST /api/auth/logout         → Revoke token
│   │   └── POST /api/auth/refresh        → Refresh JWT token
│   │
│   ├── User Profile
│   │   ├── GET  /api/user/profile        → Lihat profil
│   │   ├── PUT  /api/user/profile        → Update profil
│   │   └── GET  /api/user/alert-history  → Riwayat alert diterima
│   │
│   ├── Lokasi
│   │   ├── GET  /api/lokasi              → Daftar lokasi tersimpan
│   │   ├── POST /api/lokasi              → Tambah lokasi baru
│   │   ├── PUT  /api/lokasi/{id}         → Update lokasi
│   │   └── DELETE /api/lokasi/{id}       → Hapus lokasi
│   │
│   └── Laporan Komunitas
│       ├── GET  /api/laporan             → Daftar laporan
│       ├── POST /api/laporan             → Buat laporan baru
│       └── PUT  /api/laporan/{id}/status → Update status laporan
│
├── REPORTER ENDPOINTS (API Key Required)
│   ├── POST /api/laporan/verified        → Laporan terverifikasi
│   └── POST /api/laporan/media           → Upload foto lapangan
│
└── ADMIN ENDPOINTS (OAuth2 Required)
    ├── GET  /api/admin/users             → Manajemen user
    ├── PUT  /api/admin/users/{id}/role   → Update role user
    ├── GET  /api/admin/laporan           → Semua laporan komunitas
    ├── PUT  /api/admin/laporan/{id}      → Moderasi laporan
    └── GET  /api/admin/sistem/log        → System monitoring log
```

---

## 9. PEMODELAN SISTEM

### 9.1 Use Case Diagram

**Aktor:**
- Guest
- Member (extends Guest)
- Reporter (extends Member)
- Admin (extends Reporter)
- System Scheduler (automated)

**Use Cases Utama:**
- UC01: Melihat Peta Bencana Aktif (Guest)
- UC02: Registrasi Akun (Guest)
- UC03: Login / Logout (Member)
- UC04: Kelola Lokasi Pantauan (Member)
- UC05: Terima Notifikasi Bencana (Member)
- UC06: Buat Laporan Komunitas (Member)
- UC07: Upload Laporan Terverifikasi (Reporter)
- UC08: Moderasi Laporan (Admin)
- UC09: Manajemen User (Admin)
- UC10: Auto-Polling BMKG API (System Scheduler)
- UC11: Kirim Alert Notifikasi (System Scheduler)

### 9.2 Entity Relationship (Relasi Antar Tabel)

**Tabel Utama:**
- `users` → `lokasi` (One-to-Many: 1 user bisa punya banyak lokasi pantauan)
- `users` → `alerts` (One-to-Many: 1 user bisa terima banyak alert)
- `users` → `laporan` (One-to-Many: 1 user bisa buat banyak laporan)
- `bencana` → `alerts` (One-to-Many: 1 kejadian bencana bisa trigger banyak alert)
- `lokasi` → `alerts` (Many-to-Many: alert dicocokkan antara lokasi user & radius bencana)

### 9.3 Flowchart Sistem Alert

```
[BMKG API] → [Laravel Scheduler tiap 5 menit]
     ↓
[Parse & Simpan Data Gempa ke DB]
     ↓
[Cek: Ada kejadian baru? (belum ada di DB?)]
     ↓ Ya
[Ambil semua Lokasi User aktif dari DB]
     ↓
[Hitung Jarak: Lokasi User vs Titik Bencana]
     ↓
[Jarak ≤ Radius Alert User?]
     ↓ Ya
[Insert ke tabel `alerts` untuk user tersebut]
     ↓
[Kirim Email/Push Notification ke User]
     ↓
[Simpan Log Notifikasi]
```

---

## 10. DESAIN ANTARMUKA (UI/UX)

### Platform: Website (Progressive Web App)

### Halaman-Halaman Utama:

#### a. Landing Page (Guest)
- Hero section: Tagline + CTA Register
- Peta Indonesia real-time dengan marker bencana aktif
- Section statistik: jumlah bencana hari ini, gempa terkini
- Footer: about, kontak, API docs

#### b. Dashboard Member (Setelah Login)
- Sidebar navigasi: Home, Lokasi Saya, Alert History, Laporan
- Widget utama: Peta bencana di sekitar lokasi user
- Notifikasi panel: Alert terbaru yang diterima
- Quick stats: Jumlah lokasi dipantau, total alert diterima

#### c. Halaman Kelola Lokasi
- Form tambah lokasi baru (nama + koordinat + radius alert)
- List lokasi tersimpan dalam kartu
- Toggle aktif/nonaktif per lokasi
- Peta preview radius alert

#### d. Halaman Alert History
- Timeline alert yang pernah diterima
- Filter berdasarkan jenis bencana, tanggal, lokasi
- Detail tiap alert: magnitude, kedalaman, jarak dari lokasi user

#### e. Halaman Laporan Komunitas
- Form buat laporan: jenis bencana, lokasi, deskripsi, foto
- Daftar laporan komunitas yang telah diverifikasi
- Status laporan: Pending → Verified → Resolved

#### f. Admin Dashboard
- Overview metrics: total user, alert terkirim hari ini, laporan pending
- Tabel manajemen user dengan filter & search
- Tabel laporan komunitas dengan aksi moderasi
- Log sistem polling BMKG

### Prinsip UI/UX:
- **Mobile-first responsive design**
- **Warna utama:** Merah-oranye (emergency/alert) + Abu-abu (neutral/info)
- **Tipografi:** Clear, readable, besar — mudah dibaca saat panik/darurat
- **Notifikasi:** Minimalis, langsung ke poin, tidak membingungkan
- **Aksesibilitas:** Kontras tinggi, ukuran teks adjustable

---

## 11. WORKFLOW WEBSITE

### Alur Lengkap Pengguna

#### Alur 1: Pengguna Baru (Guest → Member)
```
1. Pengguna mengakses nusaAlert.id
2. Melihat dashboard publik (peta bencana aktif)
3. Klik "Daftar Sekarang"
4. Isi form registrasi (nama, email, password)
5. Sistem kirim email verifikasi
6. Pengguna klik link verifikasi
7. Akun aktif → Redirect ke halaman setup lokasi
8. Input lokasi tempat tinggal (nama + koordinat atau cari via peta)
9. Set radius alert (default 100km)
10. Dashboard member aktif → Pengguna siap terima alert
```

#### Alur 2: Sistem Polling & Alert (Automated)
```
1. Laravel Scheduler berjalan setiap 5 menit
2. Hit endpoint BMKG: data.bmkg.go.id/gempa terbaru
3. Parse response JSON/XML dari BMKG
4. Cek di database: apakah event_id sudah ada?
   → Jika sudah ada: skip
   → Jika baru: simpan ke tabel `bencana`
5. Ambil semua user dengan lokasi aktif dari DB
6. Untuk setiap user:
   a. Ambil koordinat lokasi user
   b. Hitung jarak ke titik bencana (Haversine Formula)
   c. Jika jarak ≤ radius alert user:
      → Insert record ke tabel `alerts`
      → Queue job: kirim email notifikasi
      → (Opsional) Push notification via web push
7. Log hasil polling ke sistem log
```

#### Alur 3: Login & Akses API (Member)
```
1. POST /api/auth/login (email + password)
2. Sistem validasi kredensial
3. Return: JWT Access Token + Refresh Token
4. Client simpan token di localStorage/cookie
5. Setiap request berikutnya: sertakan header
   Authorization: Bearer {jwt_token}
6. Middleware `auth:api` verifikasi token
7. Jika token expired: POST /api/auth/refresh
8. Jika logout: POST /api/auth/logout (revoke token)
```

#### Alur 4: Third-Party Developer (OAuth2)
```
1. Developer daftar di developer portal
2. Sistem issue Client ID + Client Secret
3. Developer hit POST /oauth/token
   (grant_type: client_credentials)
4. Return: OAuth2 Access Token
5. Developer gunakan token untuk hit public API endpoint
6. Rate limiting: 1000 req/jam per client
```

---

## 12. STRUKTUR DATABASE (MIGRASI LARAVEL)

### Tabel Utama

```sql
-- users
id, name, email, password, role, email_verified_at,
api_key, created_at, updated_at

-- lokasi (relasi: belongs to users)
id, user_id (FK), nama_lokasi, latitude, longitude,
radius_km, is_active, created_at, updated_at

-- bencana
id, event_id (unique, dari BMKG), jenis_bencana,
magnitude, kedalaman_km, latitude, longitude,
wilayah, sumber_api, raw_data (JSON),
terjadi_pada, created_at

-- alerts (relasi: belongs to users, belongs to bencana)
id, user_id (FK), bencana_id (FK), lokasi_id (FK),
jarak_km, status (sent/read/dismissed),
sent_at, read_at, created_at

-- laporan (relasi: belongs to users)
id, user_id (FK), jenis_bencana, latitude, longitude,
deskripsi, foto_url, status (pending/verified/resolved),
verified_by (FK users), created_at, updated_at

-- oauth_clients (untuk OAuth2)
id, name, secret, redirect, personal_access_client,
password_client, revoked, created_at, updated_at
```

---

*Dokumen ini merupakan bagian dari project NusaAlert — Sistem Peringatan Dini Bencana Alam Personal berbasis Laravel API.*
