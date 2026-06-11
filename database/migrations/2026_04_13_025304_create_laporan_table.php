<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // pelapor
            $table->string('nama_pelapor');
            $table->string('jabatan_pelapor');
            $table->date('tanggal_laporan');
            $table->foreignId('site_id')->constrained('sites');
            $table->foreignId('tipe_radio_id')->constrained('tipe_radios');
            $table->foreignId('jenis_kerusakan_id')->constrained('jenis_kerusakans');
            $table->text('deskripsi_kerusakan');
            $table->string('foto')->nullable();
            // Status: menunggu_verifikasi, diverifikasi, sedang_proses, selesai, ditolak
            $table->enum('status', ['menunggu_verifikasi', 'diverifikasi', 'sedang_proses', 'selesai', 'ditolak'])
                  ->default('menunggu_verifikasi');
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // yang verifikasi
            $table->text('catatan_admin')->nullable();
            $table->text('catatan_teknisi')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            // Tanda tangan digital (base64 atau path)
            $table->longText('ttd_pelapor')->nullable();
            $table->longText('ttd_teknisi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};