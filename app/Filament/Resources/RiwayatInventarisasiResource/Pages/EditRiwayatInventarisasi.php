<?php

namespace App\Filament\Resources\RiwayatInventarisasiResource\Pages;

use App\Filament\Resources\RiwayatInventarisasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatInventarisasi extends EditRecord
{
    protected static string $resource = RiwayatInventarisasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
