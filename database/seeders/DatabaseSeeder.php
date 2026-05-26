<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Alert;
use App\Models\Bencana;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::create(['name' => 'admin']);
        $memberRole = Role::create(['name' => 'member']);
        $reporterRole = Role::create(['name' => 'reporter']);

        // Create Permissions
        $permissions = [
            'view-dashboard',
            'manage-lokasi',
            'view-alerts',
            'create-laporan',
            'verify-laporan',
            'manage-users',
            'view-admin-panel',
            'manage-system',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $memberRole->givePermissionTo(['view-dashboard', 'manage-lokasi', 'view-alerts', 'create-laporan']);
        $reporterRole->givePermissionTo(['view-dashboard', 'manage-lokasi', 'view-alerts', 'create-laporan', 'verify-laporan']);
        $adminRole->givePermissionTo(Permission::all());

        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@nusaalert.id',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'api_key' => Str::random(64),
        ]);
        $admin->assignRole('admin');

        // Create Demo Member
        $member = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'phone' => '081234567890',
        ]);
        $member->assignRole('member');

        // Create demo lokasi for member
        $lokasiRumah = $member->lokasi()->create([
            'nama_lokasi' => 'Rumah Utama',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_km' => 50,
            'is_active' => true,
        ]);

        $lokasiKantor = $member->lokasi()->create([
            'nama_lokasi' => 'Kantor Sudirman',
            'latitude' => -6.2250,
            'longitude' => 106.8100,
            'radius_km' => 25,
            'is_active' => true,
        ]);

        // Create demo bencana data
        $bencana1 = Bencana::create([
            'user_id' => $admin->id,
            'event_id' => 'manual-demo-001',
            'jenis_bencana' => 'gempa',
            'magnitude' => 5.2,
            'kedalaman_km' => 10,
            'latitude' => -6.3500,
            'longitude' => 106.9000,
            'wilayah' => 'Selatan Bekasi, Jawa Barat',
            'sumber_api' => 'manual_admin',
            'raw_data' => ['source' => 'demo'],
            'terjadi_pada' => now()->subHours(2),
        ]);

        $bencana2 = Bencana::create([
            'user_id' => $admin->id,
            'event_id' => 'manual-demo-002',
            'jenis_bencana' => 'gempa',
            'magnitude' => 6.8,
            'kedalaman_km' => 10,
            'latitude' => -8.3925,
            'longitude' => 110.7658,
            'wilayah' => '120km Barat Daya Pacitan, Jawa Timur',
            'sumber_api' => 'manual_admin',
            'raw_data' => ['source' => 'demo'],
            'terjadi_pada' => now()->subHours(5),
        ]);

        $bencana3 = Bencana::create([
            'user_id' => $admin->id,
            'event_id' => 'manual-demo-003',
            'jenis_bencana' => 'banjir',
            'magnitude' => null,
            'kedalaman_km' => null,
            'latitude' => -6.1751,
            'longitude' => 106.8650,
            'wilayah' => 'Pesisir Utara Jakarta',
            'sumber_api' => 'komunitas',
            'raw_data' => ['source' => 'demo'],
            'terjadi_pada' => now()->subHours(1),
        ]);

        $bencana4 = Bencana::create([
            'event_id' => 'bmkg-demo-004',
            'jenis_bencana' => 'gempa',
            'magnitude' => 3.8,
            'kedalaman_km' => 15,
            'latitude' => -6.2500,
            'longitude' => 106.7800,
            'wilayah' => 'Tangerang Selatan, Banten',
            'sumber_api' => 'bmkg',
            'raw_data' => ['source' => 'demo'],
            'terjadi_pada' => now()->subDays(1),
        ]);

        $bencana5 = Bencana::create([
            'event_id' => 'bmkg-demo-005',
            'jenis_bencana' => 'cuaca_ekstrem',
            'magnitude' => null,
            'kedalaman_km' => null,
            'latitude' => -6.1300,
            'longitude' => 106.9200,
            'wilayah' => 'Jakarta Timur',
            'sumber_api' => 'bmkg',
            'raw_data' => ['source' => 'demo'],
            'terjadi_pada' => now()->subDays(2),
        ]);

        // Create demo alert records (#5 - Riwayat Peringatan dummy data)
        // Alert 1: Gempa M5.2 near Rumah (within 50km) - unread
        Alert::create([
            'user_id' => $member->id,
            'bencana_id' => $bencana1->id,
            'lokasi_id' => $lokasiRumah->id,
            'jarak_km' => 16.5,
            'status' => 'sent',
            'sent_at' => now()->subHours(2),
        ]);

        // Alert 2: Banjir near Rumah - unread
        Alert::create([
            'user_id' => $member->id,
            'bencana_id' => $bencana3->id,
            'lokasi_id' => $lokasiRumah->id,
            'jarak_km' => 4.2,
            'status' => 'sent',
            'sent_at' => now()->subHour(),
        ]);

        // Alert 3: Gempa M3.8 near Kantor - read
        Alert::create([
            'user_id' => $member->id,
            'bencana_id' => $bencana4->id,
            'lokasi_id' => $lokasiKantor->id,
            'jarak_km' => 8.7,
            'status' => 'read',
            'sent_at' => now()->subDays(1),
            'read_at' => now()->subDays(1)->addMinutes(15),
        ]);

        // Alert 4: Cuaca ekstrem near Rumah - read
        Alert::create([
            'user_id' => $member->id,
            'bencana_id' => $bencana5->id,
            'lokasi_id' => $lokasiRumah->id,
            'jarak_km' => 12.3,
            'status' => 'read',
            'sent_at' => now()->subDays(2),
            'read_at' => now()->subDays(2)->addMinutes(30),
        ]);

        // Alert 5: Gempa M5.2 near Kantor too - dismissed
        Alert::create([
            'user_id' => $member->id,
            'bencana_id' => $bencana1->id,
            'lokasi_id' => $lokasiKantor->id,
            'jarak_km' => 18.2,
            'status' => 'dismissed',
            'sent_at' => now()->subHours(2),
            'read_at' => now()->subHours(1),
        ]);
    }
}
