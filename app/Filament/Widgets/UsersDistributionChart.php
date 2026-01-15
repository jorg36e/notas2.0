<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UsersDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Usuarios por Rol';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'half';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $students = User::where('role', 'student')->count();
        $teachers = User::where('role', 'teacher')->count();
        $admins = User::where('role', 'admin')->count();
        
        return [
            'datasets' => [
                [
                    'label' => 'Usuarios',
                    'data' => [$students, $teachers, $admins],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // verde - estudiantes
                        'rgba(59, 130, 246, 0.8)', // azul - docentes
                        'rgba(239, 68, 68, 0.8)',  // rojo - admins
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Estudiantes', 'Docentes', 'Administradores'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
