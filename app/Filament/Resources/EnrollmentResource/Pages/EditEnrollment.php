<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Filament\Resources\EnrollmentResource;
use App\Models\Enrollment;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEnrollment extends EditRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validar que el estudiante no tenga otra matrícula en el mismo año escolar
        $exists = Enrollment::where('school_year_id', $data['school_year_id'])
            ->where('student_id', $data['student_id'])
            ->where('id', '!=', $this->record->id)
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Error')
                ->body('Este estudiante ya tiene otra matrícula registrada para el año escolar seleccionado.')
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
