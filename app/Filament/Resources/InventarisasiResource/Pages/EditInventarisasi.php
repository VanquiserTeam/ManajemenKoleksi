<?php

namespace App\Filament\Resources\InventarisasiResource\Pages;

use App\Filament\Resources\InventarisasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventarisasi extends EditRecord
{
    protected static string $resource = InventarisasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
