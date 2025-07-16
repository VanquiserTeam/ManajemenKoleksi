<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatInventarisasiResource\Pages;
use App\Filament\Resources\RiwayatInventarisasiResource\RelationManagers;
use App\Models\RiwayatInventarisasi;
use App\Models\Inventarisasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatInventarisasiResource extends Resource
{
    protected static ?string $model = RiwayatInventarisasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationLabel = 'Riwayat Inventarisasi';
    
    protected static ?string $pluralLabel = 'Riwayat Inventarisasi';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Riwayat')
                    ->schema([
                        Forms\Components\Select::make('inventarisasi_id')
                            ->label('Inventarisasi')
                            ->options(Inventarisasi::with('koleksi')->get()->pluck('koleksi.nama_koleksi', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('kondisi_fisik_sebelum')
                            ->label('Kondisi Fisik Sebelum')
                            ->options([
                                'baik' => 'Baik',
                                'rusak' => 'Rusak',
                                'hilang' => 'Hilang',
                            ])
                            ->required(),

                        Forms\Components\Select::make('kondisi_fisik_sesudah')
                            ->label('Kondisi Fisik Sesudah')
                            ->options([
                                'baik' => 'Baik',
                                'rusak' => 'Rusak',
                                'hilang' => 'Hilang',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('solusi')
                            ->label('Solusi')
                            ->options([
                                'konservasi_ringan' => 'Konservasi Ringan',
                                'konservasi_sedang' => 'Konservasi Sedang',
                                'konservasi_berat' => 'Konservasi Berat',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('kondisi_fisik_sesudah') === 'rusak'),

                        Forms\Components\DatePicker::make('tanggal_perubahan')
                            ->label('Tanggal Perubahan')
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('petugas')
                            ->label('Petugas')
                            ->maxLength(255)
                            ->default(fn () => \Illuminate\Support\Facades\Auth::user()?->name),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inventarisasi.nomor_inventarisasi')
                    ->label('Nomor Inventarisasi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('inventarisasi.koleksi.nama_koleksi')
                    ->label('Nama Koleksi')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('kondisi_fisik_sebelum')
                    ->label('Kondisi Sebelum')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak' => 'warning',
                        'hilang' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('kondisi_fisik_sesudah')
                    ->label('Kondisi Sesudah')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak' => 'warning',
                        'hilang' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('solusi')
                    ->label('Solusi')
                    ->formatStateUsing(function (?string $state): string {
                        if (!$state) return '-';
                        return match ($state) {
                            'konservasi_ringan' => 'Konservasi Ringan',
                            'konservasi_sedang' => 'Konservasi Sedang',
                            'konservasi_berat' => 'Konservasi Berat',
                            default => ucfirst(str_replace('_', ' ', $state)),
                        };
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal_perubahan')
                    ->label('Tanggal Perubahan')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('petugas')
                    ->label('Petugas')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kondisi_fisik_sesudah')
                    ->label('Kondisi Sesudah')
                    ->options([
                        'baik' => 'Baik',
                        'rusak' => 'Rusak',
                        'hilang' => 'Hilang',
                    ]),

                Tables\Filters\SelectFilter::make('solusi')
                    ->label('Solusi')
                    ->options([
                        'konservasi_ringan' => 'Konservasi Ringan',
                        'konservasi_sedang' => 'Konservasi Sedang',
                        'konservasi_berat' => 'Konservasi Berat',
                    ]),

                Tables\Filters\Filter::make('kerusakan')
                    ->label('Hanya Kerusakan')
                    ->query(fn (Builder $query): Builder => $query->where('kondisi_fisik_sesudah', 'rusak')),

                Tables\Filters\Filter::make('tanggal_perubahan')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_perubahan', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_perubahan', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_perubahan', 'desc');
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
            'index' => Pages\ListRiwayatInventarisasis::route('/'),
            'create' => Pages\CreateRiwayatInventarisasi::route('/create'),
            'edit' => Pages\EditRiwayatInventarisasi::route('/{record}/edit'),
        ];
    }
}
