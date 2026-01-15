<?php

namespace App\Filament\Resources\TeachingAssignmentResource\Pages;

use App\Filament\Resources\TeachingAssignmentResource;
use App\Models\TeachingAssignment;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditTeachingAssignment extends EditRecord
{
    protected static string $resource = TeachingAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validar que no exista una asignación duplicada
        $exists = TeachingAssignment::where('school_year_id', $data['school_year_id'])
            ->where('teacher_id', $data['teacher_id'])
            ->where('sede_id', $data['sede_id'])
            ->where('grade_id', $data['grade_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('id', '!=', $this->record->id)
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Error')
                ->body('Esta asignación docente ya existe para el año escolar seleccionado.')
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
