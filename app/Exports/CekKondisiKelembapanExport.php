<?php

namespace App\Exports;

use App\Models\CekKondisiKelembapan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CekKondisiKelembapanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $selectedIds;

    public function __construct($selectedIds = null)
    {
        $this->selectedIds = $selectedIds;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = CekKondisiKelembapan::query();

        if ($this->selectedIds) {
            $query->whereIn('id', $this->selectedIds);
        }

        return $query->orderBy('tanggal_cek', 'desc')->get();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Cek',
            'Waktu',
            'Lokasi',
            'Kelembapan (%)',
            'Suhu (°C)',
            'Status',
            'Keterangan',
            'Petugas 1',
            'Petugas 2',
            'Tanggal Dibuat',
            'Tanggal Diupdate'
        ];
    }

    /**
    * @param \App\Models\CekKondisiKelembapan $cekKelembapan
    * @return array
    */
    public function map($cekKelembapan): array
    {
        return [
            $cekKelembapan->id,
            $cekKelembapan->tanggal_cek->format('d/m/Y'),
            $cekKelembapan->waktu ? \Carbon\Carbon::parse($cekKelembapan->waktu)->format('H:i') : '-',
            $cekKelembapan->lokasi,
            number_format($cekKelembapan->kelembapan, 2),
            number_format($cekKelembapan->suhu, 2),
            $this->formatStatus($cekKelembapan->status),
            $cekKelembapan->keterangan ?? '-',
            $cekKelembapan->petugas_1 ?? '-',
            $cekKelembapan->petugas_2 ?? '-',
            $cekKelembapan->created_at->format('d/m/Y H:i:s'),
            $cekKelembapan->updated_at->format('d/m/Y H:i:s'),
        ];
    }

    private function formatStatus($status)
    {
        $options = CekKondisiKelembapan::getStatusOptions();
        return $options[$status] ?? $status;
    }
}
