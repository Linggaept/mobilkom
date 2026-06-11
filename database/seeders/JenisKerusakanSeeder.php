<?php

namespace Database\Seeders;

use App\Models\JenisKerusakan;
use Illuminate\Database\Seeder;

class JenisKerusakanSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama' => 'Radio Mati',         'deskripsi' => 'Radio tidak menyala / tidak berfungsi sama sekali'],
            ['nama' => 'Baterai Habis',       'deskripsi' => 'Baterai tidak bisa diisi atau sudah melemah'],
            ['nama' => 'Antena Rusak',        'deskripsi' => 'Antena patah, bengkok, atau tidak berfungsi'],
            ['nama' => 'Kerusakan Lainnya',   'deskripsi' => 'Kerusakan selain kategori di atas'],
        ];

        foreach ($items as $item) {
            JenisKerusakan::updateOrCreate(['nama' => $item['nama']], array_merge($item, ['is_active' => true]));
        }
    }
}