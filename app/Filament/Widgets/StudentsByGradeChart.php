<?php

namespace App\Filament\Widgets;

use App\Models\Enrollment;
use App\Models\Grade;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StudentsByGradeChart extends ChartWidget
{
    protected static ?string $heading = 'Estudiantes por Grado';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'half';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $grades = Grade::where('is_active', true)
            ->orderBy('level')
            ->get();
        
        $labels = [];
        $data = [];
        $backgroundColors = [];
        
        $colors = [
            'rgba(239, 68, 68, 0.7)',   // Preescolar - rojo
            'rgba(249, 115, 22, 0.7)',  // Primero - naranja
            'rgba(234, 179, 8, 0.7)',   // Segundo - amarillo
            'rgba(132, 204, 22, 0.7)',  // Tercero - lima
            'rgba(34, 197, 94, 0.7)',   // Cuarto - verde
            'rgba(20, 184, 166, 0.7)',  // Quinto - teal
            'rgba(6, 182, 212, 0.7)',   // Sexto - cyan
            'rgba(59, 130, 246, 0.7)',  // Séptimo - azul
            'rgba(99, 102, 241, 0.7)',  // Octavo - indigo
            'rgba(139, 92, 246, 0.7)',  // Noveno - violeta
            'rgba(168, 85, 247, 0.7)',  // Décimo - púrpura
            'rgba(236, 72, 153, 0.7)',  // Undécimo - rosa
        ];
        
        foreach ($grades as $index => $grade) {
            $labels[] = $grade->code;
            $count = Enrollment::where('grade_id', $grade->id)
                ->where('status', 'active')
                ->count();
            $data[] = $count;
            $backgroundColors[] = $colors[$index % count($colors)];
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Estudiantes matriculados',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderRadius' => 5,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
