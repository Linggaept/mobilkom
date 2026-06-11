<?php

namespace App\Policies;

use App\Models\{Laporan, User};

class LaporanPolicy
{
    public function view(User $user, Laporan $laporan): bool
    {
        return $user->id === $laporan->user_id
            || in_array($user->role, ['admin', 'pimpinan'])
            || $user->id === $laporan->teknisi_id;
    }

    public function update(User $user, Laporan $laporan): bool
    {
        return $user->id === $laporan->user_id && $laporan->status === 'menunggu_verifikasi';
    }

    public function delete(User $user, Laporan $laporan): bool
    {
        return $user->id === $laporan->user_id
            && in_array($laporan->status, ['menunggu_verifikasi', 'ditolak']);
    }
}