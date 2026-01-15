<?php

namespace App\Filament\Pages;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Period;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Services\ReportCardServiceV2;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;

class GenerateReportCards extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationLabel = 'Generar Boletines';
    protected static ?string $title = 'Generación de Boletines';
    protected static ?string $navigationGroup = 'Académico';
    protected static ?int $navigationSort = 10;
    
    protected static string $view = 'filament.pages.generate-report-cards';

    public ?int $school_year_id = null;
    public ?int $period_id = null;
    public ?int $sede_id = null;
    public ?int $grade_id = null;
    
    public bool $isGenerating = false;
    public ?string $downloadUrl = null;
    public int $totalReports = 0;
    public array $previewData = [];
    public array $generationStats = [];
    public ?string $previewUrl = null;

    public function mount(): void
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        $this->school_year_id = $activeYear?->id;
        
        if ($activeYear) {
            $activePeriod = Period::where('school_year_id', $activeYear->id)
                ->where('is_active', true)
                ->first();
            $this->period_id = $activePeriod?->id;
        }
        
        $this->updatePreview();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filtros de Generación')
                    ->description('Selecciona los criterios para generar los boletines')
                    ->icon('heroicon-o-funnel')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('school_year_id')
                                    ->label('Año Escolar')
                                    ->options(SchoolYear::pluck('name', 'id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->period_id = null;
                                        $this->updatePreview();
                                    })
                                    ->native(false),
                                    
                                Forms\Components\Select::make('period_id')
                                    ->label('Período')
                                    ->options(function () {
                                        if (!$this->school_year_id) return [];
                                        return Period::where('school_year_id', $this->school_year_id)
                                            ->orderBy('number')
                                            ->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->updatePreview())
                                    ->native(false),
                                    
                                Forms\Components\Select::make('sede_id')
                                    ->label('Sede')
                                    ->options(Sede::where('is_active', true)->pluck('name', 'id'))
                                    ->placeholder('Todas las sedes')
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->updatePreview())
                                    ->native(false),
                                    
                                Forms\Components\Select::make('grade_id')
                                    ->label('Grado')
                                    ->options(Grade::where('is_active', true)->orderBy('level')->pluck('name', 'id'))
                                    ->placeholder('Todos los grados')
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->updatePreview())
                                    ->native(false),
                            ]),
                    ]),
            ]);
    }

    public function updatePreview(): void
    {
        if (!$this->school_year_id || !$this->period_id) {
            $this->previewData = [];
            $this->totalReports = 0;
            return;
        }

        $query = Enrollment::where('school_year_id', $this->school_year_id)
            ->where('status', 'active')
            ->with(['student', 'sede', 'grade']);
        
        if ($this->sede_id) {
            $query->where('sede_id', $this->sede_id);
        }
        
        if ($this->grade_id) {
            // Si es multigrado, incluir todos los grados del rango
            $grade = Grade::find($this->grade_id);
            if ($grade && $grade->type === 'multi' && $grade->min_grade && $grade->max_grade) {
                $gradesInRange = Grade::where('is_active', true)
                    ->whereBetween('level', [$grade->min_grade, $grade->max_grade])
                    ->pluck('id')
                    ->toArray();
                $query->whereIn('grade_id', $gradesInRange);
            } else {
                $query->where('grade_id', $this->grade_id);
            }
        }
        
        $enrollments = $query->get();
        
        // Agrupar por sede y grado
        $grouped = $enrollments->groupBy(['sede_id', 'grade_id']);
        
        $preview = [];
        foreach ($grouped as $sedeId => $gradeGroups) {
            $sede = Sede::find($sedeId);
            $sedeData = [
                'name' => $sede->name,
                'grades' => [],
                'total' => 0,
            ];
            
            foreach ($gradeGroups as $gradeId => $students) {
                $grade = Grade::find($gradeId);
                $sedeData['grades'][] = [
                    'name' => $grade->name,
                    'count' => $students->count(),
                ];
                $sedeData['total'] += $students->count();
            }
            
            $preview[] = $sedeData;
        }
        
        $this->previewData = $preview;
        $this->totalReports = $enrollments->count();
    }

    public function generateAll(): void
    {
        if (!$this->period_id) {
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
            $period = Period::find($this->period_id);
            $service = new ReportCardServiceV2();
            
            $result = $service->generateBulkZip(
                $period,
                $this->sede_id,
                $this->grade_id
            );
            
            if ($result && isset($result['path'])) {
                $this->downloadUrl = asset('storage/' . $result['path']);
                $this->generationStats = [
                    'total' => $result['total'] ?? $this->totalReports,
                    'success' => $result['success'] ?? $this->totalReports,
                    'errors' => $result['errors'] ?? 0,
                    'time' => $result['time'] ?? 0,
                ];
                
                Notification::make()
                    ->title('¡Boletines generados exitosamente!')
                    ->body("Se generaron {$this->generationStats['success']} boletines en {$this->generationStats['time']}s")
                    ->success()
                    ->duration(10000)
                    ->send();
            } else {
                Notification::make()
                    ->title('No se encontraron estudiantes')
                    ->body('No hay estudiantes matriculados con los filtros seleccionados')
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al generar boletines')
                ->body($e->getMessage())
                ->danger()
                ->send();
                
            \Log::error('Error generando boletines', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        $this->isGenerating = false;
    }

    public function generatePreview(): void
    {
        if (!$this->period_id) {
            Notification::make()
                ->title('Selecciona un período')
                ->danger()
                ->send();
            return;
        }

        // Obtener el primer estudiante para vista previa
        $query = Enrollment::where('school_year_id', $this->school_year_id)
            ->where('status', 'active')
            ->with(['student', 'sede', 'grade']);
        
        if ($this->sede_id) {
            $query->where('sede_id', $this->sede_id);
        }
        
        if ($this->grade_id) {
            // Si es multigrado, incluir todos los grados del rango
            $grade = Grade::find($this->grade_id);
            if ($grade && $grade->type === 'multi' && $grade->min_grade && $grade->max_grade) {
                $gradesInRange = Grade::where('is_active', true)
                    ->whereBetween('level', [$grade->min_grade, $grade->max_grade])
                    ->pluck('id')
                    ->toArray();
                $query->whereIn('grade_id', $gradesInRange);
            } else {
                $query->where('grade_id', $this->grade_id);
            }
        }
        
        $enrollment = $query->first();
        
        if (!$enrollment) {
            Notification::make()
                ->title('No hay estudiantes')
                ->danger()
                ->send();
            return;
        }

        $period = Period::find($this->period_id);
        $service = new ReportCardServiceV2();
        
        $data = $service->getStudentReportData($enrollment->student, $period, $enrollment);
        
        if (empty($data)) {
            Notification::make()
                ->title('No hay datos para mostrar')
                ->body('El estudiante no tiene calificaciones registradas en este período')
                ->warning()
                ->send();
            return;
        }

        $pdf = Pdf::loadView('pdf.report-card-v2', $data);
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isCssFloatEnabled', true);
        $pdf->setOption('dpi', 150);
        
        $fileName = 'preview_boletin_' . time() . '.pdf';
        Storage::disk('public')->put('reports/' . $fileName, $pdf->output());
        
        $this->previewUrl = asset('storage/reports/' . $fileName);
        $this->downloadUrl = $this->previewUrl;
        
        Notification::make()
            ->title('Vista previa generada')
            ->body('Boletín de: ' . $enrollment->student->name)
            ->success()
            ->send();
    }

    public function generateSingleStudent(int $studentId): void
    {
        if (!$this->period_id) {
            Notification::make()
                ->title('Selecciona un período')
                ->danger()
                ->send();
            return;
        }

        $enrollment = Enrollment::where('school_year_id', $this->school_year_id)
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->with(['student', 'sede', 'grade'])
            ->first();
        
        if (!$enrollment) {
            Notification::make()
                ->title('Estudiante no encontrado')
                ->danger()
                ->send();
            return;
        }

        $period = Period::find($this->period_id);
        $service = new ReportCardServiceV2();
        
        $data = $service->getStudentReportData($enrollment->student, $period, $enrollment);
        
        if (empty($data)) {
            Notification::make()
                ->title('No hay datos para mostrar')
                ->warning()
                ->send();
            return;
        }

        $pdf = Pdf::loadView('pdf.report-card-v2', $data);
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isCssFloatEnabled', true);
        $pdf->setOption('dpi', 150);
        
        $fileName = 'boletin_' . \Str::slug($enrollment->student->name) . '_' . time() . '.pdf';
        Storage::disk('public')->put('reports/' . $fileName, $pdf->output());
        
        $this->downloadUrl = asset('storage/reports/' . $fileName);
        
        Notification::make()
            ->title('Boletín generado')
            ->body('Boletín de: ' . $enrollment->student->name)
            ->success()
            ->send();
    }

    public function downloadReport()
    {
        if ($this->downloadUrl) {
            return redirect($this->downloadUrl);
        }
    }
    
    public function clearDownload(): void
    {
        $this->downloadUrl = null;
        $this->previewUrl = null;
        $this->generationStats = [];
    }
}
