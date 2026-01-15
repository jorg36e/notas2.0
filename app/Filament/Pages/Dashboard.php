<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UsersDistributionChart;
use App\Filament\Widgets\StudentsByGradeChart;
use App\Filament\Widgets\StudentsBySedeChart;
use App\Filament\Widgets\LatestStudentsTable;
use App\Filament\Widgets\LatestTeachersTable;
use App\Filament\Widgets\SystemInfoWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;
    
    public function getTitle(): string
    {
        return 'Panel de Control';
    }

    public function getHeading(): string
    {
        return '👋 Bienvenido a NOTAS 2.0';
    }

    public function getSubheading(): string|null
    {
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = 'Buenos días';
        } elseif ($hour < 18) {
            $greeting = 'Buenas tardes';
        } else {
            $greeting = 'Buenas noches';
        }
        
        return "{$greeting}, " . auth()->user()?->name . '. Aquí está tu resumen del sistema.';
    }

    public function getWidgets(): array
    {
        return [
            QuickActionsWidget::class,
            StatsOverview::class,
            UsersDistributionChart::class,
            StudentsByGradeChart::class,
            StudentsBySedeChart::class,
            LatestStudentsTable::class,
            LatestTeachersTable::class,
            SystemInfoWidget::class,
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return 2;
    }
}
