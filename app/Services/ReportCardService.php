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
use ZipArchive;

class ReportCardService
{
    /**
     * Obtener los datos completos de un estudiante para el boletín
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
        
        // Obtener asignaciones de docentes para este grado y sede
        $assignments = TeachingAssignment::where('school_year_id', $schoolYear->id)
            ->where('sede_id', $sede->id)
            ->where('grade_id', $grade->id)
            ->where('is_active', true)
            ->with(['subject', 'teacher'])
            ->get();
        
        // Construir datos de calificaciones por asignatura
        $subjectsData = [];
        $totalFinalScore = 0;
        $subjectsWithScore = 0;
        $totalAbsences = [];
        $behaviorByPeriod = [];
        
        foreach ($assignments as $assignment) {
            $subjectData = [
                'name' => $assignment->subject->name,
                'teacher' => $assignment->teacher->name,
                'periods' => [],
                'final' => null,
                'performance' => null,
                'absences' => 0,        // Fallas totales de esta asignatura
                'behavior' => null,     // Comportamiento de esta asignatura (último registrado)
            ];
            
            $periodScores = [];
            $subjectTotalAbsences = 0;
            $lastBehavior = null;
            
            foreach ($periods as $p) {
                $grade_record = StudentGrade::where('teaching_assignment_id', $assignment->id)
                    ->where('student_id', $student->id)
                    ->where('period_id', $p->id)
                    ->first();
                
                $score = $grade_record?->final_score;
                $subjectData['periods'][$p->number] = $score;
                
                if ($score !== null) {
                    $periodScores[] = $score;
                }
                
                // Recolectar inasistencias y comportamiento
                if ($grade_record) {
                    // Sumar inasistencias de esta asignatura
                    $subjectTotalAbsences += $grade_record->absences ?? 0;
                    
                    // Guardar el último comportamiento registrado para esta asignatura
                    if ($grade_record->behavior) {
                        $lastBehavior = $grade_record->behavior;
                    }
                    
                    // Totales generales por periodo
                    if (!isset($totalAbsences[$p->number])) {
                        $totalAbsences[$p->number] = 0;
                    }
                    $totalAbsences[$p->number] += $grade_record->absences ?? 0;
                    
                    if ($grade_record->behavior && !isset($behaviorByPeriod[$p->number])) {
                        $behaviorByPeriod[$p->number] = $grade_record->behavior;
                    }
                }
            }
            
            // Asignar fallas y comportamiento a esta asignatura
            $subjectData['absences'] = $subjectTotalAbsences;
            $subjectData['behavior'] = $lastBehavior;
            
            // Calcular definitiva (promedio de todos los períodos con nota)
            if (count($periodScores) > 0) {
                $subjectData['final'] = round(array_sum($periodScores) / count($periodScores), 1);
                $subjectData['performance'] = $this->getPerformanceLevel($subjectData['final']);
                $totalFinalScore += $subjectData['final'];
                $subjectsWithScore++;
            }
            
            // Marcar si tiene todas las notas de los 4 períodos
            $subjectData['hasAllGrades'] = count($periodScores) >= count($periods);
            
            $subjectsData[] = $subjectData;
        }
        
        // Calcular promedio general
        $generalAverage = $subjectsWithScore > 0 
            ? round($totalFinalScore / $subjectsWithScore, 2) 
            : null;
        
        // Verificar si todas las materias tienen las 4 notas completas
        $allSubjectsComplete = count($subjectsData) > 0 && collect($subjectsData)->every(fn($s) => $s['hasAllGrades']);
        
        // Contar materias perdidas (nota final < 3.0)
        $failedSubjects = collect($subjectsData)->filter(fn($s) => $s['final'] !== null && $s['final'] < 3.0)->count();
        
        // Determinar estado final (solo si todas las notas están completas)
        $finalStatus = null;
        $passed = null;
        
        if ($allSubjectsComplete && count($subjectsData) > 0) {
            if ($failedSubjects >= 3) {
                $finalStatus = 'REPROBADO';
                $passed = false;
            } elseif ($failedSubjects >= 1) {
                $finalStatus = 'APLAZADO';
                $passed = null; // Ni aprobado ni reprobado, pendiente de recuperación
            } else {
                $finalStatus = 'APROBADO';
                $passed = true;
            }
        }
        
        // Obtener director de grupo (primer docente asignado al grado)
        $director = $assignments->first()?->teacher;
        
        // Calcular puesto en el grado
        $position = $this->calculatePosition($student, $grade, $sede, $schoolYear, $periods);
        $totalStudents = $this->getTotalStudentsInGrade($grade, $sede, $schoolYear);
        
        return [
            'student' => $student,
            'enrollment' => $enrollment,
            'sede' => $sede,
            'grade' => $grade,
            'schoolYear' => $schoolYear,
            'period' => $period,
            'periods' => $periods,
            'subjects' => $subjectsData,
            'generalAverage' => $generalAverage,
            'performanceLevel' => $this->getPerformanceLevel($generalAverage),
            'passed' => $passed,
            'finalStatus' => $finalStatus,
            'failedSubjects' => $failedSubjects,
            'allSubjectsComplete' => $allSubjectsComplete,
            'position' => $position,
            'totalStudents' => $totalStudents,
            'absences' => $totalAbsences,
            'behavior' => $behaviorByPeriod[$period->number] ?? 'BASICO',
            'director' => $director,
            'settings' => $this->getSchoolSettings(),
        ];
    }
    
    /**
     * Generar PDF de un boletín individual
     */
    public function generatePDF(User $student, Period $period): ?string
    {
        $data = $this->getStudentReportData($student, $period);
        
        if (empty($data)) {
            return null;
        }
        
        $pdf = Pdf::loadView('pdf.report-card', $data);
        $pdf->setPaper('letter', 'portrait');
        
        return $pdf->output();
    }
    
