<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = ['nama', 'kode', 'alamat', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }
}