<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrasiResource\Pages;
use App\Filament\Resources\RegistrasiResource\RelationManagers;
use App\Models\Registrasi;
use App\Exports\RegistrasiExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegistrasiResource extends Resource
{
    protected static ?string $model = Registrasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    protected static ?string $navigationLabel = 'Registrasi Koleksi';

    protected static ?string $pluralLabel = 'Registrasi Koleksi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('registrasi_id')
                            ->label('ID Registrasi')
                            ->placeholder('MTP.2020.0001')
                            // ->unique(ignoreRecord: true)
                            ->helperText('Format: MTP.Tahun.Nomor Urut (akan auto-generate jika kosong)'),

                        Forms\Components\TextInput::make('nama_koleksi')
                            ->label('Nama Koleksi')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Batu Obsidian - Batuan Beku')
                            ->helperText('Untuk batuan, sertakan jenis (Sedimen, Beku, Metamorf)'),

                        Forms\Components\TextInput::make('tahun')
                            ->label('Tahun')
                            ->required()
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y'))
                            ->default(date('Y'))
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state && !$get('registrasi_id')) {
                                    $newId = Registrasi::generateRegistrasiId($state);
                                    $set('registrasi_id', $newId);
                                }
                            }),

                        Forms\Components\Select::make('cara_perolehan')
                            ->label('Cara Perolehan')
                            ->options(Registrasi::getCaraPerolehanOptions())
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Dimensi dan Berat')
                    ->schema([
                        Forms\Components\TextInput::make('panjang')
                            ->label('Panjang (cm)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('cm'),

                        Forms\Components\TextInput::make('lebar')
                            ->label('Lebar (cm)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('cm'),

                        Forms\Components\TextInput::make('tinggi')
                            ->label('Tinggi (cm)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('cm'),

                        Forms\Components\TextInput::make('berat')
                            ->label('Berat (gram)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('gram'),
                    ])->columns(4),

                Forms\Components\Section::make('Detail Koleksi')
                    ->schema([
                        Forms\Components\TextInput::make('asal')
                            ->label('Asal')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('bahan')
                            ->label('Bahan')
                            ->maxLength(255),

                        // Forms\Components\TextInput::make('tanah')
                        //     ->label('Tanah')
                        //     ->maxLength(255),

                        Forms\Components\TextInput::make('warna')
                            ->label('Warna')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('bentuk')
                            ->label('Bentuk')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('narasumber')
                            ->label('Narasumber')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Dokumentasi')
                    ->schema([
                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto Koleksi')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->directory('koleksi-photos')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registrasi_id')
                    ->label('ID Registrasi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(asset('images/no-image.png')),

                Tables\Columns\TextColumn::make('nama_koleksi')
                    ->label('Nama Koleksi')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('cara_perolehan')
                    ->label('Cara Perolehan')
                    ->formatStateUsing(fn (string $state): string => Registrasi::getCaraPerolehanOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hibah' => 'success',
                        'pembelian' => 'primary',
                        'peminjaman' => 'warning',
                        'warisan' => 'info',
                        'hadiah' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('bahan')
                    ->label('Bahan')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('asal')
                    ->label('Asal')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('dimensi')
                    ->label('Dimensi (P×L×T)')
                    ->getStateUsing(function ($record) {
                        $p = $record->panjang ? number_format($record->panjang, 1) : '-';
                        $l = $record->lebar ? number_format($record->lebar, 1) : '-';
                        $t = $record->tinggi ? number_format($record->tinggi, 1) : '-';
                        return "{$p}×{$l}×{$t} cm";
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('berat')
                    ->label('Berat')
                    ->formatStateUsing(fn (?float $state): string => $state ? number_format($state, 1) . ' g' : '-')
                    ->alignRight()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('inventarisasis_count')
                    ->label('Inventarisasi')
                    ->counts('inventarisasis')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

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
                Tables\Filters\SelectFilter::make('cara_perolehan')
                    ->label('Cara Perolehan')
                    ->options(Registrasi::getCaraPerolehanOptions()),

                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $years = Registrasi::distinct()->pluck('tahun')->sort()->toArray();
                        return array_combine($years, $years);
                    }),

                Tables\Filters\Filter::make('has_inventarisasi')
                    ->label('Sudah Diinventarisasi')
                    ->query(fn (Builder $query): Builder => $query->has('inventarisasis')),

                Tables\Filters\Filter::make('no_inventarisasi')
                    ->label('Belum Diinventarisasi')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('inventarisasis')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new RegistrasiExport(),
                            'registrasi-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Registrasi $record) {
                        if ($record->inventarisasis()->count() > 0) {
                            throw new \Exception('Tidak dapat menghapus registrasi yang sudah memiliki inventarisasi.');
                        }
                    }),
                Tables\Actions\Action::make('inventarisasi')
                    ->label('Buat Inventarisasi')
                    ->icon('heroicon-o-plus')
                    ->url(fn (Registrasi $record): string => route('filament.admin.resources.inventarisasis.create', ['koleksi_id' => $record->id]))
                    ->visible(fn (Registrasi $record): bool => $record->inventarisasis()->count() === 0),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->inventarisasis()->count() > 0) {
                                    throw new \Exception('Tidak dapat menghapus registrasi yang sudah memiliki inventarisasi.');
                                }
                            }
                        }),
                    Tables\Actions\BulkAction::make('export-selected')
                        ->label('Export Terpilih')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new RegistrasiExport($records->pluck('id')),
                                'registrasi-selected-' . now()->format('Y-m-d') . '.xlsx'
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
            'index' => Pages\ListRegistrasis::route('/'),
            'create' => Pages\CreateRegistrasi::route('/create'),
            'edit' => Pages\EditRegistrasi::route('/{record}/edit'),
        ];
    }
}
