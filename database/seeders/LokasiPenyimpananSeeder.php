<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LokasiPenyimpanan;

class LokasiPenyimpananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lokasi = [
            [
                'nama_lokasi' => 'Galeri Tanah, Iklim dan Lingkungan – Gedung A',
                'kode_lokasi' => 'GA',
                'deskripsi' => 'Galeri yang menampilkan koleksi tentang tanah, iklim dan lingkungan di Gedung A',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Galeri Pangan dan Peradaban – Gedung C1',
                'kode_lokasi' => 'GC1',
                'deskripsi' => 'Galeri yang menampilkan koleksi tentang pangan dan peradaban di Gedung C1',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Galeri Kebijakan dan Komoditas – Gedung C2',
                'kode_lokasi' => 'GC2',
                'deskripsi' => 'Galeri yang menampilkan koleksi tentang kebijakan dan komoditas di Gedung C2',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Galeri Teknologi Pertanian Masa Depan – Gedung C3',
                'kode_lokasi' => 'GC3',
                'deskripsi' => 'Galeri yang menampilkan koleksi tentang teknologi pertanian masa depan di Gedung C3',
                'is_active' => true,
            ],
            [
                'nama_lokasi' => 'Galeri Peternakan – Gedung D',
                'kode_lokasi' => 'GD',
                'deskripsi' => 'Galeri yang menampilkan koleksi tentang peternakan di Gedung D',
                'is_active' => true,
            ],
        ];

        foreach ($lokasi as $item) {
            LokasiPenyimpanan::create($item);
        }
    }
}
