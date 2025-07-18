<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisKoleksi extends Model
{
    use HasFactory;

    protected $table = 'jenis_koleksis';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relasi dengan inventarisasi
     */
    public function inventarisasis()
    {
        return $this->hasMany(Inventarisasi::class, 'jenis_koleksi', 'kode');
    }

    /**
     * Scope untuk jenis koleksi aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get nama lengkap (kode - nama)
     */
    public function getNamaLengkapAttribute()
    {
        return $this->kode . ' - ' . $this->nama;
    }

    /**
     * Get jumlah koleksi berdasarkan jenis
     */
    public function getJumlahKoleksiAttribute()
    {
        return $this->inventarisasis()->count();
    }

    /**
     * Static method untuk mendapatkan options dalam format array
     */
    public static function getOptionsArray()
    {
        return self::aktif()
            ->orderBy('kode')
            ->pluck('nama', 'kode')
            ->toArray();
    }
}
