<?php

namespace App\Filament\Teacher\Pages;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Period;
use App\Models\SchoolYear;
use App\Models\StudentGrade;
use App\Models\TeachingAssignment;
use App\Services\GradesExcelService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GradingBoardPage extends Page implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $title = 'Tablero de Calificaciones';

    protected static string $view = 'filament.teacher.pages.grading-board-page';

    protected static bool $shouldRegisterNavigation = false;

    #[Url]
    public ?int $assignment = null;

    public ?int $selectedPeriod = null;

    public ?TeachingAssignment $teachingAssignment = null;

    public ?Period $currentPeriod = null;

    public Collection $students;

    public array $grades = [];

    public bool $isReadOnly = false;

    public bool $isMultigrade = false;

    public array $multigradeInfo = [];

    public $excelFile = null;

    public function mount(): void
    {
        // Verificar que el assignment pertenece al profesor
        $this->teachingAssignment = TeachingAssignment::with(['sede', 'grade', 'subject', 'schoolYear'])
            ->where('id', $this->assignment)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        // Obtener el periodo activo
        $activePeriod = Period::where('school_year_id', $this->teachingAssignment->school_year_id)
            ->where('is_active', true)
            ->first();

        $this->selectedPeriod = $activePeriod?->id;

        $this->loadData();
    }

    public function form(Form $form): Form
    {
        $periods = Period::where('school_year_id', $this->teachingAssignment->school_year_id)
            ->orderBy('number')
            ->pluck('name', 'id')
            ->toArray();

        return $form
            ->schema([
                Select::make('selectedPeriod')
                    ->label('Periodo')
                    ->options($periods)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadData()),
            ]);
    }

    public function loadData(): void
    {
        if (!$this->selectedPeriod || !$this->teachingAssignment) {
            $this->students = collect();
            return;
        }

        // Cargar periodo
        $this->currentPeriod = Period::find($this->selectedPeriod);
        $this->isReadOnly = $this->currentPeriod?->is_finalized ?? true;

        // Verificar si es multigrado
        $assignedGrade = $this->teachingAssignment->grade;
        $this->isMultigrade = $assignedGrade->type === 'multi';
        
        // Cargar estudiantes matriculados
        if ($this->isMultigrade && $assignedGrade->min_grade && $assignedGrade->max_grade) {
            // MULTIGRADO: Buscar estudiantes de todos los grados en el rango de niveles
            // Buscar todos los grados cuyo level esté dentro del rango (sin importar si son single o multi)
            $gradesInRange = Grade::where('is_active', true)
                ->whereBetween('level', [$assignedGrade->min_grade, $assignedGrade->max_grade])
                ->pluck('id')
                ->toArray();
            
            // Guardar info de multigrado para la vista
            $this->multigradeInfo = [
                'min' => $assignedGrade->min_grade,
                'max' => $assignedGrade->max_grade,
                'grades_count' => count($gradesInRange),
            ];
            
            $this->students = Enrollment::with(['student', 'grade'])
                ->where('school_year_id', $this->teachingAssignment->school_year_id)
                ->where('sede_id', $this->teachingAssignment->sede_id)
                ->whereIn('grade_id', $gradesInRange)
                ->where('status', 'active')
                ->get()
                ->sortBy(['grade.level', 'student.name']);
        } else {
            // GRADO ÚNICO: Comportamiento normal
            $this->multigradeInfo = [];
            
            $this->students = Enrollment::with(['student', 'grade'])
                ->where('school_year_id', $this->teachingAssignment->school_year_id)
                ->where('sede_id', $this->teachingAssignment->sede_id)
                ->where('grade_id', $this->teachingAssignment->grade_id)
                ->where('status', 'active')
                ->get()
                ->sortBy('student.name');
        }

        // Cargar notas existentes
        $existingGrades = StudentGrade::where('teaching_assignment_id', $this->teachingAssignment->id)
            ->where('period_id', $this->selectedPeriod)
            ->get()
            ->keyBy('student_id');

        // Inicializar array de grades
        $this->grades = [];
        foreach ($this->students as $enrollment) {
            $studentId = $enrollment->student_id;
            $grade = $existingGrades->get($studentId);

            $this->grades[$studentId] = [
                'tasks_score' => $grade?->tasks_score,
                'evaluations_score' => $grade?->evaluations_score,
                'self_score' => $grade?->self_score,
                'final_score' => $grade?->final_score,
                'observations' => $grade?->observations,
                'behavior' => $grade?->behavior,
                'absences' => $grade?->absences ?? 0,
            ];
        }
    }

    public function updatedGrades($value, $key): void
    {
        if ($this->isReadOnly) {
            return;
        }

        // Extraer student_id y field del key (formato: studentId.field)
        [$studentId, $field] = explode('.', $key);

        // Validar que sea un score válido
        if (in_array($field, ['tasks_score', 'evaluations_score', 'self_score'])) {
            $numValue = floatval($value);
            if ($numValue < 1.0) $numValue = 1.0;
            if ($numValue > 5.0) $numValue = 5.0;
            $this->grades[$studentId][$field] = $numValue;

            // Recalcular final_score
            $this->calculateFinalScore($studentId);
        }

        // Validar absences (>=0)
        if ($field === 'absences') {
            $this->grades[$studentId][$field] = max(0, intval($value));
        }

        // Validar behavior
        if ($field === 'behavior') {
            $validBehaviors = array_keys(\App\Models\StudentGrade::BEHAVIOR_OPTIONS);
            $this->grades[$studentId][$field] = in_array($value, $validBehaviors) ? $value : null;
        }
    }

    protected function calculateFinalScore(int $studentId): void
    {
        $tasks = floatval($this->grades[$studentId]['tasks_score'] ?? 0);
        $evaluations = floatval($this->grades[$studentId]['evaluations_score'] ?? 0);
        $self = floatval($this->grades[$studentId]['self_score'] ?? 0);

        if ($tasks == 0 && $evaluations == 0 && $self == 0) {
            $this->grades[$studentId]['final_score'] = null;
        } else {
            $this->grades[$studentId]['final_score'] = round(
                ($tasks * 0.40) + ($evaluations * 0.50) + ($self * 0.10),
                1
            );
        }
    }

    public function saveGrades(): void
    {
        if ($this->isReadOnly) {
            Notification::make()
                ->title('Periodo finalizado')
                ->body('No se pueden modificar las notas de un periodo finalizado.')
                ->danger()
                ->send();
            return;
        }

        foreach ($this->grades as $studentId => $gradeData) {
            // Solo guardar si hay al menos una nota
            if ($gradeData['tasks_score'] || $gradeData['evaluations_score'] || $gradeData['self_score'] || $gradeData['behavior'] || $gradeData['absences'] > 0) {
                StudentGrade::updateOrCreate(
                    [
                        'teaching_assignment_id' => $this->teachingAssignment->id,
                        'student_id' => $studentId,
                        'period_id' => $this->selectedPeriod,
                    ],
                    [
                        'tasks_score' => $gradeData['tasks_score'],
                        'evaluations_score' => $gradeData['evaluations_score'],
                        'self_score' => $gradeData['self_score'],
                        'observations' => $gradeData['observations'] ?? null,
                        'behavior' => $gradeData['behavior'] ?? null,
                        'absences' => $gradeData['absences'] ?? 0,
                    ]
                );
            }
        }

        Notification::make()
            ->title('Notas guardadas')
            ->body('Las calificaciones se han guardado correctamente.')
            ->success()
            ->send();
    }

    public function goToActivePeriod(): void
    {
        $activePeriod = Period::where('school_year_id', $this->teachingAssignment->school_year_id)
            ->where('is_active', true)
            ->first();

        if ($activePeriod) {
            $this->selectedPeriod = $activePeriod->id;
            $this->loadData();
        }
    }

    /**
     * Descargar plantilla Excel
     */
    public function downloadExcel(): StreamedResponse
    {
        if ($this->isReadOnly) {
            Notification::make()
                ->title('Periodo finalizado')
                ->body('No se puede descargar la plantilla de un periodo finalizado.')
                ->danger()
                ->send();
            return response()->streamDownload(fn () => null, 'error.txt');
        }

        if (!$this->selectedPeriod || !$this->teachingAssignment) {
            Notification::make()
                ->title('Error')
                ->body('Debe seleccionar un periodo.')
                ->danger()
                ->send();
            return response()->streamDownload(fn () => null, 'error.txt');
        }

        $service = new GradesExcelService();
        $filePath = $service->export($this->teachingAssignment, $this->selectedPeriod);
        
        $fileName = basename($filePath);

        return response()->streamDownload(function () use ($filePath) {
            readfile($filePath);
            // Eliminar archivo temporal
            @unlink($filePath);
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Subir y procesar archivo Excel
     */
    public function uploadExcel(): void
    {
        if ($this->isReadOnly) {
            Notification::make()
                ->title('Periodo finalizado')
                ->body('No se pueden importar notas en un periodo finalizado.')
                ->danger()
                ->send();
            return;
        }

        if (!$this->excelFile) {
            Notification::make()
                ->title('Error')
                ->body('Debe seleccionar un archivo Excel.')
                ->danger()
                ->send();
            return;
        }

        // Obtener la ruta real del archivo
        $filePath = $this->excelFile->getRealPath();
        
        $service = new GradesExcelService();
        $results = $service->import($filePath, $this->teachingAssignment, $this->selectedPeriod);

        // Limpiar archivo
        $this->excelFile = null;

        // Mostrar resultados
        $message = "✅ {$results['success']} registros actualizados correctamente.";
        
        if ($results['skipped'] > 0) {
            $message .= "\n⏭️ {$results['skipped']} filas omitidas (sin datos/cambios).";
        }

        if (count($results['errors']) > 0) {
            $errorCount = count($results['errors']);
            $message .= "\n⚠️ {$errorCount} errores:";
            
            // Mostrar solo los primeros 5 errores
            $errorsToShow = array_slice($results['errors'], 0, 5);
            foreach ($errorsToShow as $error) {
                $message .= "\n• {$error}";
            }
            
            if ($errorCount > 5) {
                $message .= "\n... y " . ($errorCount - 5) . " más.";
            }
        }

        // Determinar tipo de notificación
        if ($results['success'] > 0 && count($results['errors']) == 0) {
            // Todo bien
            Notification::make()
                ->title('Importación exitosa')
                ->body($message)
                ->success()
                ->persistent()
                ->send();
            
            $this->loadData();
        } elseif ($results['success'] > 0 && count($results['errors']) > 0) {
            // Algunos éxitos, algunos errores
            Notification::make()
                ->title('Importación completada con advertencias')
                ->body($message)
                ->warning()
                ->persistent()
                ->send();
            
            $this->loadData();
        } elseif ($results['success'] == 0 && $results['skipped'] > 0 && count($results['errors']) == 0) {
            // Solo filas omitidas (sin datos nuevos)
            Notification::make()
                ->title('Sin cambios')
                ->body("No se encontraron datos para importar. Asegúrese de que el archivo tenga notas o valores ingresados.")
                ->info()
                ->persistent()
                ->send();
        } else {
            // Error total
            Notification::make()
                ->title('Importación fallida')
                ->body($message)
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        return route('filament.teacher.pages.grading-board-page', $parameters);
    }

    public function getTitle(): string
    {
        if ($this->teachingAssignment) {
            return "{$this->teachingAssignment->subject->name} - {$this->teachingAssignment->grade->name}";
        }
        return 'Tablero de Calificaciones';
    }

    public function getSubheading(): ?string
    {
        if ($this->teachingAssignment) {
            return "Sede: {$this->teachingAssignment->sede->name} | Año: {$this->teachingAssignment->schoolYear->name}";
        }
        return null;
    }
}
