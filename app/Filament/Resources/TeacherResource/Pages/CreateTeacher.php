<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generar password automáticamente basado en la identificación
        $data['password'] = Hash::make($data['identification']);
        $data['role'] = 'teacher';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
