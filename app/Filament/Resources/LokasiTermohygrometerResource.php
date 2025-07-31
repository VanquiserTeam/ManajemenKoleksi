<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LokasiTermohygrometerResource\Pages;
use App\Filament\Resources\LokasiTermohygrometerResource\RelationManagers;
use App\Models\LokasiTermohygrometer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LokasiTermohygrometerResource extends Resource
{
    protected static ?string $model = LokasiTermohygrometer::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Lokasi Termohygrometer';

    protected static ?string $pluralLabel = 'Lokasi Termohygrometer';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Lokasi Termohygrometer')
                    ->schema([
                        Forms\Components\TextInput::make('nama_lokasi')
                            ->label('Nama Lokasi')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Galeri Tanah, Iklim dan Lingkungan – Gedung A'),

                        Forms\Components\TextInput::make('kode_lokasi')
                            ->label('Kode Lokasi')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Contoh: GA-TH')
                            ->helperText('Kode unik untuk lokasi termohygrometer'),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Deskripsi detail tentang lokasi termohygrometer'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan jika lokasi tidak lagi digunakan'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_lokasi')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nama_lokasi')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ]),
            ])
            ->defaultSort('kode_lokasi');
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
            'index' => Pages\ListLokasiTermohygrometers::route('/'),
            'create' => Pages\CreateLokasiTermohygrometer::route('/create'),
            'edit' => Pages\EditLokasiTermohygrometer::route('/{record}/edit'),
        ];
    }
}
