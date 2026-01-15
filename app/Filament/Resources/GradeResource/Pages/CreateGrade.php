<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGrade extends CreateRecord
{
    protected static string $resource = GradeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si el tipo es single, asegurarse de que min_grade y max_grade sean null
        if ($data['type'] === 'single') {
            $data['min_grade'] = null;
            $data['max_grade'] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
