<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Site;
use App\Models\TipeRadio;
use App\Models\JenisKerusakan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaporanSeeder extends Seeder
{
    public function run(): void
    {
        $pelapor  = User::where('email', 'pelapor@mobilkom.com')->first();
        $admin    = User::where('email', 'admin@mobilkom.com')->first();
        $teknisi1 = User::where('email', 'teknisi1@mobilkom.com')->first();
        $teknisi2 = User::where('email', 'teknisi2@mobilkom.com')->first();

        $site1 = Site::where('kode', 'DRI')->first();
        $site2 = Site::where('kode', 'MNS')->first();
        $site3 = Site::where('kode', 'RMB')->first();

        $radioHT      = TipeRadio::where('nama', 'Radio HT')->first();
        $radioMobile  = TipeRadio::where('nama', 'Radio Mobile')->first();
        $radioDesktop = TipeRadio::where('nama', 'Radio Desktop')->first();

        $jkMati    = JenisKerusakan::where('nama', 'Radio Mati')->first();
        $jkBaterai = JenisKerusakan::where('nama', 'Baterai Habis')->first();
        $jkAntena  = JenisKerusakan::where('nama', 'Antena Rusak')->first();
        $jkLainnya = JenisKerusakan::where('nama', 'Kerusakan Lainnya')->first();

        $now = now();

        $laporan = [
            // 1. Menunggu verifikasi - NORMAL (baru 6 jam lalu, sisa ~18 jam)
            [
                'nomor_laporan'      => 'INC000000000001',
                'user_id'            => $pelapor->id,
                'nama_pelapor'       => $pelapor->name,
                'jabatan_pelapor'    => $pelapor->jabatan,
                'tanggal_laporan'    => $now->copy()->subHours(6)->toDateString(),
                'site_id'            => $site1->id,
                'tipe_radio_id'      => $radioHT->id,
                'jenis_kerusakan_id' => $jkMati->id,
                'deskripsi_kerusakan'=> 'Radio HT tidak menyala sama sekali setelah jatuh dari ketinggian 1 meter. Sudah dicoba ganti baterai namun tetap tidak berfungsi.',
                'status'             => 'menunggu_verifikasi',
                'teknisi_id'         => null,
                'admin_id'           => null,
                'catatan_admin'      => null,
                'catatan_teknisi'    => null,
                'tanggal_verifikasi' => null,
                'tanggal_selesai'    => null,
                'created_at'         => $now->copy()->subHours(6),
                'updated_at'         => $now->copy()->subHours(6),
            ],

            // 2. Menunggu verifikasi - OVERDUE (sudah 30 jam, lewat 24 jam)
            [
                'nomor_laporan'      => 'INC000000000002',
                'user_id'            => $pelapor->id,
                'nama_pelapor'       => $pelapor->name,
                'jabatan_pelapor'    => $pelapor->jabatan,
                'tanggal_laporan'    => $now->copy()->subHours(30)->toDateString(),
                'site_id'            => $site2->id,
                'tipe_radio_id'      => $radioMobile->id,
                'jenis_kerusakan_id' => $jkBaterai->id,
                'deskripsi_kerusakan'=> 'Baterai radio mobile tidak bisa diisi ulang. Indikator pengisian tidak menyala meskipun sudah dihubungkan ke charger lebih dari 2 jam.',
                'status'             => 'menunggu_verifikasi',
                'teknisi_id'         => null,
                'admin_id'           => null,
                'catatan_admin'      => null,
                'catatan_teknisi'    => null,
                'tanggal_verifikasi' => null,
                'tanggal_selesai'    => null,
                'created_at'         => $now->copy()->subHours(30),
                'updated_at'         => $now->copy()->subHours(30),
            ],

            // 3. Diverifikasi - NORMAL (verifikasi 1 jam lalu, sisa ~47 jam proses)
            [
                'nomor_laporan'      => 'INC000000000003',
                'user_id'            => $pelapor->id,
                'nama_pelapor'       => $pelapor->name,
                'jabatan_pelapor'    => $pelapor->jabatan,
                'tanggal_laporan'    => $now->copy()->subHours(10)->toDateString(),
                'site_id'            => $site3->id,
                'tipe_radio_id'      => $radioDesktop->id,
                'jenis_kerusakan_id' => $jkAntena->id,
                'deskripsi_kerusakan'=> 'Antena radio desktop bengkok dan sinyal sangat lemah. Komunikasi antar tim terganggu di area Rumbai.',
                'status'             => 'diverifikasi',
                'teknisi_id'         => $teknisi1->id,
                'admin_id'           => $admin->id,
                'catatan_admin'      => 'Segera ditindaklanjuti. Antena perlu diganti unit baru.',
                'catatan_teknisi'    => null,
                'tanggal_verifikasi' => $now->copy()->subHour(),
                'tanggal_selesai'    => null,
                'created_at'         => $now->copy()->subHours(10),
                'updated_at'         => $now->copy()->subHour(),
            ],

            // 4. Sedang proses - OVERDUE (verifikasi 3 hari lalu, lewat 48 jam)
            [
                'nomor_laporan'      => 'INC000000000004',
                'user_id'            => $pelapor->id,
                'nama_pelapor'       => $pelapor->name,
                'jabatan_pelapor'    => $pelapor->jabatan,
                'tanggal_laporan'    => $now->copy()->subDays(4)->toDateString(),
                'site_id'            => $site1->id,
                'tipe_radio_id'      => $radioHT->id,
                'jenis_kerusakan_id' => $jkLainnya->id,
                'deskripsi_kerusakan'=> 'Tombol PTT (Push To Talk) macet dan tidak dapat dilepas. Radio masih menyala namun tidak bisa digunakan untuk komunikasi normal.',
                'status'             => 'sedang_proses',
                'teknisi_id'         => $teknisi2->id,
                'admin_id'           => $admin->id,
                'catatan_admin'      => 'Tunggu spare part PTT dari gudang pusat.',
                'catatan_teknisi'    => 'Sudah diperiksa, menunggu komponen pengganti dari gudang.',
                'tanggal_verifikasi' => $now->copy()->subDays(3),
                'tanggal_selesai'    => null,
                'created_at'         => $now->copy()->subDays(4),
                'updated_at'         => $now->copy()->subDays(3),
            ],

            // 5. Selesai - normal dengan durasi lengkap
            [
                'nomor_laporan'      => 'INC000000000005',
                'user_id'            => $pelapor->id,
                'nama_pelapor'       => $pelapor->name,
                'jabatan_pelapor'    => $pelapor->jabatan,
                'tanggal_laporan'    => $now->copy()->subDays(5)->toDateString(),
                'site_id'            => $site2->id,
                'tipe_radio_id'      => $radioMobile->id,
                'jenis_kerusakan_id' => $jkMati->id,
                'deskripsi_kerusakan'=> 'Radio mobile tiba-tiba mati di tengah operasi lapangan. Setelah dicek, ditemukan korsleting pada papan sirkuit.',
                'status'             => 'selesai',
                'teknisi_id'         => $teknisi1->id,
                'admin_id'           => $admin->id,
                'catatan_admin'      => 'Diverifikasi dan ditugaskan ke Budi Santoso.',
                'catatan_teknisi'    => 'Papan sirkuit berhasil diperbaiki dan radio berfungsi normal kembali.',
                'tanggal_verifikasi' => $now->copy()->subDays(5)->addHours(3),
                'tanggal_selesai'    => $now->copy()->subDays(4)->addHours(10),
                'created_at'         => $now->copy()->subDays(5),
                'updated_at'         => $now->copy()->subDays(4)->addHours(10),
            ],

            // 6. Ditolak
            [
                'nomor_laporan'      => 'INC000000000006',
                'user_id'            => $pelapor->id,
                'nama_pelapor'       => $pelapor->name,
                'jabatan_pelapor'    => $pelapor->jabatan,
                'tanggal_laporan'    => $now->copy()->subDays(2)->toDateString(),
                'site_id'            => $site3->id,
                'tipe_radio_id'      => $radioDesktop->id,
                'jenis_kerusakan_id' => $jkLainnya->id,
                'deskripsi_kerusakan'=> 'Suara radio terputus-putus. Namun setelah dicek admin, ditemukan ini masalah sinyal jaringan, bukan kerusakan perangkat.',
                'status'             => 'ditolak',
                'teknisi_id'         => null,
                'admin_id'           => $admin->id,
                'catatan_admin'      => 'Laporan ditolak. Masalah disebabkan gangguan jaringan sementara, bukan kerusakan hardware. Silakan laporkan ke tim jaringan.',
                'catatan_teknisi'    => null,
                'tanggal_verifikasi' => $now->copy()->subDays(2)->addHours(5),
                'tanggal_selesai'    => null,
                'created_at'         => $now->copy()->subDays(2),
                'updated_at'         => $now->copy()->subDays(2)->addHours(5),
            ],
        ];

        DB::table('laporan')->delete();
        DB::table('laporan')->insert($laporan);
    }
}
