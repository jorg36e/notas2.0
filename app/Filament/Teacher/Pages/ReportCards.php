<?php

namespace App\Filament\Teacher\Pages;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Period;
use App\Models\SchoolYear;
use App\Models\TeachingAssignment;
use App\Services\ReportCardServiceV2;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ReportCards extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationLabel = 'Boletines';
    protected static ?string $title = 'Boletines de Mi Sede';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.teacher.pages.report-cards';

    public ?array $formData = [];
    public ?int $selectedPeriodId = null;
    public bool $isGenerating = false;
    public ?string $downloadUrl = null;
    public array $generationStats = [];

    /**
     * Solo mostrar en navegación si el profesor tiene asignaciones multigrado
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $activeYear = SchoolYear::where('is_active', true)->first();
        if (!$activeYear) return false;

        return TeachingAssignment::where('teacher_id', $user->id)
            ->where('school_year_id', $activeYear->id)
            ->where('is_active', true)
            ->whereHas('grade', function($query) {
                $query->where('type', 'multi');
            })
            ->exists();
    }

    public function mount(): void
    {
        // Verificar acceso
        if (!$this->getMultigradeDirectorInfo()) {
            $this->redirect(route('filament.teacher.pages.dashboard'));
            return;
        }

        // Inicializar período activo
        $activeYear = SchoolYear::where('is_active', true)->first();
        if ($activeYear) {
            $activePeriod = Period::where('school_year_id', $activeYear->id)
                ->where('is_active', true)
                ->first();
            $this->selectedPeriodId = $activePeriod?->id;
        }

        $this->formData = [
            'period_id' => $this->selectedPeriodId,
        ];
    }

    public function form(Form $form): Form
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        
        return $form
            ->schema([
                Select::make('period_id')
                    ->label('Período')
                    ->options(function () use ($activeYear) {
                        if (!$activeYear) return [];
                        return Period::where('school_year_id', $activeYear->id)
                            ->orderBy('number')
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->selectedPeriodId = $state),
            ])
            ->statePath('formData');
    }

    /**
     * Obtener información del multigrado del profesor
     */
    public function getMultigradeDirectorInfo(): ?array
    {
        $user = auth()->user();
        $activeYear = SchoolYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            return null;
        }

        // Buscar si el profesor tiene asignaciones en grados multigrado
        $multigradeAssignment = TeachingAssignment::where('teacher_id', $user->id)
            ->where('school_year_id', $activeYear->id)
            ->where('is_active', true)
            ->whereHas('grade', function($query) {
                $query->where('type', 'multi');
            })
            ->with(['sede', 'grade'])
            ->first();

        if (!$multigradeAssignment) {
            return null;
        }

        $grade = $multigradeAssignment->grade;
        $sede = $multigradeAssignment->sede;

        // Obtener los grados dentro del rango del multigrado
        $gradesInRange = Grade::where('is_active', true)
            ->whereBetween('level', [$grade->min_grade, $grade->max_grade])
            ->pluck('id')
            ->toArray();

        // Contar estudiantes en esta sede y estos grados
        $studentsCount = Enrollment::where('school_year_id', $activeYear->id)
            ->where('sede_id', $sede->id)
            ->where('status', 'active')
            ->whereIn('grade_id', $gradesInRange)
            ->count();

        // Obtener lista de estudiantes agrupados por grado
        $studentsByGrade = Enrollment::where('school_year_id', $activeYear->id)
            ->where('sede_id', $sede->id)
            ->where('status', 'active')
            ->whereIn('grade_id', $gradesInRange)
            ->with(['student', 'grade'])
            ->get()
            ->groupBy('grade.name')
            ->map(fn($group) => $group->count());

        return [
            'grade' => $grade,
            'sede' => $sede,
            'gradesInRange' => $gradesInRange,
            'studentsCount' => $studentsCount,
            'studentsByGrade' => $studentsByGrade,
            'schoolYear' => $activeYear,
        ];
    }

    /**
     * Generar boletines para los estudiantes de la sede multigrado
     */
    public function generateReportCards(): void
    {
        $info = $this->getMultigradeDirectorInfo();
        
        if (!$info) {
            Notification::make()
                ->title('Sin acceso')
                ->body('No tienes asignaciones multigrado')
                ->danger()
                ->send();
            return;
        }

        if (!$this->selectedPeriodId) {
            Notification::make()
                ->title('Selecciona un período')
                ->danger()
                ->send();
            return;
        }

        $this->isGenerating = true;
        $this->downloadUrl = null;
        $this->generationStats = [];

        try {
            $period = Period::find($this->selectedPeriodId);
            $service = new ReportCardServiceV2();
            
            // Generar boletines solo para la sede del profesor
            $result = $service->generateBulkZip(
                $period,
                $info['sede']->id,
                $info['grade']->id  // El grado multigrado
            );
            
            if ($result && isset($result['path'])) {
                $this->downloadUrl = asset('storage/' . $result['path']);
                $this->generationStats = [
                    'total' => $result['total'] ?? 0,
                    'success' => $result['success'] ?? 0,
                    'errors' => $result['errors'] ?? 0,
                    'time' => $result['time'] ?? 0,
                ];
                
                Notification::make()
                    ->title('¡Boletines generados!')
                    ->body("Se generaron {$this->generationStats['success']} boletines en {$this->generationStats['time']}s")
                    ->success()
                    ->duration(10000)
                    ->send();
            } else {
                Notification::make()
                    ->title('No se encontraron estudiantes')
                    ->body('No hay estudiantes con calificaciones en este período')
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al generar boletines')
                ->body($e->getMessage())
                ->danger()
                ->send();
                
            \Log::error('Error generando boletines multigrado', [
                'teacher' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        $this->isGenerating = false;
    }
}
