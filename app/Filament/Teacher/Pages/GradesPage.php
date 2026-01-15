<?php

namespace App\Filament\Teacher\Pages;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Period;
use App\Models\SchoolYear;
use App\Models\TeachingAssignment;
use App\Services\BulkGradesExcelService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GradesPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Calificaciones';

    protected static ?string $title = 'Mis Calificaciones';

    protected static string $view = 'filament.teacher.pages.grades-page';

    protected static ?int $navigationSort = 1;

    public ?int $selectedPeriod = null;
    public $bulkExcelFile = null;
    public bool $showImportModal = false;

    public function mount(): void
    {
        // Obtener periodo activo por defecto
        $activeYear = SchoolYear::where('is_active', true)->first();
        if ($activeYear) {
            $activePeriod = Period::where('school_year_id', $activeYear->id)
                ->where('is_active', true)
                ->first();
            $this->selectedPeriod = $activePeriod?->id;
        }
    }

    /**
     * Obtener periodos disponibles
     */
    public function getPeriods(): array
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        if (!$activeYear) {
            return [];
        }

        return Period::where('school_year_id', $activeYear->id)
            ->orderBy('number')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Verificar si el periodo seleccionado permite edición
     */
    public function isPeriodEditable(): bool
    {
        if (!$this->selectedPeriod) {
            return false;
        }

        $period = Period::find($this->selectedPeriod);
        return $period && !$period->is_finalized;
    }

    public function table(Table $table): Table
    {
        $activeYear = SchoolYear::where('is_active', true)->first();

        return $table
            ->query(
                TeachingAssignment::query()
                    ->where('teacher_id', auth()->id())
                    ->where('school_year_id', $activeYear?->id)
                    ->where('is_active', true)
                    ->with(['sede', 'grade', 'subject', 'schoolYear'])
            )
            ->columns([
                TextColumn::make('sede.name')
                    ->label('Sede')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('grade.name')
                    ->label('Grado')
                    ->sortable()
                    ->searchable()
                    ->description(fn (TeachingAssignment $record): ?string => 
                        $record->grade->type === 'multi' ? "Grados {$record->grade->min_grade}° - {$record->grade->max_grade}°" : null
                    )
                    ->badge()
                    ->color(fn (TeachingAssignment $record): string => 
                        $record->grade->type === 'multi' ? 'warning' : 'gray'
                    ),

                TextColumn::make('subject.name')
                    ->label('Asignatura')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('schoolYear.name')
                    ->label('Año Escolar')
                    ->badge()
                    ->color('success'),

                TextColumn::make('students_count')
                    ->label('Estudiantes')
                    ->getStateUsing(function (TeachingAssignment $record) {
                        $grade = $record->grade;
                        
                        // Si es multigrado, contar estudiantes de todos los grados en el rango
                        if ($grade->type === 'multi' && $grade->min_grade && $grade->max_grade) {
                            $gradesInRange = Grade::where('is_active', true)
                                ->whereBetween('level', [$grade->min_grade, $grade->max_grade])
                                ->pluck('id')
                                ->toArray();
                            
                            return Enrollment::where('school_year_id', $record->school_year_id)
                                ->where('sede_id', $record->sede_id)
                                ->whereIn('grade_id', $gradesInRange)
                                ->where('status', 'active')
                                ->count();
                        }
                        
                        // Grado único: comportamiento normal
                        return Enrollment::where('school_year_id', $record->school_year_id)
                            ->where('sede_id', $record->sede_id)
                            ->where('grade_id', $record->grade_id)
                            ->where('status', 'active')
                            ->count();
                    })
                    ->badge()
                    ->color('info'),
            ])
            ->actions([
                Action::make('calificar')
                    ->label('Calificar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->url(fn (TeachingAssignment $record): string => 
                        GradingBoardPage::getUrl(['assignment' => $record->id])
                    ),
            ])
            ->emptyStateHeading('Sin asignaciones')
            ->emptyStateDescription('No tienes asignaciones para el año escolar actual.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->defaultSort('grade.name');
    }

    /**
     * Descargar Excel masivo con todas las asignaturas
     */
    public function downloadBulkExcel(): StreamedResponse
    {
        if (!$this->selectedPeriod) {
            Notification::make()
                ->title('Error')
                ->body('Debe seleccionar un periodo.')
                ->danger()
                ->send();
            return response()->streamDownload(fn () => null, 'error.txt');
        }

        $period = Period::find($this->selectedPeriod);
        if (!$period) {
            Notification::make()
                ->title('Error')
                ->body('Periodo no encontrado.')
                ->danger()
                ->send();
            return response()->streamDownload(fn () => null, 'error.txt');
        }

        if ($period->is_finalized) {
            Notification::make()
                ->title('Periodo finalizado')
                ->body('No se puede descargar plantilla de un periodo finalizado.')
                ->warning()
                ->send();
            return response()->streamDownload(fn () => null, 'error.txt');
        }

        try {
            $service = new BulkGradesExcelService();
            $filePath = $service->exportAllSubjects(auth()->id(), $this->selectedPeriod);
            
            $fileName = basename($filePath);

            return response()->streamDownload(function () use ($filePath) {
                readfile($filePath);
                @unlink($filePath);
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return response()->streamDownload(fn () => null, 'error.txt');
        }
    }

    /**
     * Abrir modal de importación
     */
    public function openImportModal(): void
    {
        $this->showImportModal = true;
    }

    /**
     * Cerrar modal de importación
     */
    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->bulkExcelFile = null;
    }

    /**
     * Subir y procesar Excel masivo
     */
    public function uploadBulkExcel(): void
    {
        if (!$this->bulkExcelFile) {
            Notification::make()
                ->title('Error')
                ->body('Debe seleccionar un archivo Excel.')
                ->danger()
                ->send();
            return;
        }

        $filePath = $this->bulkExcelFile->getRealPath();
        
        try {
            $service = new BulkGradesExcelService();
            $results = $service->importAllSubjects($filePath, auth()->id());

            $this->bulkExcelFile = null;
            $this->showImportModal = false;

            // Construir mensaje de resultado
            $message = "✅ {$results['total_success']} registros actualizados.";
            
            if ($results['total_skipped'] > 0) {
                $message .= "\n⏭️ {$results['total_skipped']} filas omitidas.";
            }

            // Detalles por hoja
            $sheetsWithErrors = array_filter($results['sheets'], fn($s) => count($s['errors']) > 0);
            
            if (!empty($sheetsWithErrors)) {
                $message .= "\n\n📋 Detalles por asignatura:";
                foreach (array_slice($sheetsWithErrors, 0, 3) as $sheet) {
                    $errorCount = count($sheet['errors']);
                    $message .= "\n• {$sheet['name']}: {$sheet['success']} OK, {$errorCount} errores";
                }
                
                if (count($sheetsWithErrors) > 3) {
                    $message .= "\n... y " . (count($sheetsWithErrors) - 3) . " más con errores.";
                }
            }

            // Determinar tipo de notificación
            if ($results['total_success'] > 0 && $results['total_errors'] == 0) {
                Notification::make()
                    ->title('Importación exitosa')
                    ->body($message)
                    ->success()
                    ->persistent()
                    ->send();
            } elseif ($results['total_success'] > 0 && $results['total_errors'] > 0) {
                Notification::make()
                    ->title('Importación con advertencias')
                    ->body($message)
                    ->warning()
                    ->persistent()
                    ->send();
            } elseif ($results['total_success'] == 0 && $results['total_skipped'] > 0) {
                Notification::make()
                    ->title('Sin cambios')
                    ->body('No se encontraron datos para importar.')
                    ->info()
                    ->persistent()
                    ->send();
            } else {
                Notification::make()
                    ->title('Importación fallida')
                    ->body($message)
                    ->danger()
                    ->persistent()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Error al procesar archivo: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Obtener conteo de asignaturas
     */
    public function getAssignmentsCount(): int
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        if (!$activeYear) {
            return 0;
        }

        return TeachingAssignment::where('teacher_id', auth()->id())
            ->where('school_year_id', $activeYear->id)
            ->where('is_active', true)
            ->count();
    }
}
