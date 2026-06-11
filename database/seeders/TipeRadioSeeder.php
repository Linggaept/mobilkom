<?php

namespace Database\Seeders;

use App\Models\TipeRadio;
use Illuminate\Database\Seeder;

class TipeRadioSeeder extends Seeder
{
    public function run(): void
    {
        $tipes = [
            ['nama' => 'Radio HT',      'deskripsi' => 'Handheld Transceiver / Radio Genggam'],
            ['nama' => 'Radio Mobile',  'deskripsi' => 'Radio yang dipasang di kendaraan'],
            ['nama' => 'Radio Desktop', 'deskripsi' => 'Radio stasioner / meja'],
        ];

        foreach ($tipes as $tipe) {
            TipeRadio::updateOrCreate(['nama' => $tipe['nama']], array_merge($tipe, ['is_active' => true]));
        }
    }
}