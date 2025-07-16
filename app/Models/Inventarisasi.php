<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Inventarisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'koleksi_id',
        'nomor_inventarisasi',
        'status_kepemilikan',
        'jenis_koleksi',
        'kondisi_fisik',
        'solusi',
        'lokasi_penyimpanan',
        'keterangan',
    ];

    protected $casts = [
        'status_kepemilikan' => 'string',
        'jenis_koleksi' => 'string',
        'kondisi_fisik' => 'string',
        'solusi' => 'string',
    ];

    /**
     * Relasi dengan registrasi (dulu koleksi)
     */
    public function registrasi()
    {
        return $this->belongsTo(Registrasi::class, 'koleksi_id');
    }

    /**
     * Legacy relasi untuk backward compatibility
     */
    public function koleksi()
    {
        return $this->registrasi();
    }

    /**
     * Relasi dengan riwayat inventarisasi
     */
    public function riwayatInventarisasi()
    {
        return $this->hasMany(RiwayatInventarisasi::class);
    }

    /**
     * Relasi dengan lokasi penyimpanan
     */
    public function lokasiPenyimpananDetail()
    {
        return $this->belongsTo(LokasiPenyimpanan::class, 'lokasi_penyimpanan', 'kode_lokasi');
    }

    /**
     * Get the available options for status_kepemilikan
     */
    public static function getStatusKepemilikanOptions(): array
    {
        return [
            'milik_museum' => 'Milik Museum',
            'peminjaman_jangka_pendek' => 'Peminjaman Jangka Pendek',
            'peminjaman_jangka_panjang' => 'Peminjaman Jangka Panjang',
            'bmn' => 'BMN',
        ];
    }

    /**
     * Get the available options for jenis_koleksi
     */
    public static function getJenisKoleksiOptions(): array
    {
        return [
            '01' => '01 - Geologika',
            '02' => '02 - Biologika',
            '03' => '03 - Etnografika',
            '04' => '04 - Arkeologika',
            '05' => '05 - Historika',
            '06' => '06 - Numismatika',
            '07' => '07 - Filologika',
            '08' => '08 - Keramonologika',
            '09' => '09 - Seni Rupa',
            '10' => '10 - Teknologika',
        ];
    }

    /**
     * Get the available options for kondisi_fisik
     */
    public static function getKondisiFisikOptions(): array
    {
        return [
            'baik' => 'Baik',
            'rusak' => 'Rusak',
            'hilang' => 'Hilang',
        ];
    }

    /**
     * Get the available options for solusi
     */
    public static function getSolusiOptions(): array
    {
        return [
            'konservasi_ringan' => 'Konservasi Ringan',
            'konservasi_sedang' => 'Konservasi Sedang',
            'konservasi_berat' => 'Konservasi Berat',
        ];
    }

    /**
     * Generate nomor inventarisasi otomatis
     */
    public static function generateNomorInventarisasi($jenisKoleksi, $tahun = null)
    {
        $tahun = $tahun ?? date('Y');
        
        // Ambil nomor terakhir untuk jenis dan tahun yang sama
        $lastNumber = self::where('jenis_koleksi', $jenisKoleksi)
            ->whereYear('created_at', $tahun)
            ->orderBy('nomor_inventarisasi', 'desc')
            ->first();

        $sequence = 1;
        if ($lastNumber) {
            $parts = explode('.', $lastNumber->nomor_inventarisasi);
            if (count($parts) == 4) {
                $sequence = intval($parts[3]) + 1;
            }
        }

        return sprintf('%s.B.%d.%04d', $jenisKoleksi, $tahun, $sequence);
    }

    /**
     * Scope untuk filter berdasarkan jenis koleksi
     */
    public function scopeByJenisKoleksi($query, $jenis)
    {
        return $query->where('jenis_koleksi', $jenis);
    }

    /**
     * Scope untuk filter berdasarkan kondisi fisik
     */
    public function scopeByKondisiFisik($query, $kondisi)
    {
        return $query->where('kondisi_fisik', $kondisi);
    }

    /**
     * Scope untuk filter berdasarkan status kepemilikan
     */
    public function scopeByStatusKepemilikan($query, $status)
    {
        return $query->where('status_kepemilikan', $status);
    }

    /**
     * Scope untuk filter berdasarkan lokasi penyimpanan
     */
    public function scopeByLokasi($query, $lokasi)
    {
        return $query->where('lokasi_penyimpanan', 'like', '%' . $lokasi . '%');
    }

    /**
     * Scope untuk koleksi yang rusak
     */
    public function scopeRusak($query)
    {
        return $query->where('kondisi_fisik', 'rusak');
    }

    /**
     * Boot method untuk auto-generate riwayat ketika kondisi berubah
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($inventarisasi) {
            if ($inventarisasi->isDirty('kondisi_fisik')) {
                $original = $inventarisasi->getOriginal('kondisi_fisik');
                $new = $inventarisasi->kondisi_fisik;

                RiwayatInventarisasi::create([
                    'inventarisasi_id' => $inventarisasi->id,
                    'kondisi_fisik_sebelum' => $original,
                    'kondisi_fisik_sesudah' => $new,
                    'solusi' => $new === 'rusak' ? $inventarisasi->solusi : null,
                    'tanggal_perubahan' => now()->toDateString(),
                    'petugas' => Auth::check() ? Auth::user()->name : null,
                ]);
            }
        });
    }
}
