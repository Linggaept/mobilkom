<?php

namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LaporanMasukNotification extends Notification
{
    use Queueable;

    public function __construct(public Laporan $laporan) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'laporan_id'    => $this->laporan->id,
            'nomor_laporan' => $this->laporan->nomor_laporan,
            'pelapor'       => $this->laporan->nama_pelapor,
            'site'          => $this->laporan->site->nama,
            'tipe'          => $this->laporan->tipeRadio->nama,
            'kerusakan'     => $this->laporan->jenisKerusakan->nama,
            'pesan'         => 'Laporan baru masuk dari ' . $this->laporan->nama_pelapor,
            'type'          => 'laporan_masuk',
            'url'           => route('admin.laporan.show', $this->laporan->id),
        ];
    }
}