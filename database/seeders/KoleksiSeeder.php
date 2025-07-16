<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Koleksi;

class KoleksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Koleksi::create([
            'no_registrasi' => 'MTP.2024.0001',
            'no_inventaris' => null,
            'nama_koleksi' => 'Ascoria - Beku',
            'tahun_perolehan' => 2024,
            'cara_perolehan' => 'Hibah',
            'asal_perolehan' => 'Kusumo Nugroho',
            'panjang' => 6.3,
            'lebar' => 9.9,
            'tinggi' => 7.0,
            'asal_daerah' => 'Aceh, Selatan, Aceh',
            'warna' => null,
            'bentuk' => null,
            'foto' => null,
            'bahan' => 'Felspar Horn belenda',
            'narasumber' => 'Hikmatullah (2016)',
            'jenis_koleksi' => '01 Geologika',
            'lokasi_penyimpanan' => 'Iklim',
            'deskripsi' => null,
            'status' => '1'
        ]);
    }
}
