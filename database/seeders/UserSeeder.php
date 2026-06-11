<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(['email' => 'admin@mobilkom.com'], [
            'name'      => 'Administrator',
            'email'     => 'admin@mobilkom.com',
            'no_hp'     => '081234567890',
            'jabatan'   => 'Administrator Sistem',
            'role'      => 'admin',
            'site'      => 'Duri',
            'password'  => Hash::make('admin123'),
            'is_active' => true,
        ]);

        // Pimpinan
        User::updateOrCreate(['email' => 'pimpinan@mobilkom.com'], [
            'name'      => 'Pimpinan',
            'email'     => 'pimpinan@mobilkom.com',
            'no_hp'     => '081234567891',
            'jabatan'   => 'Kepala Bagian Komunikasi',
            'role'      => 'pimpinan',
            'site'      => 'Duri',
            'password'  => Hash::make('pimpinan123'),
            'is_active' => true,
        ]);

        // Teknisi
        $teknisis = [
            ['name' => 'Budi Santoso',   'email' => 'teknisi1@mobilkom.com', 'site' => 'Duri',   'jabatan' => 'Teknisi Radio Senior'],
            ['name' => 'Agus Wijaya',    'email' => 'teknisi2@mobilkom.com', 'site' => 'Minas',  'jabatan' => 'Teknisi Radio'],
            ['name' => 'Rudi Hermawan',  'email' => 'teknisi3@mobilkom.com', 'site' => 'Rumbai', 'jabatan' => 'Teknisi Radio'],
        ];

        foreach ($teknisis as $t) {
            User::updateOrCreate(['email' => $t['email']], array_merge($t, [
                'no_hp'     => '08' . rand(100000000, 999999999),
                'role'      => 'teknisi',
                'password'  => Hash::make('teknisi123'),
                'is_active' => true,
            ]));
        }

        // Contoh Pelapor
        User::updateOrCreate(['email' => 'pelapor@mobilkom.com'], [
            'name'      => 'Siti Rahayu',
            'email'     => 'pelapor@mobilkom.com',
            'no_hp'     => '081234567895',
            'jabatan'   => 'Staff Operasional',
            'role'      => 'pelapor',
            'site'      => 'Duri',
            'password'  => Hash::make('pelapor123'),
            'is_active' => true,
        ]);
    }
}