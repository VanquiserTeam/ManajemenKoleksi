<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CekKondisiKelembapan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal_cek',
        'waktu',
        'petugas_1',
        'petugas_2',
        'foto',
        'kelembapan',
        'suhu',
        'lokasi',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_cek' => 'date',
        'waktu' => 'datetime:H:i',
        'kelembapan' => 'decimal:2',
        'suhu' => 'decimal:2',
    ];

    /**
     * Get available status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'rentang_ideal' => 'Rentang Ideal (45%-65%)',
            'kelembapan_tinggi' => 'Kelembapan Tinggi (>65%)',
            'kelembapan_rendah' => 'Kelembapan Rendah (<45%)',
        ];
    }

    /**
     * Get keterangan based on status
     */
    public static function getKeteranganByStatus($status): string
    {
        return match ($status) {
            'rentang_ideal' => 'Pertahankan',
            'kelembapan_tinggi' => 'Waspada pertumbuhan jamur, dan kerusakan material',
            'kelembapan_rendah' => 'Waspada material menjadi rapuh dan kering',
            default => '',
        };
    }

    /**
     * Determine status based on kelembapan value
     */
    public static function determineStatus(float $kelembapan): string
    {
        if ($kelembapan >= 45 && $kelembapan <= 65) {
            return 'rentang_ideal';
        } elseif ($kelembapan > 65) {
            return 'kelembapan_tinggi';
        } else {
            return 'kelembapan_rendah';
        }
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan lokasi
     */
    public function scopeByLokasi($query, $lokasi)
    {
        return $query->where('lokasi', 'like', '%' . $lokasi . '%');
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_cek', $tanggal);
    }

    /**
     * Accessor untuk status dengan badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'rentang_ideal' => 'success',
            'kelembapan_tinggi' => 'danger',
            'kelembapan_rendah' => 'warning',
            default => 'gray',
        };
    }

    /**
     * Boot method untuk auto-set status dan keterangan
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cekKelembapan) {
            // Auto-set status berdasarkan kelembapan
            $cekKelembapan->status = self::determineStatus($cekKelembapan->kelembapan);
            
            // Auto-set keterangan berdasarkan status
            $cekKelembapan->keterangan = self::getKeteranganByStatus($cekKelembapan->status);
        });

        static::updating(function ($cekKelembapan) {
            // Auto-update status dan keterangan jika kelembapan berubah
            if ($cekKelembapan->isDirty('kelembapan')) {
                $cekKelembapan->status = self::determineStatus($cekKelembapan->kelembapan);
                $cekKelembapan->keterangan = self::getKeteranganByStatus($cekKelembapan->status);
            }
        });
    }
}
