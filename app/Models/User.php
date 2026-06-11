<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'no_hp', 'jabatan', 'role', 'site', 'password', 'avatar', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function laporanSebagaiPelapor()
    {
        return $this->hasMany(Laporan::class, 'user_id');
    }

    public function laporanSebagaiTeknisi()
    {
        return $this->hasMany(Laporan::class, 'teknisi_id');
    }

    public function isPelapor(): bool { return $this->role === 'pelapor'; }
    public function isTeknisi(): bool { return $this->role === 'teknisi'; }
    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isPimpinan(): bool { return $this->role === 'pimpinan'; }

    public function getDashboardRoute(): string
    {
        return match($this->role) {
            'teknisi'  => 'teknisi.dashboard',
            'admin'    => 'admin.dashboard',
            'pimpinan' => 'pimpinan.dashboard',
            default    => 'pelapor.dashboard',
        };
    }
}