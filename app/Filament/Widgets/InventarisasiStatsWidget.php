<?php

namespace App\Filament\Widgets;

use App\Models\Inventarisasi;
use App\Models\Registrasi;
use App\Models\LokasiPenyimpanan;
use App\Models\RiwayatInventarisasi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventarisasiStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRegistrasi = Registrasi::count();
        $totalInventarisasi = Inventarisasi::count();
        $koleksiTerInventarisasi = Inventarisasi::distinct('koleksi_id')->count('koleksi_id');
        $persentaseInventarisasi = $totalRegistrasi > 0 ? round(($koleksiTerInventarisasi / $totalRegistrasi) * 100, 1) : 0;

        $kondisiBaik = Inventarisasi::where('kondisi_fisik', 'baik')->count();
        $kondisiRusak = Inventarisasi::where('kondisi_fisik', 'rusak')->count();
        $kondisiHilang = Inventarisasi::where('kondisi_fisik', 'hilang')->count();

        $riwayatBulanIni = RiwayatInventarisasi::whereMonth('tanggal_perubahan', now()->month)
            ->whereYear('tanggal_perubahan', now()->year)
            ->count();

        return [
            Stat::make('Total Registrasi', $totalRegistrasi)
                ->description('Koleksi terdaftar')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary'),

            Stat::make('Inventarisasi', $totalInventarisasi)
                ->description("{$persentaseInventarisasi}% dari total registrasi")
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),

            Stat::make('Kondisi Baik', $kondisiBaik)
                ->description('Inventarisasi dengan kondisi baik')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Kondisi Rusak', $kondisiRusak)
                ->description('Memerlukan konservasi')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Kondisi Hilang', $kondisiHilang)
                ->description('Inventarisasi hilang')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Perubahan Bulan Ini', $riwayatBulanIni)
                ->description('Riwayat perubahan kondisi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
}
