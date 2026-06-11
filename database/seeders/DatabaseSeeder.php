<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SiteSeeder::class,
            TipeRadioSeeder::class,
            JenisKerusakanSeeder::class,
            UserSeeder::class,
            LaporanSeeder::class,
        ]);
    }
}