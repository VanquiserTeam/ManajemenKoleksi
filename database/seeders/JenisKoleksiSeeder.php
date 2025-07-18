<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisKoleksi;

class JenisKoleksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisKoleksi = [
            [
                'kode' => '01',
                'nama' => 'Geologika',
                'deskripsi' => 'Koleksi yang berkaitan dengan ilmu geologi, termasuk mineral, batuan, fosil, dan material geologis lainnya.',
                'status' => true,
            ],
            [
                'kode' => '02',
                'nama' => 'Biologika',
                'deskripsi' => 'Koleksi yang berkaitan dengan makhluk hidup, termasuk spesimen tumbuhan, hewan, dan organisme lainnya.',
                'status' => true,
            ],
            [
                'kode' => '03',
                'nama' => 'Etnografika',
                'deskripsi' => 'Koleksi yang berkaitan dengan budaya dan kehidupan masyarakat, termasuk alat tradisional, pakaian, dan artefak budaya.',
                'status' => true,
            ],
            [
                'kode' => '04',
                'nama' => 'Arkeologika',
                'deskripsi' => 'Koleksi yang berkaitan dengan peninggalan arkeologi, termasuk artefak kuno, alat batu, dan peninggalan sejarah.',
                'status' => true,
            ],
            [
                'kode' => '05',
                'nama' => 'Historika',
                'deskripsi' => 'Koleksi yang berkaitan dengan sejarah, termasuk dokumen bersejarah, foto, dan benda-benda bersejarah.',
                'status' => true,
            ],
            [
                'kode' => '06',
                'nama' => 'Numismatika',
                'deskripsi' => 'Koleksi yang berkaitan dengan mata uang, termasuk koin, uang kertas, dan alat pembayaran tradisional.',
                'status' => true,
            ],
            [
                'kode' => '07',
                'nama' => 'Filologika',
                'deskripsi' => 'Koleksi yang berkaitan dengan naskah dan tulisan, termasuk manuskrip kuno, prasasti, dan dokumen tertulis.',
                'status' => true,
            ],
            [
                'kode' => '08',
                'nama' => 'Keramonologika',
                'deskripsi' => 'Koleksi yang berkaitan dengan keramik dan tembikar, termasuk gerabah, porselen, dan keramik tradisional.',
                'status' => true,
            ],
            [
                'kode' => '09',
                'nama' => 'Seni Rupa',
                'deskripsi' => 'Koleksi yang berkaitan dengan seni rupa, termasuk lukisan, patung, ukiran, dan karya seni lainnya.',
                'status' => true,
            ],
            [
                'kode' => '10',
                'nama' => 'Teknologika',
                'deskripsi' => 'Koleksi yang berkaitan dengan teknologi dan peralatan, termasuk alat-alat teknologi tradisional dan modern.',
                'status' => true,
            ],
        ];

        foreach ($jenisKoleksi as $jenis) {
            JenisKoleksi::create($jenis);
        }
    }
}
