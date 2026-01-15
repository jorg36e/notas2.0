<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Usuarios', User::count())
                ->description('Usuarios registrados en el sistema')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->icon('heroicon-o-users'),
            
            Stat::make('Nuevos Usuarios Este Mes', User::where('created_at', '>=', now()->startOfMonth())->count())
                ->description('Usuarios nuevos en los últimos 30 días')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-star'),
            
            Stat::make('Sesiones Activas', \DB::table('sessions')->count())
                ->description('Usuarios conectados ahora')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('warning')
                ->icon('heroicon-o-bolt'),
        ];
    }
}
