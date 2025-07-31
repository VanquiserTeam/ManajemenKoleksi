<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CekKondisiKoleksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventarisasi_id',
        'tanggal_cek',
        'status',
        'tanggal_pengembalian',
        'detail_kerusakan',
        'nama_petugas',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_cek' => 'date',
        'tanggal_pengembalian' => 'date',
    ];

    /**
     * Relasi dengan inventarisasi
     */
    public function inventarisasi()
    {
        return $this->belongsTo(Inventarisasi::class);
    }

    /**
     * Get available status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'baik' => 'Baik',
            'rusak' => 'Rusak',
            'hilang' => 'Hilang',
            'dipinjam' => 'Dipinjam',
            'dikonservasi' => 'Dikonservasi',
        ];
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_cek', $tanggal);
    }

    /**
     * Boot method untuk auto-update status di inventarisasi dan riwayat
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($cekKondisi) {
            // Update status di inventarisasi jika kondisi berubah dari rusak/dikonservasi ke baik
            $inventarisasi = $cekKondisi->inventarisasi;
            $statusSebelum = $inventarisasi->kondisi_fisik;

            if ($cekKondisi->status === 'baik' && in_array($statusSebelum, ['rusak'])) {
                // Update inventarisasi
                $inventarisasi->update([
                    'kondisi_fisik' => 'baik',
                    'solusi' => null
                ]);

                // Buat riwayat otomatis
                RiwayatInventarisasi::create([
                    'inventarisasi_id' => $inventarisasi->id,
                    'kondisi_fisik_sebelum' => $statusSebelum,
                    'kondisi_fisik_sesudah' => 'baik',
                    'solusi' => null,
                    'keterangan' => 'Status diubah otomatis melalui cek kondisi koleksi',
                    'tanggal_perubahan' => $cekKondisi->tanggal_cek,
                    'petugas' => $cekKondisi->nama_petugas,
                ]);
            } elseif (in_array($cekKondisi->status, ['rusak', 'hilang', 'dipinjam', 'dikonservasi'])) {
                // Update inventarisasi untuk status rusak/hilang
                $inventarisasi->update([
                    'kondisi_fisik' => $cekKondisi->status,
                ]);

                // Buat riwayat otomatis
                RiwayatInventarisasi::create([
                    'inventarisasi_id' => $inventarisasi->id,
                    'kondisi_fisik_sebelum' => $statusSebelum,
                    'kondisi_fisik_sesudah' => $cekKondisi->status,
                    'keterangan' => 'Status diubah melalui cek kondisi koleksi: ' . ($cekKondisi->detail_kerusakan ?? $cekKondisi->keterangan ?? ''),
                    'tanggal_perubahan' => $cekKondisi->tanggal_cek,
                    'petugas' => $cekKondisi->nama_petugas,
                ]);
            }
        });
    }
}
