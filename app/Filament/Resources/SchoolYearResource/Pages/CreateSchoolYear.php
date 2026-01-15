<?php

namespace App\Filament\Resources\SchoolYearResource\Pages;

use App\Filament\Resources\SchoolYearResource;
use App\Models\SchoolYear;
use Filament\Resources\Pages\CreateRecord;

class CreateSchoolYear extends CreateRecord
{
    protected static string $resource = SchoolYearResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si se está activando este año, desactivar los demás
        if ($data['is_active'] ?? false) {
            SchoolYear::where('is_active', true)->update(['is_active' => false]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
