<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarisasiResource\Pages;
use App\Filament\Resources\InventarisasiResource\RelationManagers;
use App\Models\Inventarisasi;
use App\Models\Registrasi;
use App\Models\LokasiPenyimpanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use App\Exports\InventarisasiExport;
use App\Imports\InventarisasiImport;

class InventarisasiResource extends Resource
{
    protected static ?string $model = Inventarisasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    
    protected static ?string $navigationLabel = 'Inventarisasi';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Koleksi')
                    ->schema([
                        Forms\Components\Select::make('koleksi_id')
                            ->label('Registrasi Koleksi')
                            ->options(Registrasi::all()->pluck('nama_koleksi', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $registrasi = Registrasi::find($state);
                                    if ($registrasi) {
                                        // Auto-generate nomor inventarisasi berdasarkan ID registrasi
                                        $tahun = date('Y');
                                        $lastNumber = Inventarisasi::where('koleksi_id', $state)
                                            ->orderBy('nomor_inventarisasi', 'desc')
                                            ->first();
                                        
                                        $sequence = 1;
                                        if ($lastNumber) {
                                            $parts = explode('.', $lastNumber->nomor_inventarisasi);
                                            if (count($parts) == 4) {
                                                $sequence = intval($parts[3]) + 1;
                                            }
                                        }
                                        
                                        $nomorInventarisasi = sprintf('01.B.%d.%04d', $tahun, $sequence);
                                        $set('nomor_inventarisasi', $nomorInventarisasi);
                                    }
                                }
                            }),
                        
                        Forms\Components\TextInput::make('nomor_inventarisasi')
                            ->label('Nomor Inventarisasi')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('01.B.2020.0001')
                            ->helperText('Format: [Jenis].[B].[Tahun].[Urutan]'),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Inventarisasi')
                    ->schema([
                        Forms\Components\Select::make('status_kepemilikan')
                            ->label('Status Kepemilikan')
                            ->options(Inventarisasi::getStatusKepemilikanOptions())
                            ->required(),

                        Forms\Components\Select::make('jenis_koleksi')
                            ->label('Jenis Koleksi')
                            ->options(Inventarisasi::getJenisKoleksiOptions())
                            ->required(),

                        Forms\Components\Select::make('kondisi_fisik')
                            ->label('Kondisi Fisik')
                            ->options(Inventarisasi::getKondisiFisikOptions())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state !== 'rusak') {
                                    $set('solusi', null);
                                }
                            }),

                        Forms\Components\Select::make('solusi')
                            ->label('Solusi')
                            ->options(Inventarisasi::getSolusiOptions())
                            ->visible(fn (Forms\Get $get) => $get('kondisi_fisik') === 'rusak')
                            ->helperText('Wajib diisi jika kondisi fisik rusak'),

                        Forms\Components\Select::make('lokasi_penyimpanan')
                            ->label('Lokasi Penyimpanan')
                            ->options(LokasiPenyimpanan::active()->pluck('nama_lokasi', 'kode_lokasi'))
                            ->searchable()
                            ->required(),

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
                Tables\Columns\TextColumn::make('nomor_inventarisasi')
                    ->label('Nomor Inventarisasi')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('registrasi.nama_koleksi')
                    ->label('Nama Koleksi')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('status_kepemilikan')
                    ->label('Status Kepemilikan')
                    ->formatStateUsing(fn (string $state): string => Inventarisasi::getStatusKepemilikanOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'milik_museum' => 'primary',
                        'peminjaman_jangka_pendek' => 'warning',
                        'peminjaman_jangka_panjang' => 'danger',
                        'bmn' => 'success',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('jenis_koleksi')
                    ->label('Jenis Koleksi')
                    ->formatStateUsing(fn (string $state): string => Inventarisasi::getJenisKoleksiOptions()[$state] ?? $state),
                    
                Tables\Columns\TextColumn::make('kondisi_fisik')
                    ->label('Kondisi Fisik')
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
                    ->formatStateUsing(fn (?string $state): string => $state ? (Inventarisasi::getSolusiOptions()[$state] ?? $state) : '-')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('lokasiPenyimpananDetail.nama_lokasi')
                    ->label('Lokasi Penyimpanan')
                    ->searchable()
                    ->limit(25),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Diupdate')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_kepemilikan')
                    ->label('Status Kepemilikan')
                    ->options(Inventarisasi::getStatusKepemilikanOptions()),
                    
                Tables\Filters\SelectFilter::make('jenis_koleksi')
                    ->label('Jenis Koleksi')
                    ->options(Inventarisasi::getJenisKoleksiOptions()),
                    
                Tables\Filters\SelectFilter::make('kondisi_fisik')
                    ->label('Kondisi Fisik')
                    ->options(Inventarisasi::getKondisiFisikOptions()),
                    
                Tables\Filters\SelectFilter::make('lokasi_penyimpanan')
                    ->label('Lokasi Penyimpanan')
                    ->options(LokasiPenyimpanan::active()->pluck('nama_lokasi', 'kode_lokasi')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new InventarisasiExport(), 
                            'inventarisasi-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    }),
                Tables\Actions\Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $import = new InventarisasiImport();
                        \Maatwebsite\Excel\Facades\Excel::import($import, $data['file']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export-selected')
                        ->label('Export Terpilih')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new InventarisasiExport($records->pluck('id')), 
                                'inventarisasi-selected-' . now()->format('Y-m-d') . '.xlsx'
                            );
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListInventarisasis::route('/'),
            'create' => Pages\CreateInventarisasi::route('/create'),
            'edit' => Pages\EditInventarisasi::route('/{record}/edit'),
        ];
    }
}
