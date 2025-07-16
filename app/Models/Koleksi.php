<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Koleksi extends Model
{
    protected $table = 'koleksi';
    
    protected $fillable = [
        'no_registrasi',
        'no_inventaris',
        'nama_koleksi',
        'tahun_perolehan',
        'cara_perolehan',
        'asal_perolehan',
        'panjang',
        'lebar',
        'tinggi',
        'asal_daerah',
        'warna',
        'bentuk',
        'foto',
        'bahan',
        'narasumber',
        'jenis_koleksi',
        'lokasi_penyimpanan',
        'deskripsi',
        'status'
    ];

    protected $casts = [
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'tinggi' => 'decimal:2',
        'tahun_perolehan' => 'integer'
    ];
    
    // Accessor untuk foto
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return Storage::url($this->foto);
        }
        return null;
    }
}
