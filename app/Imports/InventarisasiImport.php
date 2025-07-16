<?php

namespace App\Imports;

use App\Models\Inventarisasi;
use App\Models\Koleksi;
use App\Models\LokasiPenyimpanan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class InventarisasiImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Cari koleksi berdasarkan nama atau nomor registrasi
        $koleksi = Koleksi::where('nama_koleksi', $row['nama_koleksi'])
                         ->orWhere('no_registrasi', $row['no_registrasi'] ?? '')
                         ->first();

        if (!$koleksi) {
            return null; // Skip jika koleksi tidak ditemukan
        }

        // Cari lokasi penyimpanan
        $lokasi = LokasiPenyimpanan::where('nama_lokasi', $row['lokasi_penyimpanan'])
                                  ->orWhere('kode_lokasi', $row['lokasi_penyimpanan'])
                                  ->first();

        $lokasiCode = $lokasi ? $lokasi->kode_lokasi : $row['lokasi_penyimpanan'];

        return new Inventarisasi([
            'koleksi_id' => $koleksi->id,
            'nomor_inventarisasi' => $row['nomor_inventarisasi'],
            'status_kepemilikan' => $this->parseStatusKepemilikan($row['status_kepemilikan']),
            'jenis_koleksi' => $this->parseJenisKoleksi($row['jenis_koleksi']),
            'kondisi_fisik' => strtolower($row['kondisi_fisik']),
            'solusi' => isset($row['solusi']) && $row['solusi'] !== '-' ? $this->parseSolusi($row['solusi']) : null,
            'lokasi_penyimpanan' => $lokasiCode,
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    /**
    * @return array
    */
    public function rules(): array
    {
        return [
            'nomor_inventarisasi' => 'required|unique:inventarisasis,nomor_inventarisasi',
            'nama_koleksi' => 'required',
            'status_kepemilikan' => 'required',
            'jenis_koleksi' => 'required',
            'kondisi_fisik' => 'required|in:baik,rusak,hilang',
            'lokasi_penyimpanan' => 'required',
        ];
    }

    private function parseStatusKepemilikan($status)
    {
        $mapping = [
            'Milik Museum' => 'milik_museum',
            'Peminjaman Jangka Pendek' => 'peminjaman_jangka_pendek',
            'Peminjaman Jangka Panjang' => 'peminjaman_jangka_panjang',
            'BMN' => 'bmn',
        ];

        return $mapping[$status] ?? strtolower(str_replace(' ', '_', $status));
    }

    private function parseJenisKoleksi($jenis)
    {
        // Ekstrak nomor dari format "01 - Geologika"
        if (preg_match('/^(\d{2})/', $jenis, $matches)) {
            return $matches[1];
        }
        
        // Mapping berdasarkan nama
        $mapping = [
            'Geologika' => '01',
            'Biologika' => '02',
            'Etnografika' => '03',
            'Arkeologika' => '04',
            'Historika' => '05',
            'Numismatika' => '06',
            'Filologika' => '07',
            'Keramonologika' => '08',
            'Seni Rupa' => '09',
            'Teknologika' => '10',
        ];

        return $mapping[$jenis] ?? $jenis;
    }

    private function parseSolusi($solusi)
    {
        $mapping = [
            'Konservasi Ringan' => 'konservasi_ringan',
            'Konservasi Sedang' => 'konservasi_sedang',
            'Konservasi Berat' => 'konservasi_berat',
        ];

        return $mapping[$solusi] ?? strtolower(str_replace(' ', '_', $solusi));
    }
}
