<?php

namespace App\Filament\Resources\CekKondisiKelembapanResource\Pages;

use App\Filament\Resources\CekKondisiKelembapanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCekKondisiKelembapan extends EditRecord
{
    protected static string $resource = CekKondisiKelembapanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data pengecekan kelembapan berhasil diperbarui!';
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title($this->getSavedNotificationTitle())
            ->body('Status dan keterangan telah otomatis disesuaikan dengan nilai kelembapan terbaru.');
    }
}
