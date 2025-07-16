<?php

namespace App\Filament\Resources\LokasiPenyimpananResource\Pages;

use App\Filament\Resources\LokasiPenyimpananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLokasiPenyimpanan extends EditRecord
{
    protected static string $resource = LokasiPenyimpananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
