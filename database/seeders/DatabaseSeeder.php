<?php

namespace Database\Seeders;

use App\Models\User;
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
        $member->lokasi()->createMany([
            [
                'nama_lokasi' => 'Rumah Utama',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'radius_km' => 50,
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Kantor Sudirman',
                'latitude' => -6.2250,
                'longitude' => 106.8100,
                'radius_km' => 25,
                'is_active' => false,
            ],
        ]);

        // Create some demo bencana data
        \App\Models\Bencana::create([
            'event_id' => 'bmkg-demo-001',
            'jenis_bencana' => 'gempa',
            'magnitude' => 5.2,
            'kedalaman_km' => 10,
            'latitude' => -7.4956,
            'longitude' => 109.1275,
            'wilayah' => 'Barat Daya KAB-PANGANDARAN-JABAR',
            'sumber_api' => 'bmkg',
            'raw_data' => ['source' => 'demo'],
            'terjadi_pada' => now()->subHours(2),
        ]);

        \App\Models\Bencana::create([
            'event_id' => 'bmkg-demo-002',
            'jenis_bencana' => 'gempa',
            'magnitude' => 6.8,
            'kedalaman_km' => 10,
            'latitude' => -8.3925,
            'longitude' => 110.7658,
            'wilayah' => '120km Barat Daya Pacitan, Jawa Timur',
            'sumber_api' => 'bmkg',
            'raw_data' => ['source' => 'demo'],
            'terjadi_pada' => now()->subHours(5),
        ]);

        \App\Models\Bencana::create([
            'event_id' => 'bmkg-demo-003',
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
    }
}
