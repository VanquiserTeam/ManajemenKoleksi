<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CekKondisiKelembapanResource\Pages;
use App\Filament\Resources\CekKondisiKelembapanResource\RelationManagers;
use App\Models\CekKondisiKelembapan;
use App\Models\LokasiPenyimpanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CekKondisiKelembapanResource extends Resource
{
    protected static ?string $model = CekKondisiKelembapan::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    
    protected static ?string $navigationLabel = 'Cek Kondisi Kelembapan';
    
    protected static ?string $pluralLabel = 'Cek Kondisi Kelembapan';
    
    protected static ?string $navigationGroup = 'Cek Kondisi';
    
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengecekan')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_cek')
                            ->label('Tanggal Cek')
                            ->required()
                            ->default(now())
                            ->native(false),

                        Forms\Components\TimePicker::make('waktu')
                            ->label('Waktu')
                            ->required()
                            ->default(now()->format('H:i'))
                            ->seconds(false),

                        Forms\Components\TextInput::make('petugas_1')
                            ->label('Petugas 1')
                            ->required()
                            ->default(fn () => \Illuminate\Support\Facades\Auth::user()?->name)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('petugas_2')
                            ->label('Petugas 2')
                            ->maxLength(255)
                            ->placeholder('Opsional'),
                    ])->columns(2),

                Forms\Components\Section::make('Data Pengukuran')
                    ->schema([
                        Forms\Components\TextInput::make('kelembapan')
                            ->label('Kelembapan (%)')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->live()
                            ->helperText('Rentang ideal: 45%-65%')
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    // Auto-set status dan keterangan berdasarkan kelembapan
                                    $status = CekKondisiKelembapan::determineStatus((float) $state);
                                    $keterangan = CekKondisiKelembapan::getKeteranganByStatus($status);
                                    
                                    $set('status_display', CekKondisiKelembapan::getStatusOptions()[$status]);
                                    $set('keterangan_display', $keterangan);
                                }
                            }),

                        Forms\Components\TextInput::make('suhu')
                            ->label('Suhu (°C)')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->suffix('°C'),

                        Forms\Components\Select::make('lokasi')
                            ->label('Lokasi')
                            ->options(LokasiPenyimpanan::active()->pluck('nama_lokasi', 'nama_lokasi'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Placeholder::make('status_display')
                            ->label('Status (Auto)')
                            ->content(fn (Forms\Get $get) => $get('status_display') ?? 'Akan muncul otomatis berdasarkan kelembapan'),

                        Forms\Components\Placeholder::make('keterangan_display')
                            ->label('Keterangan (Auto)')
                            ->content(fn (Forms\Get $get) => $get('keterangan_display') ?? 'Akan muncul otomatis berdasarkan status')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Dokumentasi')
                    ->schema([
                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto Alat Ukur/Kondisi')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->directory('kelembapan-photos')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_cek')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('waktu')
                    ->label('Waktu')
                    ->formatStateUsing(fn (string $state): string => \Carbon\Carbon::parse($state)->format('H:i'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kelembapan')
                    ->label('Kelembapan')
                    ->formatStateUsing(fn ($state): string => number_format($state, 1) . '%')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($state): string => match (true) {
                        $state < 45 => 'danger',
                        $state >= 45 && $state <= 65 => 'success',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('suhu')
                    ->label('Suhu')
                    ->formatStateUsing(fn ($state): string => number_format($state, 1) . '°C')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'baik' => 'success',
                        'kurang_baik' => 'warning',
                        'buruk' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => \App\Models\CekKondisiKelembapan::getStatusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('petugas_1')
                    ->label('Petugas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(\App\Models\CekKondisiKelembapan::getStatusOptions())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('lokasi')
                    ->label('Lokasi')
                    ->options(\App\Models\LokasiPenyimpanan::active()->pluck('nama_lokasi', 'nama_lokasi'))
                    ->searchable()
                    ->multiple(),

                Tables\Filters\Filter::make('kelembapan_ideal')
                    ->label('Kelembapan Ideal (45%-65%)')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('kelembapan', [45, 65])),

                Tables\Filters\Filter::make('kelembapan_rendah')
                    ->label('Kelembapan Rendah (<45%)')
                    ->query(fn (Builder $query): Builder => $query->where('kelembapan', '<', 45)),

                Tables\Filters\Filter::make('kelembapan_tinggi')
                    ->label('Kelembapan Tinggi (>65%)')
                    ->query(fn (Builder $query): Builder => $query->where('kelembapan', '>', 65)),

                Tables\Filters\Filter::make('tanggal_hari_ini')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query): Builder => $query->whereDate('tanggal_cek', today())),

                Tables\Filters\Filter::make('tanggal_minggu_ini')
                    ->label('Minggu Ini')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('tanggal_cek', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('tanggal_cek', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->poll('60s')
            ->emptyStateHeading('Belum ada data kelembapan')
            ->emptyStateDescription('Silakan tambahkan data pengukuran kelembapan pertama.')
            ->emptyStateIcon('heroicon-o-beaker');
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
            'index' => Pages\ListCekKondisiKelembapans::route('/'),
            'create' => Pages\CreateCekKondisiKelembapan::route('/create'),
            'edit' => Pages\EditCekKondisiKelembapan::route('/{record}/edit'),
        ];
    }
}
