<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisKoleksiResource\Pages;
use App\Filament\Resources\JenisKoleksiResource\RelationManagers;
use App\Models\JenisKoleksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JenisKoleksiResource extends Resource
{
    protected static ?string $model = JenisKoleksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Jenis Koleksi';

    protected static ?string $pluralModelLabel = 'Jenis Koleksi';

    protected static ?string $modelLabel = 'Jenis Koleksi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->label('Kode')
                    ->required()
                    ->maxLength(2)
                    ->placeholder('01')
                    ->helperText('Kode unik 2 digit untuk jenis koleksi'),

                Forms\Components\TextInput::make('nama')
                    ->label('Nama Jenis Koleksi')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Geologika'),

                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->placeholder('Deskripsi lengkap tentang jenis koleksi ini...'),

                Forms\Components\Toggle::make('status')
                    ->label('Status Aktif')
                    ->default(true)
                    ->helperText('Hanya jenis koleksi aktif yang akan ditampilkan di website'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('inventarisasis_count')
                    ->label('Jumlah Koleksi')
                    ->counts('inventarisasis')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
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
            ->defaultSort('kode');
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
            'index' => Pages\ListJenisKoleksis::route('/'),
            'create' => Pages\CreateJenisKoleksi::route('/create'),
            'edit' => Pages\EditJenisKoleksi::route('/{record}/edit'),
        ];
    }
}
