<?php

namespace App\Exports;

use App\Models\Registrasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RegistrasiExport implements FromCollection, WithHeadings, WithMapping
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
        $query = Registrasi::with(['inventarisasis']);

        if ($this->selectedIds) {
            $query->whereIn('id', $this->selectedIds);
        }

        return $query->get();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID',
            'ID Registrasi',
            'Nama Koleksi',
            'Tahun',
            'Cara Perolehan',
            'Panjang (cm)',
            'Lebar (cm)',
            'Tinggi (cm)',
            'Berat (gram)',
            'Asal',
            'Bahan',
            'Warna',
            'Bentuk',
            'Narasumber',
            'Jumlah Inventarisasi',
            'Tanggal Dibuat',
            'Tanggal Diupdate'
        ];
    }

    /**
    * @param \App\Models\Registrasi $registrasi
    * @return array
    */
    public function map($registrasi): array
    {
        return [
            $registrasi->id,
            $registrasi->registrasi_id,
            $registrasi->nama_koleksi,
            $registrasi->tahun,
            $this->formatCaraPerolehan($registrasi->cara_perolehan),
            $registrasi->panjang ? number_format($registrasi->panjang, 2) : '-',
            $registrasi->lebar ? number_format($registrasi->lebar, 2) : '-',
            $registrasi->tinggi ? number_format($registrasi->tinggi, 2) : '-',
            $registrasi->berat ? number_format($registrasi->berat, 2) : '-',
            $registrasi->asal ?? '-',
            $registrasi->bahan ?? '-',
            $registrasi->warna ?? '-',
            $registrasi->bentuk ?? '-',
            $registrasi->narasumber ?? '-',
            $registrasi->inventarisasis->count(),
            $registrasi->created_at->format('d/m/Y H:i:s'),
            $registrasi->updated_at->format('d/m/Y H:i:s'),
        ];
    }

    private function formatCaraPerolehan($cara)
    {
        $options = Registrasi::getCaraPerolehanOptions();
        return $options[$cara] ?? $cara;
    }
}
