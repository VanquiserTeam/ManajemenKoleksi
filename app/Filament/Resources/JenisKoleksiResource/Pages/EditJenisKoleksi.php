<?php

namespace App\Filament\Resources\JenisKoleksiResource\Pages;

use App\Filament\Resources\JenisKoleksiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisKoleksi extends EditRecord
{
    protected static string $resource = JenisKoleksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
