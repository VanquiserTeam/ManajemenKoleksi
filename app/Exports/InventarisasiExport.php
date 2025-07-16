<?php

namespace App\Exports;

use App\Models\Inventarisasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventarisasiExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Inventarisasi::with(['koleksi', 'lokasiPenyimpananDetail'])->get();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID',
            'Nomor Inventarisasi',
            'Nama Koleksi',
            'Status Kepemilikan',
            'Jenis Koleksi',
            'Kondisi Fisik',
            'Solusi',
            'Lokasi Penyimpanan',
            'Keterangan',
            'Tanggal Dibuat',
            'Tanggal Diupdate'
        ];
    }

    /**
    * @param \App\Models\Inventarisasi $inventarisasi
    * @return array
    */
    public function map($inventarisasi): array
    {
        return [
            $inventarisasi->id,
            $inventarisasi->nomor_inventarisasi,
            $inventarisasi->koleksi->nama_koleksi ?? '-',
            $this->formatStatusKepemilikan($inventarisasi->status_kepemilikan),
            $this->formatJenisKoleksi($inventarisasi->jenis_koleksi),
            ucfirst($inventarisasi->kondisi_fisik),
            $inventarisasi->solusi ? $this->formatSolusi($inventarisasi->solusi) : '-',
            $inventarisasi->lokasiPenyimpananDetail->nama_lokasi ?? $inventarisasi->lokasi_penyimpanan,
            $inventarisasi->keterangan ?? '-',
            $inventarisasi->created_at->format('d/m/Y H:i:s'),
            $inventarisasi->updated_at->format('d/m/Y H:i:s'),
        ];
    }

    private function formatStatusKepemilikan($status)
    {
        $options = Inventarisasi::getStatusKepemilikanOptions();
        return $options[$status] ?? $status;
    }

    private function formatJenisKoleksi($jenis)
    {
        $options = Inventarisasi::getJenisKoleksiOptions();
        return $options[$jenis] ?? $jenis;
    }

    private function formatSolusi($solusi)
    {
        $options = Inventarisasi::getSolusiOptions();
        return $options[$solusi] ?? $solusi;
    }
}
