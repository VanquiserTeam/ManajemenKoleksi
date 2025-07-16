<?php

namespace App\Filament\Resources\CekKondisiKelembapanResource\Pages;

use App\Filament\Resources\CekKondisiKelembapanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

use Filament\Infolists\Components\ImageEntry;

class ViewCekKondisiKelembapan extends ViewRecord
{
    protected static string $resource = CekKondisiKelembapanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit Data'),
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pengecekan')
                    ->schema([
                        TextEntry::make('tanggal_cek')
                            ->label('Tanggal Cek')
                            ->date('d F Y'),
                            
                        TextEntry::make('waktu')
                            ->label('Waktu')
                            ->formatStateUsing(fn (string $state): string => \Carbon\Carbon::parse($state)->format('H:i') . ' WIB'),
                            
                        TextEntry::make('petugas_1')
                            ->label('Petugas 1'),
                            
                        TextEntry::make('petugas_2')
                            ->label('Petugas 2')
                            ->placeholder('Tidak ada'),
                    ])->columns(2),

                Section::make('Data Pengukuran')
                    ->schema([
                        TextEntry::make('kelembapan')
                            ->label('Kelembapan')
                            ->formatStateUsing(fn ($state): string => number_format($state, 1) . '%')
                            ->color(fn ($state): string => match (true) {
                                $state < 45 => 'danger',
                                $state >= 45 && $state <= 65 => 'success',
                                default => 'warning',
                            })
                            ->weight('bold'),
                            
                        TextEntry::make('suhu')
                            ->label('Suhu')
                            ->formatStateUsing(fn ($state): string => number_format($state, 1) . '°C'),
                            
                        TextEntry::make('lokasi')
                            ->label('Lokasi'),
                            
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'baik' => 'success',
                                'kurang_baik' => 'warning',
                                'buruk' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => \App\Models\CekKondisiKelembapan::getStatusOptions()[$state] ?? $state),
                            
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Dokumentasi')
                    ->schema([
                        ImageEntry::make('foto')
                            ->label('Foto')
                            ->placeholder('Tidak ada foto')
                            ->hiddenLabel(),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => !$record->foto),

                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d F Y, H:i'),
                            
                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime('d F Y, H:i'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
