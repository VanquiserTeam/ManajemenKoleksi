<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Registrasi;

class RegistrasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $registrasi = [
            [
                'registrasi_id' => 'MTP.2020.0001',
                'nama_koleksi' => 'Batu Obsidian – Batuan Beku',
                'tahun' => 2020,
                'cara_perolehan' => 'hibah',
                'panjang' => 15.5,
                'lebar' => 10.2,
                'tinggi' => 8.7,
                'berat' => 450.5,
                'asal' => 'Gunung Merapi, Yogyakarta',
                'bahan' => 'Obsidian',
                'tanah' => 'Vulkanik',
                'warna' => 'Hitam mengkilap',
                'bentuk' => 'Tidak beraturan',
                'narasumber' => 'Prof. Dr. Geologi Univ',
            ],
            [
                'registrasi_id' => 'MTP.2021.0001',
                'nama_koleksi' => 'Batu Gamping – Batuan Sedimen',
                'tahun' => 2021,
                'cara_perolehan' => 'pembelian',
                'panjang' => 20.0,
                'lebar' => 15.5,
                'tinggi' => 12.3,
                'berat' => 890.2,
                'asal' => 'Gunungkidul, Yogyakarta',
                'bahan' => 'Kalsium Karbonat',
                'tanah' => 'Karst',
                'warna' => 'Putih keabu-abuan',
                'bentuk' => 'Berlapis',
                'narasumber' => 'Surveyor Geologis Daerah',
            ],
            [
                'registrasi_id' => 'MTP.2022.0001',
                'nama_koleksi' => 'Batu Marmer – Batuan Metamorf',
                'tahun' => 2022,
                'cara_perolehan' => 'warisan',
                'panjang' => 25.8,
                'lebar' => 18.4,
                'tinggi' => 15.6,
                'berat' => 1250.8,
                'asal' => 'Tulungagung, Jawa Timur',
                'bahan' => 'Marmer',
                'tanah' => 'Metamorf',
                'warna' => 'Putih dengan urat abu-abu',
                'bentuk' => 'Kristal',
                'narasumber' => 'Pengrajin Marmer Lokal',
            ],
            [
                'registrasi_id' => 'MTP.2023.0001',
                'nama_koleksi' => 'Fosil Trilobita',
                'tahun' => 2023,
                'cara_perolehan' => 'hadiah',
                'panjang' => 8.5,
                'lebar' => 6.2,
                'tinggi' => 2.1,
                'berat' => 125.3,
                'asal' => 'Maroko',
                'bahan' => 'Batu fosil',
                'tanah' => 'Sedimen laut purba',
                'warna' => 'Coklat tua',
                'bentuk' => 'Oval segmented',
                'narasumber' => 'Kolektor Fosil Internasional',
            ],
            [
                'registrasi_id' => 'MTP.2024.0001',
                'nama_koleksi' => 'Kristal Kuarsa',
                'tahun' => 2024,
                'cara_perolehan' => 'peminjaman',
                'panjang' => 12.8,
                'lebar' => 8.9,
                'tinggi' => 10.5,
                'berat' => 380.7,
                'asal' => 'Brazil',
                'bahan' => 'Silika',
                'tanah' => 'Hidrotermal',
                'warna' => 'Bening dengan kilau',
                'bentuk' => 'Heksagonal prisma',
                'narasumber' => 'Museum Geologi Bandung',
            ],
        ];

        foreach ($registrasi as $item) {
            Registrasi::create($item);
        }
    }
}
