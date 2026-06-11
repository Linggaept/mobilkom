<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKerusakan extends Model
{
    protected $fillable = ['nama', 'deskripsi', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }
}