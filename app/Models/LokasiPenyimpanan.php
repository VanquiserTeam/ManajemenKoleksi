<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LokasiPenyimpanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lokasi',
        'kode_lokasi',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk lokasi yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relasi dengan inventarisasi
     */
    public function inventarisasis()
    {
        return $this->hasMany(Inventarisasi::class, 'lokasi_penyimpanan', 'kode_lokasi');
    }
}
