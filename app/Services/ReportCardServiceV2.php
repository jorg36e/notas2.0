<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Period;
use App\Models\SchoolSetting;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\StudentGrade;
use App\Models\TeachingAssignment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use ZipArchive;

/**
 * Servicio de Generación de Boletines V2
 * Optimizado con soporte para multigrado y mejor rendimiento
 */
class ReportCardServiceV2
{
    // Cache de configuraciones para evitar múltiples queries
    private ?array $settingsCache = null;
    private array $assignmentsCache = [];
    private array $gradesCache = [];

    /**
     * Escala de valoración institucional
     */
    public const PERFORMANCE_SCALE = [
        'SUPERIOR' => ['min' => 4.6, 'max' => 5.0, 'description' => 'Desempeño Superior', 'color' => '#7c3aed'],
        'ALTO' => ['min' => 4.0, 'max' => 4.5, 'description' => 'Desempeño Alto', 'color' => '#2563eb'],
        'BASICO' => ['min' => 3.0, 'max' => 3.9, 'description' => 'Desempeño Básico', 'color' => '#d97706'],
        'BAJO' => ['min' => 1.0, 'max' => 2.9, 'description' => 'Desempeño Bajo', 'color' => '#dc2626'],
    ];

    /**
     * Obtener datos completos de un estudiante para el boletín
     * Con soporte para multigrado
     */
    public function getStudentReportData(
        User $student,
        Period $period,
        ?Enrollment $enrollment = null
    ): array {
        $schoolYear = $period->schoolYear;

        // Obtener matrícula si no se proporciona
        if (!$enrollment) {
            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('school_year_id', $schoolYear->id)
                ->where('status', 'active')
                ->first();
        }

        if (!$enrollment) {
            return [];
        }

        $sede = $enrollment->sede;
        $grade = $enrollment->grade;

        // Obtener todos los períodos del año escolar
        $periods = Period::where('school_year_id', $schoolYear->id)
            ->orderBy('number')
            ->get();

        // Obtener asignaciones de docentes (con soporte multigrado)
        $assignments = $this->getAssignmentsForStudent($enrollment, $schoolYear);

        // Si no hay asignaciones, aún así generar boletín básico
        if ($assignments->isEmpty()) {
            \Log::info("Estudiante {$student->name} sin asignaciones en sede {$sede->name}, grado {$grade->name}");
        }

        // Construir datos de calificaciones por asignatura
        $subjectsData = [];
        $totalFinalScore = 0;
        $subjectsWithScore = 0;
        $totalAbsences = [];
        $behaviorByPeriod = [];
        $observationsBySubject = [];

        foreach ($assignments as $assignment) {
            $subjectData = [
                'id' => $assignment->id,
                'name' => $assignment->subject->name,
                'teacher' => $assignment->teacher->name,
                'periods' => [],
                'periodDetails' => [],
                'final' => null,
                'performance' => null,
                'passed' => null,
                'totalAbsences' => 0,
                'absences' => 0,           // Alias para compatibilidad con la vista
                'behavior' => null,        // Comportamiento general de la asignatura
                'observations' => [],
            ];

            $periodScores = [];
            $subjectTotalAbsences = 0;
            $lastBehavior = null;

            foreach ($periods as $p) {
                $gradeRecord = $this->getStudentGrade($assignment->id, $student->id, $p->id);

                $score = $gradeRecord?->final_score;
                $subjectData['periods'][$p->number] = $score;
                $subjectData['periodDetails'][$p->number] = [
                    'tasks' => $gradeRecord?->tasks_score,
                    'evaluations' => $gradeRecord?->evaluations_score,
                    'self' => $gradeRecord?->self_score,
                    'final' => $score,
                    'absences' => $gradeRecord?->absences ?? 0,
                    'behavior' => $gradeRecord?->behavior,
                    'observations' => $gradeRecord?->observations,
                ];

                if ($score !== null) {
                    $periodScores[] = $score;
                }

                // Recolectar inasistencias y comportamiento
                if ($gradeRecord) {
                    $absences = $gradeRecord->absences ?? 0;
                    $subjectTotalAbsences += $absences;
                    
                    // Guardar último comportamiento registrado
                    if ($gradeRecord->behavior) {
                        $lastBehavior = $gradeRecord->behavior;
                    }
                    
                    if (!isset($totalAbsences[$p->number])) {
                        $totalAbsences[$p->number] = 0;
                    }
                    $totalAbsences[$p->number] += $absences;

                    if ($gradeRecord->behavior && !isset($behaviorByPeriod[$p->number])) {
                        $behaviorByPeriod[$p->number] = $gradeRecord->behavior;
                    }

                    if ($gradeRecord->observations) {
                        $subjectData['observations'][$p->number] = $gradeRecord->observations;
                    }
                }
            }

            $subjectData['totalAbsences'] = $subjectTotalAbsences;
            $subjectData['absences'] = $subjectTotalAbsences;  // Alias
            $subjectData['behavior'] = $lastBehavior;

            // Calcular definitiva (promedio de todos los períodos con nota)
            if (count($periodScores) > 0) {
                $subjectData['final'] = round(array_sum($periodScores) / count($periodScores), 1);
                $subjectData['performance'] = $this->getPerformanceLevel($subjectData['final']);
                $subjectData['passed'] = $subjectData['final'] >= 3.0;
                $totalFinalScore += $subjectData['final'];
                $subjectsWithScore++;
            }

            // Marcar si tiene todas las notas de los 4 períodos
            $subjectData['hasAllGrades'] = count($periodScores) >= count($periods);
            $subjectData['completedPeriods'] = count($periodScores);
            $subjectData['totalPeriods'] = count($periods);

            $subjectsData[] = $subjectData;
        }

        // Ordenar asignaturas alfabéticamente
        usort($subjectsData, fn($a, $b) => strcmp($a['name'], $b['name']));

        // Calcular promedio general
        $generalAverage = $subjectsWithScore > 0
            ? round($totalFinalScore / $subjectsWithScore, 2)
            : null;

        // Verificar si todas las materias tienen las 4 notas completas
        $allSubjectsComplete = count($subjectsData) > 0 && collect($subjectsData)->every(fn($s) => $s['hasAllGrades']);

        // Contar materias perdidas (nota final < 3.0)
        $failedSubjects = collect($subjectsData)->filter(fn($s) => $s['final'] !== null && $s['final'] < 3.0);
        $failedCount = $failedSubjects->count();
        $failedNames = $failedSubjects->pluck('name')->toArray();

        // Determinar estado final (solo si todas las notas están completas)
        $finalStatus = null;
        $passed = null;
        $statusMessage = null;

        if ($allSubjectsComplete && count($subjectsData) > 0) {
            if ($failedCount >= 3) {
                $finalStatus = 'REPROBADO';
                $passed = false;
                $statusMessage = "Reprueba el año escolar con {$failedCount} áreas en desempeño bajo.";
            } elseif ($failedCount >= 1) {
                $finalStatus = 'PENDIENTE';
                $passed = null;
                $statusMessage = "Debe presentar actividades de recuperación en: " . implode(', ', $failedNames);
            } else {
                $finalStatus = 'APROBADO';
                $passed = true;
                $statusMessage = "Aprueba satisfactoriamente el año escolar.";
            }
        }

        // Obtener director de grupo desde el grado
        $director = $grade->director ?? $assignments->first()?->teacher;
        
        // Obtener firma del director de grupo y convertir a base64
        $directorSignatureBase64 = null;
        if ($director && $director->signature) {
            $fullPath = storage_path('app/public/' . $director->signature);
            if (file_exists($fullPath)) {
                $signatureData = file_get_contents($fullPath);
                $signatureMime = mime_content_type($fullPath);
                $directorSignatureBase64 = 'data:' . $signatureMime . ';base64,' . base64_encode($signatureData);
            }
        }

        // Calcular puesto en el grado
        $rankingData = $this->calculateRanking($student, $enrollment, $schoolYear, $periods);

        // Determinar comportamiento general (el más frecuente o el del último período)
        $generalBehavior = $this->determineGeneralBehavior($behaviorByPeriod);

        // Total de inasistencias
        $totalAbsencesSum = array_sum($totalAbsences);

        return [
            'student' => $student,
            'enrollment' => $enrollment,
            'sede' => $sede,
            'grade' => $grade,
            'schoolYear' => $schoolYear,
            'period' => $period,
            'periods' => $periods,
            'subjects' => $subjectsData,
            'subjectsCount' => count($subjectsData),
            'generalAverage' => $generalAverage,
            'performanceLevel' => $this->getPerformanceLevel($generalAverage),
            'performanceScale' => self::PERFORMANCE_SCALE,
            'passed' => $passed,
            'finalStatus' => $finalStatus,
            'statusMessage' => $statusMessage,
            'failedSubjects' => $failedCount,
            'failedSubjectNames' => $failedNames,
            'allSubjectsComplete' => $allSubjectsComplete,
            'position' => $rankingData['position'],
            'totalStudents' => $rankingData['total'],
            'absencesByPeriod' => $totalAbsences,
            'totalAbsences' => $totalAbsencesSum,
            'behaviorByPeriod' => $behaviorByPeriod,
            'generalBehavior' => $generalBehavior,
            'director' => $director,
            'directorSignature' => $directorSignatureBase64,
            'settings' => $this->getSchoolSettings(),
            'generatedAt' => now(),
            'isMultigrade' => $this->isMultigradeAssignment($assignments->first()),
        ];
    }

