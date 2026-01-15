<?php

namespace App\Filament\Resources\SchoolYearResource\Pages;

use App\Filament\Resources\SchoolYearResource;
use App\Models\SchoolYear;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchoolYear extends EditRecord
{
    protected static string $resource = SchoolYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si se está activando este año, desactivar los demás
        if ($data['is_active'] ?? false) {
            SchoolYear::where('id', '!=', $this->record->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
