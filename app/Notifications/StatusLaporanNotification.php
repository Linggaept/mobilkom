<?php

namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusLaporanNotification extends Notification
{
    use Queueable;

    public function __construct(public Laporan $laporan, public string $pesan) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'laporan_id'    => $this->laporan->id,
            'nomor_laporan' => $this->laporan->nomor_laporan,
            'status'        => $this->laporan->status,
            'status_label'  => $this->laporan->status_label,
            'pesan'         => $this->pesan,
            'type'          => 'status_laporan',
            'url'           => route('pelapor.laporan.show', $this->laporan->id),
        ];
    }
}