    /**
     * Obtener asignaciones para un estudiante (con soporte multigrado)
     */
    private function getAssignmentsForStudent(Enrollment $enrollment, SchoolYear $schoolYear): Collection
    {
        $cacheKey = "{$enrollment->sede_id}_{$enrollment->grade_id}_{$schoolYear->id}";

        if (isset($this->assignmentsCache[$cacheKey])) {
            return $this->assignmentsCache[$cacheKey];
        }

        $grade = $enrollment->grade;

        // Buscar asignaciones directas al grado del estudiante
        $directAssignments = TeachingAssignment::where('school_year_id', $schoolYear->id)
            ->where('sede_id', $enrollment->sede_id)
            ->where('grade_id', $enrollment->grade_id)
            ->where('is_active', true)
            ->with(['subject', 'teacher'])
            ->get();

        // Buscar asignaciones multigrado que incluyan este grado
        $multigradeAssignments = collect();
        
        $multiGrades = Grade::where('type', 'multi')
            ->where('is_active', true)
            ->where('min_grade', '<=', $grade->level)
            ->where('max_grade', '>=', $grade->level)
            ->get();

        foreach ($multiGrades as $multiGrade) {
            $multiAssignments = TeachingAssignment::where('school_year_id', $schoolYear->id)
                ->where('sede_id', $enrollment->sede_id)
                ->where('grade_id', $multiGrade->id)
                ->where('is_active', true)
                ->with(['subject', 'teacher'])
                ->get();
            
            $multigradeAssignments = $multigradeAssignments->merge($multiAssignments);
        }

        // Combinar ambas, priorizando multigrado (evitar duplicados por materia)
        $allAssignments = $multigradeAssignments->merge($directAssignments);
        
        // Eliminar duplicados por subject_id (priorizar multigrado)
        $uniqueAssignments = $allAssignments->unique('subject_id');

        $this->assignmentsCache[$cacheKey] = $uniqueAssignments;

        return $uniqueAssignments;
    }

