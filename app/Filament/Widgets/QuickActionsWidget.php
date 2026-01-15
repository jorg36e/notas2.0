<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions-widget';
    
    protected static ?int $sort = 0;
    
    protected int | string | array $columnSpan = 'full';
    
    public function getActions(): array
    {
        return [
            [
                'label' => 'Nuevo Estudiante',
                'description' => 'Registrar un estudiante',
                'url' => route('filament.admin.resources.students.create'),
                'icon' => 'heroicon-o-user-plus',
                'color' => 'success',
            ],
            [
                'label' => 'Nuevo Docente',
                'description' => 'Agregar un docente',
                'url' => route('filament.admin.resources.teachers.create'),
                'icon' => 'heroicon-o-academic-cap',
                'color' => 'info',
            ],
            [
                'label' => 'Nueva Matrícula',
                'description' => 'Crear matrícula',
                'url' => route('filament.admin.resources.enrollments.create'),
                'icon' => 'heroicon-o-clipboard-document-list',
                'color' => 'warning',
            ],
            [
                'label' => 'Asignaciones',
                'description' => 'Gestionar asignaciones',
                'url' => route('filament.admin.resources.teaching-assignments.index'),
                'icon' => 'heroicon-o-briefcase',
                'color' => 'danger',
            ],
            [
                'label' => 'Ver Estudiantes',
                'description' => 'Lista de estudiantes',
                'url' => route('filament.admin.resources.students.index'),
                'icon' => 'heroicon-o-user-group',
                'color' => 'primary',
            ],
            [
                'label' => 'Ver Sedes',
                'description' => 'Gestionar sedes',
                'url' => route('filament.admin.resources.sedes.index'),
                'icon' => 'heroicon-o-building-office-2',
                'color' => 'gray',
            ],
        ];
    }
}
