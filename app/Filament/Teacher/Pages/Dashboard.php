<?php

namespace App\Filament\Teacher\Pages;

use App\Filament\Teacher\Widgets\MyAssignmentsWidget;
use App\Filament\Teacher\Widgets\TeacherStatsWidget;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Inicio';

    protected static ?string $navigationLabel = 'Inicio';

    protected static string $view = 'filament.teacher.pages.dashboard';

    public function getHeading(): string
    {
        return '';
    }
}
