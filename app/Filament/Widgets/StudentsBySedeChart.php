<?php

namespace App\Filament\Widgets;

use App\Models\Enrollment;
use App\Models\Sede;
use Filament\Widgets\ChartWidget;

class StudentsBySedeChart extends ChartWidget
{
    protected static ?string $heading = 'Estudiantes por Sede';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $sedes = Sede::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $labels = [];
        $data = [];
        
        foreach ($sedes as $sede) {
            // Acortar nombres largos
            $shortName = strlen($sede->name) > 15 
                ? substr($sede->name, 0, 12) . '...' 
                : $sede->name;
            $labels[] = $shortName;
            
            $count = Enrollment::where('sede_id', $sede->id)
                ->where('status', 'active')
                ->count();
            $data[] = $count;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Estudiantes',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
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
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
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