    /**
     * Generar boletines masivos por sede y grado
     */
    public function generateBulkReports(
        Period $period,
        ?int $sedeId = null,
        ?int $gradeId = null
    ): array {
        $schoolYear = $period->schoolYear;
        
        $query = Enrollment::where('school_year_id', $schoolYear->id)
            ->where('status', 'active')
            ->with(['student', 'sede', 'grade']);
        
        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        }
        
        if ($gradeId) {
            $query->where('grade_id', $gradeId);
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
     * Generar ZIP con todos los boletines
     */
    public function generateBulkZip(
        Period $period,
        ?int $sedeId = null,
        ?int $gradeId = null
    ): ?string {
        $result = $this->generateBulkReports($period, $sedeId, $gradeId);
        
        if (empty($result['reports'])) {
            return null;
        }
        
        $zipFileName = 'boletines_' . $period->schoolYear->name . '_P' . $period->number . '_' . time() . '.zip';
        $zipPath = storage_path('app/public/reports/' . $zipFileName);
        
        // Crear directorio si no existe
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }
        
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }
        
        foreach ($result['reports'] as $report) {
            $pdf = Pdf::loadView('pdf.report-card', $report['data']);
            $pdf->setPaper('letter', 'portrait');
            
            $fileName = $this->sanitizeFileName(
                $report['sede']->name . '/' .
                $report['grade']->name . '/' .
                $report['student']->identification . '_' .
                $report['student']->name . '.pdf'
            );
            
            $zip->addFromString($fileName, $pdf->output());
        }
        
        $zip->close();
        
        return 'reports/' . $zipFileName;
    }
    
    /**
     * Obtener nivel de desempeño según la nota
     */
    private function getPerformanceLevel(?float $score): string
    {
        if ($score === null) return '-';
        
        if ($score >= 4.6) return 'SUPERIOR';
        if ($score >= 4.0) return 'ALTO';
        if ($score >= 3.0) return 'BASICO';
        return 'BAJO';
    }
    
    /**
     * Calcular posición del estudiante en el grado
     */
    private function calculatePosition(
        User $student,
        Grade $grade,
        Sede $sede,
        SchoolYear $schoolYear,
        Collection $periods
    ): int {
        // Obtener todos los estudiantes del grado
        $enrollments = Enrollment::where('school_year_id', $schoolYear->id)
            ->where('sede_id', $sede->id)
            ->where('grade_id', $grade->id)
            ->where('status', 'active')
            ->pluck('student_id');
        
        // Calcular promedio de cada estudiante
        $averages = [];
        
        foreach ($enrollments as $studentId) {
            $assignments = TeachingAssignment::where('school_year_id', $schoolYear->id)
                ->where('sede_id', $sede->id)
                ->where('grade_id', $grade->id)
                ->where('is_active', true)
                ->get();
            
            $totalScore = 0;
            $subjectsCount = 0;
            
            foreach ($assignments as $assignment) {
                $periodScores = [];
                
                foreach ($periods as $period) {
                    $gradeRecord = StudentGrade::where('teaching_assignment_id', $assignment->id)
                        ->where('student_id', $studentId)
                        ->where('period_id', $period->id)
                        ->first();
                    
                    if ($gradeRecord?->final_score !== null) {
                        $periodScores[] = $gradeRecord->final_score;
                    }
                }
                
                if (count($periodScores) > 0) {
                    $totalScore += array_sum($periodScores) / count($periodScores);
                    $subjectsCount++;
                }
            }
            
            $averages[$studentId] = $subjectsCount > 0 ? $totalScore / $subjectsCount : 0;
        }
        
        // Ordenar de mayor a menor
        arsort($averages);
        
        // Encontrar posición del estudiante
        $position = 1;
        foreach ($averages as $studentId => $avg) {
            if ($studentId == $student->id) {
                return $position;
            }
            $position++;
        }
        
        return $position;
    }
    
    /**
     * Obtener total de estudiantes en el grado
     */
    private function getTotalStudentsInGrade(Grade $grade, Sede $sede, SchoolYear $schoolYear): int
    {
        return Enrollment::where('school_year_id', $schoolYear->id)
            ->where('sede_id', $sede->id)
            ->where('grade_id', $grade->id)
            ->where('status', 'active')
            ->count();
    }
    
    /**
     * Obtener configuración del colegio
     */
    private function getSchoolSettings(): array
    {
        return [
            'name' => SchoolSetting::get('school_name', 'Institución Educativa'),
            'logo' => SchoolSetting::getLogoUrl(),
            'slogan' => SchoolSetting::get('school_slogan', ''),
            'nit' => SchoolSetting::get('school_nit', ''),
            'dane' => SchoolSetting::get('school_dane', ''),
            'address' => SchoolSetting::get('school_address', ''),
            'phone' => SchoolSetting::get('school_phone', ''),
            'email' => SchoolSetting::get('school_email', ''),
            'city' => SchoolSetting::get('school_city', ''),
            'department' => SchoolSetting::get('school_department', ''),
            'resolution' => SchoolSetting::get('school_resolution', ''),
            'primary_color' => SchoolSetting::get('primary_color', '#3b82f6'),
        ];
    }
    
    /**
     * Sanitizar nombre de archivo
     */
    private function sanitizeFileName(string $fileName): string
    {
        $fileName = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'], 
                                ['a', 'e', 'i', 'o', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'N'], 
                                $fileName);
        return preg_replace('/[^a-zA-Z0-9_\-\.\/]/', '_', $fileName);
    }
}
