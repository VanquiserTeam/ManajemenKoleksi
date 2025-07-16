<?php

namespace App\Filament\Resources\CekKondisiKoleksiResource\Pages;

use App\Filament\Resources\CekKondisiKoleksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCekKondisiKoleksis extends ListRecords
{
    protected static string $resource = CekKondisiKoleksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
