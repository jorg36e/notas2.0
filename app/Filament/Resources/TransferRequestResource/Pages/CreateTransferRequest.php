<?php

namespace App\Filament\Resources\TransferRequestResource\Pages;

use App\Filament\Resources\TransferRequestResource;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Services\TransferService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTransferRequest extends CreateRecord
{
    protected static string $resource = TransferRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // El administrador crea traslados directos
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $service = new TransferService();

        try {
            $transfer = $service->executeDirectTransfer(
                $data['student_id'],
                $data['destination_sede_id'],
                $data['destination_grade_id'],
                auth()->id(),
                $data['reason'] ?? null,
                $data['notes'] ?? null
            );

            Notification::make()
                ->title('Traslado realizado exitosamente')
                ->body('El estudiante ha sido trasladado a la nueva sede.')
                ->success()
                ->send();

            return $transfer;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al realizar traslado')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
