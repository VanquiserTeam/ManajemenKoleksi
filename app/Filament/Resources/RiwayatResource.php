<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatResource\Pages;
use App\Filament\Resources\RiwayatResource\RelationManagers;
use App\Models\Registrasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatResource extends Resource
{
    protected static ?string $model = Registrasi::class;

    protected static ?string $slug = 'riwayat';
    protected static ?string $label = 'Riwayat';
    protected static ?string $navigationLabel = 'Riwayat';

    protected static ?string $pluralLabel = 'Riwayat';

    protected static ?string $navigationGroup = 'Riwayat';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 9999;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('nama_koleksi')->sortable()->searchable(),
                //status diambil dari relasi cek kondisi

                Tables\Columns\TextColumn::make('inventarisasis.cekKondisiKoleksis.status')
                    ->label('Status')
                    ->badge(),

                // keterangan menjadi koleksi
                // berisi
                // Menjadi Koleksi : Tangal input data registrasi
                // Dipinjam : estimasi tanggal pengembalian
                // Dikonservasi : tanggal mulai konservasi

                Tables\Columns\TextColumn::make('keterangan_label')
                    ->label('Keterangan')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayats::route('/'),
            'create' => Pages\CreateRiwayat::route('/create'),
            // 'edit' => Pages\EditRiwayat::route('/{record}/edit'),
        ];
    }
}
