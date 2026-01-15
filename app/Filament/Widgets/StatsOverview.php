<?php

namespace App\Filament\Widgets;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $totalStudents = User::where('role', 'student')->count();
        $activeStudents = User::where('role', 'student')->where('is_active', true)->count();
        
        $totalTeachers = User::where('role', 'teacher')->count();
        $activeTeachers = User::where('role', 'teacher')->where('is_active', true)->count();
        
        $totalEnrollments = Enrollment::count();
        $activeEnrollments = Enrollment::where('status', 'active')->count();
        
        $totalSedes = Sede::where('is_active', true)->count();
        $totalSubjects = Subject::where('is_active', true)->count();
        $totalGrades = Grade::where('is_active', true)->count();
        
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $schoolYearName = $activeSchoolYear ? $activeSchoolYear->name : 'Sin definir';
        
        return [
            Stat::make('👨‍🎓 Estudiantes', $totalStudents)
                ->description($activeStudents . ' activos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart($this->getStudentsTrend())
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition',
                ]),
                
            Stat::make('👨‍🏫 Docentes', $totalTeachers)
                ->description($activeTeachers . ' activos')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info')
                ->chart($this->getTeachersTrend()),
                
            Stat::make('📋 Matrículas', $totalEnrollments)
                ->description($activeEnrollments . ' activas')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning')
                ->chart($this->getEnrollmentsTrend()),
                
            Stat::make('🏫 Sedes', $totalSedes)
                ->description('Sedes activas')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
                
            Stat::make('📚 Asignaturas', $totalSubjects)
                ->description('Materias disponibles')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('danger'),
                
            Stat::make('📅 Año Escolar', $schoolYearName)
                ->description('Año lectivo actual')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('gray'),
        ];
    }
    
    private function getStudentsTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = User::where('role', 'student')
                ->whereDate('created_at', '<=', $date)
                ->count();
        }
        return $data;
    }
    
    private function getTeachersTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = User::where('role', 'teacher')
                ->whereDate('created_at', '<=', $date)
                ->count();
        }
        return $data;
    }
    
    private function getEnrollmentsTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = Enrollment::whereDate('created_at', '<=', $date)->count();
        }
        return $data;
    }
}