    /**
     * Obtener nota de estudiante con cache
     */
    private function getStudentGrade(int $assignmentId, int $studentId, int $periodId): ?StudentGrade
    {
        $cacheKey = "{$assignmentId}_{$studentId}_{$periodId}";

        if (!isset($this->gradesCache[$cacheKey])) {
            $this->gradesCache[$cacheKey] = StudentGrade::where('teaching_assignment_id', $assignmentId)
                ->where('student_id', $studentId)
                ->where('period_id', $periodId)
                ->first();
        }

        return $this->gradesCache[$cacheKey];
    }

    /**
     * Verificar si es asignación multigrado
     */
    private function isMultigradeAssignment(?TeachingAssignment $assignment): bool
    {
        if (!$assignment) return false;
        return $assignment->grade->type === 'multi';
    }

    /**
     * Calcular ranking del estudiante en su grado/sede
     */
    private function calculateRanking(
        User $student,
        Enrollment $enrollment,
        SchoolYear $schoolYear,
        Collection $periods
    ): array {
        // Obtener todos los estudiantes del mismo grado y sede
        $enrollments = Enrollment::where('school_year_id', $schoolYear->id)
            ->where('sede_id', $enrollment->sede_id)
            ->where('grade_id', $enrollment->grade_id)
            ->where('status', 'active')
            ->get();

        $averages = [];

        foreach ($enrollments as $enr) {
            $assignments = $this->getAssignmentsForStudent($enr, $schoolYear);
            $totalScore = 0;
            $subjectsCount = 0;

            foreach ($assignments as $assignment) {
                $periodScores = [];

                foreach ($periods as $period) {
                    $gradeRecord = $this->getStudentGrade($assignment->id, $enr->student_id, $period->id);
                    if ($gradeRecord?->final_score !== null) {
                        $periodScores[] = $gradeRecord->final_score;
                    }
                }

                if (count($periodScores) > 0) {
                    $totalScore += array_sum($periodScores) / count($periodScores);
                    $subjectsCount++;
                }
            }

            $averages[$enr->student_id] = $subjectsCount > 0 ? $totalScore / $subjectsCount : 0;
        }

        // Ordenar de mayor a menor
        arsort($averages);

        // Encontrar posición del estudiante
        $position = 1;
        foreach ($averages as $studentId => $avg) {
            if ($studentId == $student->id) {
                break;
            }
            $position++;
        }

        return [
            'position' => $position,
            'total' => count($averages),
        ];
    }

