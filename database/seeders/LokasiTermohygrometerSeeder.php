<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LokasiTermohygrometer;

class LokasiTermohygrometerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_lokasi' => 'Koleksi Garu dan Mata Bajak',
                'kode_lokasi' => 'LGMB',
                'deskripsi' => 'Lokasi untuk koleksi garu dan mata bajak.',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Lumbung Rumah Tani',
                'kode_lokasi' => 'LRT',
                'deskripsi' => 'Lumbung penyimpanan rumah tani.',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Buku Rumphius',
                'kode_lokasi' => 'BR',
                'deskripsi' => 'Koleksi buku Rumphius.',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Koleksi Rempah',
                'kode_lokasi' => 'KR',
                'deskripsi' => 'Koleksi rempah-rempah.',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Lukisan Bunga',
                'kode_lokasi' => 'LB',
                'deskripsi' => 'Koleksi lukisan bunga.',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Hall of Fame Mentan',
                'kode_lokasi' => 'HOFM',
                'deskripsi' => 'Hall of Fame Menteri Pertanian.',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Aero Feertilizer',
                'kode_lokasi' => 'AF',
                'deskripsi' => 'Lokasi Aero Feertilizer.',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Tabung Penampung Susu',
                'kode_lokasi' => 'TPS',
                'deskripsi' => 'Tabung penampung susu.',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Koleksi Patung Kuda Cahaya Nagari',
                'kode_lokasi' => 'KPKCN',
                'deskripsi' => 'Koleksi patung kuda Cahaya Nagari.',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Patung Sapi Gatotkaca',
                'kode_lokasi' => 'PSG',
                'deskripsi' => 'Patung sapi Gatotkaca.',
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            LokasiTermohygrometer::create($item);
        }
    }
}
