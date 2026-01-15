<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Enrollment;
use App\Models\Period;
use App\Models\SchoolYear;
use App\Models\TeachingAssignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeacherStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $activeYear = SchoolYear::where('is_active', true)->first();
        $activePeriod = $activeYear 
            ? Period::where('school_year_id', $activeYear->id)->where('is_active', true)->first()
            : null;

        $assignmentsCount = 0;
        $subjectsCount = 0;
        $studentsCount = 0;

        if ($activeYear) {
            $assignments = TeachingAssignment::where('teacher_id', $user->id)
                ->where('school_year_id', $activeYear->id)
                ->where('is_active', true)
                ->get();

            $assignmentsCount = $assignments->count();
            $subjectsCount = $assignments->pluck('subject_id')->unique()->count();

            // Contar estudiantes únicos en los grados/sedes asignados
            $gradeSedeCombo = $assignments->map(fn($a) => "{$a->grade_id}-{$a->sede_id}")->unique();
            
            $studentsCount = Enrollment::where('school_year_id', $activeYear->id)
                ->where('status', 'active')
                ->where(function ($query) use ($assignments) {
                    foreach ($assignments as $assignment) {
                        $query->orWhere(function ($q) use ($assignment) {
                            $q->where('grade_id', $assignment->grade_id)
                              ->where('sede_id', $assignment->sede_id);
                        });
                    }
                })
                ->distinct('student_id')
                ->count();
        }

        return [
            Stat::make('Periodo Actual', $activePeriod?->name ?? 'Sin periodo')
                ->description($activePeriod?->is_finalized ? 'Finalizado' : 'Activo')
                ->icon('heroicon-o-calendar')
                ->color($activePeriod?->is_finalized ? 'danger' : 'success'),

            Stat::make('Mis Asignaciones', $assignmentsCount)
                ->description('Grupos asignados')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Asignaturas', $subjectsCount)
                ->description('Materias que dicto')
                ->icon('heroicon-o-book-open')
                ->color('info'),

            Stat::make('Estudiantes', $studentsCount)
                ->description('Total a mi cargo')
                ->icon('heroicon-o-users')
                ->color('warning'),
        ];
    }
}
