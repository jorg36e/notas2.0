<?php

namespace App\Filament\Student\Pages;

use App\Models\Enrollment;
use App\Models\Period;
use App\Models\SchoolSetting;
use App\Models\SchoolYear;
use App\Models\StudentGrade;
use App\Models\TeachingAssignment;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class MyGrades extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $title = 'Mis Calificaciones';

    protected static ?string $navigationLabel = 'Mis Calificaciones';

    protected static string $view = 'filament.student.pages.my-grades';

    protected static ?int $navigationSort = 1;

    public ?int $selectedSchoolYear = null;
    public ?int $selectedPeriod = null;
    
    public Collection $subjects;
    public ?Enrollment $enrollment = null;
    public array $schoolYears = [];
    public array $periods = [];
    
    // Colores de configuración
    public string $primaryColor = '#3b82f6';
    public string $secondaryColor = '#10b981';
    public string $accentColor = '#f59e0b';

    public function mount(): void
    {
        $student = auth()->user();
        
        // Cargar colores de configuración
        $this->primaryColor = SchoolSetting::get('primary_color', '#3b82f6');
        $this->secondaryColor = SchoolSetting::get('secondary_color', '#10b981');
        $this->accentColor = SchoolSetting::get('accent_color', '#f59e0b');
        
        // Obtener años escolares donde el estudiante tiene matrícula
        $this->schoolYears = SchoolYear::whereHas('enrollments', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->orderBy('name', 'desc')
            ->pluck('name', 'id')
            ->toArray();

        // Seleccionar año activo por defecto
        $activeYear = SchoolYear::where('is_active', true)->first();
        if ($activeYear && isset($this->schoolYears[$activeYear->id])) {
            $this->selectedSchoolYear = $activeYear->id;
        } else {
            $this->selectedSchoolYear = array_key_first($this->schoolYears);
        }

        $this->loadPeriods();
        $this->loadSubjects();
    }

    public function updatedSelectedSchoolYear(): void
    {
        $this->loadPeriods();
        $this->loadSubjects();
    }

    public function updatedSelectedPeriod(): void
    {
        $this->loadSubjects();
    }

    protected function loadPeriods(): void
    {
        if (!$this->selectedSchoolYear) {
            $this->periods = [];
            return;
        }

        $this->periods = Period::where('school_year_id', $this->selectedSchoolYear)
            ->orderBy('number')
            ->pluck('name', 'id')
            ->toArray();

        // Seleccionar periodo activo por defecto
        $activePeriod = Period::where('school_year_id', $this->selectedSchoolYear)
            ->where('is_active', true)
            ->first();
        
        if ($activePeriod) {
            $this->selectedPeriod = $activePeriod->id;
        } else {
            $this->selectedPeriod = array_key_first($this->periods);
        }
    }

    protected function loadSubjects(): void
    {
        $this->subjects = collect();
        
        if (!$this->selectedSchoolYear) {
            return;
        }

        $student = auth()->user();

        // Obtener la matrícula del estudiante para este año
        $this->enrollment = Enrollment::with(['grade', 'sede'])
            ->where('student_id', $student->id)
            ->where('school_year_id', $this->selectedSchoolYear)
            ->where('status', 'active')
            ->first();

        if (!$this->enrollment) {
            return;
        }

        // Obtener TODAS las asignaturas asignadas para el grado y sede del estudiante
        $teachingAssignments = TeachingAssignment::with(['subject', 'teacher'])
            ->where('school_year_id', $this->selectedSchoolYear)
            ->where('grade_id', $this->enrollment->grade_id)
            ->where('sede_id', $this->enrollment->sede_id)
            ->where('is_active', true)
            ->get();

        // Si hay periodo seleccionado, buscar las calificaciones
        $grades = collect();
        if ($this->selectedPeriod) {
            $grades = StudentGrade::where('student_id', $student->id)
                ->where('period_id', $this->selectedPeriod)
                ->get()
                ->keyBy('teaching_assignment_id');
        }

        // Combinar asignaciones con calificaciones
        $this->subjects = $teachingAssignments->map(function ($assignment) use ($grades) {
            $grade = $grades->get($assignment->id);
            
            return (object) [
                'teaching_assignment' => $assignment,
                'subject' => $assignment->subject,
                'teacher' => $assignment->teacher,
                'grade' => $grade,
                'tasks_score' => $grade?->tasks_score,
                'evaluations_score' => $grade?->evaluations_score,
                'self_score' => $grade?->self_score,
                'final_score' => $grade?->final_score,
                'behavior' => $grade?->behavior,
                'absences' => $grade?->absences ?? 0,
                'has_grades' => $grade !== null && $grade->final_score !== null,
            ];
        })->sortBy('subject.name');
    }

    /**
     * Obtener el color del badge según el comportamiento
     */
    public function getBehaviorColor(?string $behavior): string
    {
        return match($behavior) {
            'SUPERIOR' => 'success',
            'ALTO' => 'info',
            'BASICO' => 'warning',
            'BAJO' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Obtener el ícono según el comportamiento
     */
    public function getBehaviorIcon(?string $behavior): string
    {
        return match($behavior) {
            'SUPERIOR' => 'heroicon-o-star',
            'ALTO' => 'heroicon-o-hand-thumb-up',
            'BASICO' => 'heroicon-o-minus-circle',
            'BAJO' => 'heroicon-o-exclamation-triangle',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    /**
     * Calcular estadísticas generales
     */
    public function getStats(): array
    {
        if ($this->subjects->isEmpty()) {
            return [
                'total' => 0,
                'aprobadas' => 0,
                'perdidas' => 0,
                'promedio' => null,
                'porcentajeAprobacion' => 0,
                'sinCalificar' => 0,
            ];
        }

        $conNota = $this->subjects->filter(fn($s) => $s->has_grades);
        $aprobadas = $conNota->filter(fn($s) => $s->final_score >= 3.0)->count();
        $perdidas = $conNota->filter(fn($s) => $s->final_score < 3.0)->count();
        $promedio = $conNota->avg('final_score');
        $sinCalificar = $this->subjects->filter(fn($s) => !$s->has_grades)->count();

        return [
            'total' => $this->subjects->count(),
            'aprobadas' => $aprobadas,
            'perdidas' => $perdidas,
            'promedio' => $promedio ? round($promedio, 1) : null,
            'porcentajeAprobacion' => $conNota->count() > 0 
                ? round(($aprobadas / $conNota->count()) * 100) 
                : 0,
            'sinCalificar' => $sinCalificar,
        ];
    }
}
