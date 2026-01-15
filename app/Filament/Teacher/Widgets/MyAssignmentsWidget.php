<?php

namespace App\Filament\Teacher\Widgets;

use App\Filament\Teacher\Pages\GradingBoardPage;
use App\Models\SchoolYear;
use App\Models\TeachingAssignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyAssignmentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Acceso Rápido - Mis Asignaciones';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $activeYear = SchoolYear::where('is_active', true)->first();

        return $table
            ->query(
                TeachingAssignment::query()
                    ->where('teacher_id', auth()->id())
                    ->where('school_year_id', $activeYear?->id)
                    ->where('is_active', true)
            )
            ->columns([
                Tables\Columns\TextColumn::make('sede.name')
                    ->label('Sede')
                    ->sortable(),

                Tables\Columns\TextColumn::make('grade.name')
                    ->label('Grado')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Asignatura')
                    ->sortable()
                    ->weight('bold'),
            ])
            ->actions([
                Tables\Actions\Action::make('calificar')
                    ->label('Calificar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->url(fn (TeachingAssignment $record): string => 
                        GradingBoardPage::getUrl(['assignment' => $record->id])
                    ),
            ])
            ->defaultSort('subject.name')
            ->paginated([5]);
    }
}
