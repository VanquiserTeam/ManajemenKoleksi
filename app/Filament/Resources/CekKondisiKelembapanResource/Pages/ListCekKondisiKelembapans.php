<?php

namespace App\Filament\Resources\CekKondisiKelembapanResource\Pages;

use App\Filament\Resources\CekKondisiKelembapanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCekKondisiKelembapans extends ListRecords
{
    protected static string $resource = CekKondisiKelembapanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pengecekan Kelembapan')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(fn () => \App\Models\CekKondisiKelembapan::count()),

            'baik' => Tab::make('Status Baik')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'baik'))
                ->badge(fn () => \App\Models\CekKondisiKelembapan::where('status', 'baik')->count())
                ->badgeColor('success'),

            'kurang_baik' => Tab::make('Kurang Baik')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'kurang_baik'))
                ->badge(fn () => \App\Models\CekKondisiKelembapan::where('status', 'kurang_baik')->count())
                ->badgeColor('warning'),

            'buruk' => Tab::make('Status Buruk')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'buruk'))
                ->badge(fn () => \App\Models\CekKondisiKelembapan::where('status', 'buruk')->count())
                ->badgeColor('danger'),

            'hari_ini' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('tanggal_cek', today()))
                ->badge(fn () => \App\Models\CekKondisiKelembapan::whereDate('tanggal_cek', today())->count())
                ->badgeColor('primary'),
        ];
    }
}
