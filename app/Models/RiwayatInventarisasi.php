<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatInventarisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventarisasi_id',
        'kondisi_fisik_sebelum',
        'kondisi_fisik_sesudah',
        'solusi',
        'keterangan',
        'tanggal_perubahan',
        'petugas',
    ];

    protected $casts = [
        'tanggal_perubahan' => 'date',
    ];

    /**
     * Relasi dengan inventarisasi
     */
    public function inventarisasi()
    {
        return $this->belongsTo(Inventarisasi::class);
    }

    /**
     * Scope untuk riwayat kerusakan
     */
    public function scopeKerusakan($query)
    {
        return $query->where('kondisi_fisik_sesudah', 'rusak');
    }
}
