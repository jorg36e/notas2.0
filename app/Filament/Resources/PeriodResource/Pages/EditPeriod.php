<?php

namespace App\Filament\Resources\PeriodResource\Pages;

use App\Filament\Resources\PeriodResource;
use App\Models\Period;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPeriod extends EditRecord
{
    protected static string $resource = PeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validar que no exista duplicado de número en el mismo año escolar
        $exists = Period::where('school_year_id', $data['school_year_id'])
            ->where('number', $data['number'])
            ->where('id', '!=', $this->record->id)
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Error')
                ->body('Ya existe un periodo con ese número para el año escolar seleccionado.')
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