    /**
     * Determinar comportamiento general
     */
    private function determineGeneralBehavior(array $behaviorByPeriod): string
    {
        if (empty($behaviorByPeriod)) {
            return 'BASICO';
        }

        // Tomar el último periodo con comportamiento registrado
        $lastBehavior = end($behaviorByPeriod);
        return $lastBehavior ?: 'BASICO';
    }

    /**
     * Obtener nivel de desempeño según la nota
     */
    public function getPerformanceLevel(?float $score): string
    {
        if ($score === null) return '-';

        if ($score >= 4.6) return 'SUPERIOR';
        if ($score >= 4.0) return 'ALTO';
        if ($score >= 3.0) return 'BASICO';
        return 'BAJO';
    }

    /**
     * Generar PDF de un boletín individual
     * Optimizado para impresión tamaño carta
     */
    public function generatePDF(User $student, Period $period, ?Enrollment $enrollment = null): ?string
    {
        $data = $this->getStudentReportData($student, $period, $enrollment);

        if (empty($data)) {
            return null;
        }

        $pdf = Pdf::loadView('pdf.report-card-v2', $data);
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('dpi', 150);
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->output();
    }

    /**
     * Generar boletines masivos por sede y grado
     * Optimizado con precarga de datos
     */
    public function generateBulkReports(
        Period $period,
        ?int $sedeId = null,
        ?int $gradeId = null
    ): array {
        // Limpiar cache para procesamiento masivo
        $this->assignmentsCache = [];
        $this->gradesCache = [];

        $schoolYear = $period->schoolYear;

        // Precarga de todas las calificaciones para evitar N+1 queries
        $this->preloadGradesForPeriod($period, $sedeId, $gradeId);

        $query = Enrollment::where('school_year_id', $schoolYear->id)
            ->where('status', 'active')
            ->with(['student', 'sede', 'grade']);

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        }

        if ($gradeId) {
            // Si es multigrado, incluir todos los grados del rango
            $grade = Grade::find($gradeId);
            if ($grade && $grade->type === 'multi' && $grade->min_grade && $grade->max_grade) {
                $gradesInRange = Grade::where('is_active', true)
                    ->whereBetween('level', [$grade->min_grade, $grade->max_grade])
                    ->pluck('id')
                    ->toArray();
                $query->whereIn('grade_id', $gradesInRange);
            } else {
                $query->where('grade_id', $gradeId);
            }
        }

