<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        $sites = [
            ['nama' => 'Duri',      'kode' => 'DRI'],
            ['nama' => 'Minas',     'kode' => 'MNS'],
            ['nama' => 'Rumbai',    'kode' => 'RMB'],
            ['nama' => 'Petapahan', 'kode' => 'PTP'],
            ['nama' => 'Libo',      'kode' => 'LBO'],
            ['nama' => 'Rangau',    'kode' => 'RGU'],
            ['nama' => 'Batang',    'kode' => 'BTG'],
            ['nama' => 'Bangko',    'kode' => 'BGK'],
            ['nama' => 'Pager',     'kode' => 'PGR'],
            ['nama' => 'Pinang',    'kode' => 'PNG'],
            ['nama' => 'Dumai',     'kode' => 'DMI'],
        ];

        foreach ($sites as $site) {
            Site::updateOrCreate(['nama' => $site['nama']], array_merge($site, ['is_active' => true]));
        }
    }
}