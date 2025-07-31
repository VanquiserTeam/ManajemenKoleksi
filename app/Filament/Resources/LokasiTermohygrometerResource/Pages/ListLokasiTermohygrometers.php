<?php

namespace App\Filament\Resources\LokasiTermohygrometerResource\Pages;

use App\Filament\Resources\LokasiTermohygrometerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLokasiTermohygrometers extends ListRecords
{
    protected static string $resource = LokasiTermohygrometerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