        $enrollments = $query->get();

        $reports = [];
        $errors = [];

        foreach ($enrollments as $enrollment) {
            try {
                $data = $this->getStudentReportData(
                    $enrollment->student,
                    $period,
                    $enrollment
                );

                if (!empty($data)) {
                    $reports[] = [
                        'student' => $enrollment->student,
                        'sede' => $enrollment->sede,
                        'grade' => $enrollment->grade,
                        'data' => $data,
                    ];
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'student' => $enrollment->student->name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'reports' => $reports,
            'errors' => $errors,
            'total' => count($reports),
        ];
    }
    
    /**
     * Precargar todas las calificaciones para un período
     * Esto evita múltiples consultas N+1
     */
    private function preloadGradesForPeriod(Period $period, ?int $sedeId, ?int $gradeId): void
    {
        $schoolYear = $period->schoolYear;
        $periods = Period::where('school_year_id', $schoolYear->id)->pluck('id');
        
        $query = StudentGrade::whereIn('period_id', $periods)
            ->with(['teachingAssignment.subject', 'teachingAssignment.teacher']);
        
        // Filtrar por sede/grado si aplica
        if ($sedeId || $gradeId) {
            $assignmentQuery = TeachingAssignment::where('school_year_id', $schoolYear->id);
            
            if ($sedeId) {
                $assignmentQuery->where('sede_id', $sedeId);
            }
            
            if ($gradeId) {
                // Si es multigrado, incluir asignaciones del grado multigrado 
                // y de los grados dentro del rango
                $grade = Grade::find($gradeId);
                if ($grade && $grade->type === 'multi' && $grade->min_grade && $grade->max_grade) {
                    $gradesInRange = Grade::where('is_active', true)
                        ->whereBetween('level', [$grade->min_grade, $grade->max_grade])
                        ->pluck('id')
                        ->toArray();
                    // Incluir el grado multigrado y los grados del rango
                    $allGradeIds = array_unique(array_merge([$gradeId], $gradesInRange));
                    $assignmentQuery->whereIn('grade_id', $allGradeIds);
                } else {
                    $assignmentQuery->where('grade_id', $gradeId);
                }
            }
            
            $assignmentIds = $assignmentQuery->pluck('id');
            $query->whereIn('teaching_assignment_id', $assignmentIds);
        }
        
        $allGrades = $query->get();
        
        // Indexar por assignment_student_period
        foreach ($allGrades as $grade) {
            $key = "{$grade->teaching_assignment_id}_{$grade->student_id}_{$grade->period_id}";
            $this->gradesCache[$key] = $grade;
        }
    }

    /**
     * Generar ZIP con todos los boletines
     * Optimizado para grandes cantidades de estudiantes
     */
    public function generateBulkZip(
        Period $period,
        ?int $sedeId = null,
        ?int $gradeId = null
    ): ?array {
        // Aumentar límite de tiempo para generación masiva
        set_time_limit(600); // 10 minutos
        ini_set('memory_limit', '512M');
        
        $startTime = microtime(true);
        
        $result = $this->generateBulkReports($period, $sedeId, $gradeId);

        if (empty($result['reports'])) {
            return null;
        }

        $timestamp = now()->format('Ymd_His');
        $zipFileName = "boletines_{$period->schoolYear->name}_P{$period->number}_{$timestamp}.zip";
        $zipPath = storage_path('app/public/reports/' . $zipFileName);

        // Crear directorio si no existe
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        $successCount = 0;
        $errorCount = count($result['errors']);
        $totalReports = count($result['reports']);

        foreach ($result['reports'] as $index => $report) {
            try {
                $pdf = Pdf::loadView('pdf.report-card-v2', $report['data']);
                $pdf->setPaper('letter', 'portrait');
                $pdf->setOption('isHtml5ParserEnabled', true);
                $pdf->setOption('dpi', 150);

                $fileName = $this->sanitizeFileName(
                    $report['sede']->name . '/' .
                    $report['grade']->name . '/' .
                    $report['student']->identification . '_' .
                    $report['student']->name . '.pdf'
                );

                $zip->addFromString($fileName, $pdf->output());
                $successCount++;
                
                // Liberar memoria cada 10 boletines
                if ($index % 10 === 0) {
                    gc_collect_cycles();
                }
            } catch (\Exception $e) {
                $errorCount++;
                \Log::warning("Error generando PDF para {$report['student']->name}: " . $e->getMessage());
            }
        }

        $zip->close();

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        return [
            'path' => 'reports/' . $zipFileName,
            'total' => count($result['reports']),
            'success' => $successCount,
            'errors' => $errorCount,
            'time' => $executionTime,
        ];
    }

    /**
     * Obtener configuración del colegio (con cache)
     */
    private function getSchoolSettings(): array
    {
        if ($this->settingsCache !== null) {
            return $this->settingsCache;
        }

        // Obtener logo y convertir a base64 para PDF
        $logoBase64 = null;
        $logoPath = SchoolSetting::get('school_logo');
        if ($logoPath) {
            $fullPath = storage_path('app/public/' . $logoPath);
            if (file_exists($fullPath)) {
                $logoData = file_get_contents($fullPath);
                $logoMime = mime_content_type($fullPath);
                $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
            }
        }

        // Obtener firma del rector y convertir a base64
        $rectorSignatureBase64 = null;
        $rectorSignaturePath = SchoolSetting::get('rector_signature');
        if ($rectorSignaturePath) {
            $fullPath = storage_path('app/public/' . $rectorSignaturePath);
            if (file_exists($fullPath)) {
                $signatureData = file_get_contents($fullPath);
                $signatureMime = mime_content_type($fullPath);
                $rectorSignatureBase64 = 'data:' . $signatureMime . ';base64,' . base64_encode($signatureData);
            }
        }

        // Obtener firma del secretario y convertir a base64
        $secretarySignatureBase64 = null;
        $secretarySignaturePath = SchoolSetting::get('secretary_signature');
        if ($secretarySignaturePath) {
            $fullPath = storage_path('app/public/' . $secretarySignaturePath);
            if (file_exists($fullPath)) {
                $signatureData = file_get_contents($fullPath);
                $signatureMime = mime_content_type($fullPath);
                $secretarySignatureBase64 = 'data:' . $signatureMime . ';base64,' . base64_encode($signatureData);
            }
        }

        $this->settingsCache = [
            'name' => SchoolSetting::get('school_name', 'Institución Educativa'),
            'logo' => $logoBase64,
            'slogan' => SchoolSetting::get('school_slogan', ''),
            'nit' => SchoolSetting::get('school_nit', ''),
            'dane' => SchoolSetting::get('school_dane', ''),
            'address' => SchoolSetting::get('school_address', ''),
            'phone' => SchoolSetting::get('school_phone', ''),
            'email' => SchoolSetting::get('school_email', ''),
            'city' => SchoolSetting::get('school_city', ''),
            'department' => SchoolSetting::get('school_department', ''),
            'resolution' => SchoolSetting::get('school_resolution', ''),
            'rector_name' => SchoolSetting::get('rector_name', ''),
            'rector_signature' => $rectorSignatureBase64,
            'secretary_name' => SchoolSetting::get('secretary_name', ''),
            'secretary_signature' => $secretarySignatureBase64,
            'primary_color' => SchoolSetting::get('primary_color', '#3b82f6'),
            'secondary_color' => SchoolSetting::get('secondary_color', '#10b981'),
        ];

        return $this->settingsCache;
    }

    /**
     * Sanitizar nombre de archivo
     */
    private function sanitizeFileName(string $fileName): string
    {
        $fileName = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'ü', 'Ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'N', 'u', 'U'],
            $fileName
        );
        return preg_replace('/[^a-zA-Z0-9_\-\.\/]/', '_', $fileName);
    }

    /**
     * Limpiar cache interno
     */
    public function clearCache(): void
    {
        $this->settingsCache = null;
        $this->assignmentsCache = [];
        $this->gradesCache = [];
    }
}
