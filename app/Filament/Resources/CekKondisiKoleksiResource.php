<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CekKondisiKoleksiResource\Pages;
use App\Filament\Resources\CekKondisiKoleksiResource\RelationManagers;
use App\Models\CekKondisiKoleksi;
use App\Models\Inventarisasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CekKondisiKoleksiResource extends Resource
{
    protected static ?string $model = CekKondisiKoleksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    
    protected static ?string $navigationLabel = 'Cek Kondisi Koleksi';
    
    protected static ?string $pluralLabel = 'Cek Kondisi Koleksi';
    
    protected static ?string $navigationGroup = 'Cek Kondisi';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengecekan')
                    ->schema([
                        Forms\Components\Select::make('inventarisasi_id')
                            ->label('Nomor Inventarisasi')
                            ->options(Inventarisasi::with('registrasi')->get()->mapWithKeys(function ($item) {
                                return [$item->id => $item->nomor_inventarisasi . ' - ' . $item->registrasi->nama_koleksi];
                            }))
                            ->searchable()
                            ->required()
                            ->preload(),

                        Forms\Components\DatePicker::make('tanggal_cek')
                            ->label('Tanggal Cek')
                            ->required()
                            ->default(now())
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label('Status Kondisi')
                            ->options(CekKondisiKoleksi::getStatusOptions())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Reset fields yang tidak relevan
                                if ($state !== 'dipinjam') {
                                    $set('tanggal_pengembalian', null);
                                }
                                if ($state !== 'rusak') {
                                    $set('detail_kerusakan', null);
                                }
                            }),

                        Forms\Components\TextInput::make('nama_petugas')
                            ->label('Nama Petugas')
                            ->required()
                            ->default(fn () => \Illuminate\Support\Facades\Auth::user()?->name)
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Status')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_pengembalian')
                            ->label('Tanggal Pengembalian')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'dipinjam')
                            ->required(fn (Forms\Get $get) => $get('status') === 'dipinjam')
                            ->native(false)
                            ->after('today')
                            ->helperText('Wajib diisi jika status Dipinjam'),

                        Forms\Components\Textarea::make('detail_kerusakan')
                            ->label('Detail/Deskripsi Kerusakan')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'rusak')
                            ->required(fn (Forms\Get $get) => $get('status') === 'rusak')
                            ->rows(3)
                            ->helperText('Wajib diisi jika status Rusak')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Catatan tambahan tentang pengecekan...'),
                    ]),
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

                Tables\Columns\TextColumn::make('inventarisasi.registrasi.nama_koleksi')
                    ->label('Nama Koleksi')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('tanggal_cek')
                    ->label('Tanggal Cek')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => CekKondisiKoleksi::getStatusOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak' => 'danger',
                        'hilang' => 'gray',
                        'dipinjam' => 'warning',
                        'dikonservasi' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tanggal_pengembalian')
                    ->label('Tgl Pengembalian')
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('detail_kerusakan')
                    ->label('Detail Kerusakan')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nama_petugas')
                    ->label('Petugas')
                    ->searchable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Input')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(CekKondisiKoleksi::getStatusOptions()),

                Tables\Filters\Filter::make('tanggal_cek')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_cek', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_cek', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('dipinjam_jatuh_tempo')
                    ->label('Dipinjam Jatuh Tempo')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('status', 'dipinjam')
                              ->where('tanggal_pengembalian', '<=', now()->addDays(7))
                    ),
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
            ->defaultSort('tanggal_cek', 'desc');
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
            'index' => Pages\ListCekKondisiKoleksis::route('/'),
            'create' => Pages\CreateCekKondisiKoleksi::route('/create'),
            'edit' => Pages\EditCekKondisiKoleksi::route('/{record}/edit'),
        ];
    }
}
