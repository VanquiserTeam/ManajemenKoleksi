<?php

namespace App\Filament\Resources\CekKondisiKelembapanResource\Pages;

use App\Filament\Resources\CekKondisiKelembapanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCekKondisiKelembapan extends CreateRecord
{
    protected static string $resource = CekKondisiKelembapanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data pengecekan kelembapan berhasil disimpan!';
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title($this->getCreatedNotificationTitle())
            ->body('Status kelembapan telah otomatis ditentukan berdasarkan nilai pengukuran.')
            ->persistent();
    }
}
