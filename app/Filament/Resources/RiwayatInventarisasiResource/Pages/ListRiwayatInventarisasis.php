<?php

namespace App\Filament\Resources\RiwayatInventarisasiResource\Pages;

use App\Filament\Resources\RiwayatInventarisasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatInventarisasis extends ListRecords
{
    protected static string $resource = RiwayatInventarisasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
