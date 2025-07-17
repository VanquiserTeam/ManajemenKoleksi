<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Registrasi extends Model
{
    use HasFactory;

    protected $table = 'registrasi';

    protected $fillable = [
        'registrasi_id',
        'nama_koleksi',
        'tahun',
        'cara_perolehan',
        'panjang',
        'lebar',
        'tinggi',
        'berat',
        'asal',
        'bahan',
        'tanah',
        'warna',
        'bentuk',
        'narasumber',
        'foto',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'tinggi' => 'decimal:2',
        'berat' => 'decimal:2',
        'cara_perolehan' => 'string',
    ];

    /**
     * Relasi dengan inventarisasi
     */
    public function inventarisasis()
    {
        return $this->hasMany(Inventarisasi::class, 'koleksi_id');
    }

    public function cekKondisiKoleksis()
    {
        return $this->hasMany(CekKondisiKoleksi::class, 'koleksi_id');
    }

    /**
     * Get the available options for cara_perolehan
     */
    public static function getCaraPerolehanOptions(): array
    {
        return [
            'hibah' => 'Hibah',
            'pembelian' => 'Pembelian',
            'peminjaman' => 'Peminjaman',
            'warisan' => 'Warisan',
            'hadiah' => 'Hadiah',
        ];
    }

    /**
     * Generate registrasi_id otomatis
     */
    public static function generateRegistrasiId($tahun = null)
    {
        $tahun = $tahun ?? date('Y');

        // Ambil nomor terakhir untuk tahun yang sama
        $lastNumber = self::where('tahun', $tahun)
            ->orderBy('registrasi_id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastNumber) {
            $parts = explode('.', $lastNumber->registrasi_id);
            if (count($parts) == 3) {
                $sequence = intval($parts[2]) + 1;
            }
        }

        return sprintf('MTP.%d.%04d', $tahun, $sequence);
    }

    /**
     * Scope untuk filter berdasarkan tahun
     */
    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope untuk filter berdasarkan cara perolehan
     */
    public function scopeByCaraPerolehan($query, $cara)
    {
        return $query->where('cara_perolehan', $cara);
    }

    /**
     * Scope untuk filter berdasarkan bahan
     */
    public function scopeByBahan($query, $bahan)
    {
        return $query->where('bahan', 'like', '%' . $bahan . '%');
    }

    /**
     * Accessor untuk nama koleksi dengan format khusus untuk batuan
     */
    public function getFormattedNamaKoleksiAttribute()
    {
        $nama = $this->nama_koleksi;

        // Cek apakah ini batuan dan tambahkan jenis batuan jika ada
        if (stripos($nama, 'batu') !== false || stripos($nama, 'batuan') !== false) {
            $jenisBatuan = ['sedimen', 'beku', 'metamorf'];
            foreach ($jenisBatuan as $jenis) {
                if (stripos($nama, $jenis) !== false) {
                    return $nama . ' – Batuan ' . ucfirst($jenis);
                }
            }
        }

        return $nama;
    }

    /**
     * Boot method untuk auto-generate registrasi_id
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registrasi) {
            if (empty($registrasi->registrasi_id)) {
                $registrasi->registrasi_id = self::generateRegistrasiId($registrasi->tahun);
            }
        });
    }
}
