<?php

namespace App\Filament\Resources\CekKondisiKoleksiResource\Pages;

use App\Filament\Resources\CekKondisiKoleksiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCekKondisiKoleksi extends EditRecord
{
    protected static string $resource = CekKondisiKoleksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
