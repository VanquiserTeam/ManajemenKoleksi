<?php

namespace App\Filament\Resources\LokasiTermohygrometerResource\Pages;

use App\Filament\Resources\LokasiTermohygrometerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLokasiTermohygrometer extends EditRecord
{
    protected static string $resource = LokasiTermohygrometerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
