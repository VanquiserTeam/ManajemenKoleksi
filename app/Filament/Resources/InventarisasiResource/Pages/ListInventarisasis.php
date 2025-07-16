<?php

namespace App\Filament\Resources\InventarisasiResource\Pages;

use App\Filament\Resources\InventarisasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventarisasis extends ListRecords
{
    protected static string $resource = InventarisasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
