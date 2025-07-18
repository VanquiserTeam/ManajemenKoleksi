<?php

namespace App\Filament\Resources\JenisKoleksiResource\Pages;

use App\Filament\Resources\JenisKoleksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisKoleksis extends ListRecords
{
    protected static string $resource = JenisKoleksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
