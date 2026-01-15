<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Period;
use App\Models\SchoolYear;
use App\Models\StudentGrade;
use App\Models\TeachingAssignment;
use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Reader\XLSX\Reader;

class BulkGradesExcelService
{
    protected const BEHAVIOR_OPTIONS = ['BAJO', 'BASICO', 'ALTO', 'SUPERIOR'];

    /**
     * Exportar todas las asignaturas del profesor en un solo Excel con múltiples hojas
     * Cada hoja representa una asignatura con sus estudiantes
     */
    public function exportAllSubjects(int $teacherId, int $periodId): string
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            throw new \Exception('No hay año escolar activo');
        }

        $period = Period::find($periodId);
        if (!$period) {
            throw new \Exception('Periodo no encontrado');
        }

        // Obtener todas las asignaciones activas del profesor
        $assignments = TeachingAssignment::with(['sede', 'grade', 'subject', 'schoolYear'])
            ->where('teacher_id', $teacherId)
            ->where('school_year_id', $activeYear->id)
            ->where('is_active', true)
            ->orderBy('sede_id')
            ->get();

        if ($assignments->isEmpty()) {
            throw new \Exception('No tienes asignaciones activas');
        }

        // Crear nombre del archivo
        $userName = auth()->user()->name ?? 'Docente';
        $userNameClean = preg_replace('/[^a-zA-Z0-9]/', '_', $userName);
        $fileName = "Calificaciones_P{$period->number}_{$userNameClean}_" . date('Y-m-d') . ".xlsx";
        
        $filePath = storage_path("app/temp/{$fileName}");
        
        // Asegurar que el directorio existe
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $options = new Options();
        $writer = new Writer($options);
        $writer->openToFile($filePath);

        // Crear una hoja por cada asignatura
        $isFirstSheet = true;
        foreach ($assignments as $assignment) {
            $sheetName = $this->sanitizeSheetName($assignment->subject->name, $assignment->sede->name, $assignment->grade->name);
            
            if ($isFirstSheet) {
                // La primera hoja ya está creada, solo renombrarla
                $writer->getCurrentSheet()->setName($sheetName);
                $isFirstSheet = false;
            } else {
                // Crear nueva hoja
                $newSheet = $writer->addNewSheetAndMakeItCurrent();
                $newSheet->setName($sheetName);
            }

            // Escribir contenido de la hoja
            $this->writeAssignmentSheet($writer, $assignment, $period);
        }

        $writer->close();

        return $filePath;
    }

    /**
     * Sanitizar nombre de hoja de Excel (máx 31 caracteres, sin caracteres especiales)
     */
    protected function sanitizeSheetName(string $subject, string $sede, string $grade): string
    {
        // Abreviar nombres largos
        $subjectShort = mb_substr($subject, 0, 15);
        $sedeShort = mb_substr($sede, 0, 8);
        $gradeShort = mb_substr($grade, 0, 5);
        
        $name = "{$subjectShort}_{$gradeShort}";
        
        // Remover caracteres no permitidos en nombres de hojas
        $name = preg_replace('/[\\\\\/\?\*\[\]\:\']/', '', $name);
        
        // Limitar a 31 caracteres (límite de Excel)
        return mb_substr($name, 0, 31);
    }

    /**
     * Escribir contenido de una hoja para una asignatura
     */
    protected function writeAssignmentSheet(Writer $writer, TeachingAssignment $assignment, Period $period): void
    {
        // Verificar si es multigrado
        $assignedGrade = $assignment->grade;
        $isMultigrade = $assignedGrade->type === 'multi';

        // Cargar estudiantes matriculados
        $students = $this->getStudentsForAssignment($assignment);

        // Cargar notas existentes
        $existingGrades = StudentGrade::where('teaching_assignment_id', $assignment->id)
            ->where('period_id', $period->id)
            ->get()
            ->keyBy('student_id');

        // ========== ESTILOS ==========
        $titleStyle = (new Style())
            ->setFontBold()
            ->setFontSize(14)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('1F4E79')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);

        $infoStyle = (new Style())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor('1F4E79')
            ->setBackgroundColor('D6DCE4')
            ->setCellAlignment(CellAlignment::LEFT);

        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('2E75B6')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);

        $subHeaderStyle = (new Style())
            ->setFontSize(8)
            ->setFontColor('666666')
            ->setBackgroundColor('BDD7EE')
            ->setCellAlignment(CellAlignment::CENTER);

        $studentStyle = (new Style())
            ->setFontSize(10)
            ->setBackgroundColor('F2F2F2')
            ->setCellAlignment(CellAlignment::LEFT);

        $editableStyle = (new Style())
            ->setFontSize(10)
            ->setBackgroundColor('FFFFD6')
            ->setCellAlignment(CellAlignment::CENTER);

        $editableStyleAlt = (new Style())
            ->setFontSize(10)
            ->setBackgroundColor('FFF2CC')
            ->setCellAlignment(CellAlignment::CENTER);

        $notesHeaderStyle = (new Style())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('C65911')
            ->setCellAlignment(CellAlignment::LEFT);

        $notesStyle = (new Style())
            ->setFontSize(9)
            ->setFontColor('833C0C')
            ->setBackgroundColor('FCE4D6');

        $separatorStyle = (new Style())
            ->setBackgroundColor('FFFFFF');

        // ========== CONTENIDO ==========

        // Fila 1: Título
        $gradeLabel = $isMultigrade ? $assignment->grade->name . ' (Multigrado)' : $assignment->grade->name;
        $writer->addRow(new Row([
            Cell::fromValue("📊 {$assignment->subject->name}", $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
        ]));

        // Fila 2: Información
        $infoCells = [
            Cell::fromValue('Grado:', $infoStyle),
            Cell::fromValue($gradeLabel, $infoStyle),
            Cell::fromValue('Sede:', $infoStyle),
            Cell::fromValue($assignment->sede->name, $infoStyle),
            Cell::fromValue('Periodo:', $infoStyle),
            Cell::fromValue($period->name, $infoStyle),
            Cell::fromValue('ID:', $infoStyle),
            Cell::fromValue($assignment->id . '-' . $period->id, $infoStyle),
        ];
        $writer->addRow(new Row($infoCells));

        // Fila 3: Año escolar
        $infoCells2 = [
            Cell::fromValue('Año Escolar:', $infoStyle),
            Cell::fromValue($assignment->schoolYear->name, $infoStyle),
            Cell::fromValue('Docente:', $infoStyle),
            Cell::fromValue($assignment->teacher->name ?? '', $infoStyle),
            Cell::fromValue('Estudiantes:', $infoStyle),
            Cell::fromValue($students->count(), $infoStyle),
            Cell::fromValue('', $infoStyle),
            Cell::fromValue('', $infoStyle),
        ];
        $writer->addRow(new Row($infoCells2));

        // Fila 4: Separador
        $writer->addRow(new Row([Cell::fromValue('', $separatorStyle)]));

        // Fila 5: Encabezados de columnas
        $headerCells = [
            Cell::fromValue('N°', $headerStyle),
            Cell::fromValue('IDENTIFICACIÓN', $headerStyle),
            Cell::fromValue('NOMBRE ESTUDIANTE', $headerStyle),
        ];
        
        if ($isMultigrade) {
            $headerCells[] = Cell::fromValue('GRADO', $headerStyle);
        }
        
        $headerCells = array_merge($headerCells, [
            Cell::fromValue('TAREAS', $headerStyle),
            Cell::fromValue('EVALUACIONES', $headerStyle),
            Cell::fromValue('AUTOEVAL.', $headerStyle),
            Cell::fromValue('COMPORTAMIENTO', $headerStyle),
            Cell::fromValue('INASIST.', $headerStyle),
        ]);
        
        $writer->addRow(new Row($headerCells));

        // Fila 6: Sub-encabezados
        $subHeaderCells = [
            Cell::fromValue('', $subHeaderStyle),
            Cell::fromValue('No modificar', $subHeaderStyle),
            Cell::fromValue('No modificar', $subHeaderStyle),
        ];
        
        if ($isMultigrade) {
            $subHeaderCells[] = Cell::fromValue('No modificar', $subHeaderStyle);
        }
        
        $subHeaderCells = array_merge($subHeaderCells, [
            Cell::fromValue('40% (1.0-5.0)', $subHeaderStyle),
            Cell::fromValue('50% (1.0-5.0)', $subHeaderStyle),
            Cell::fromValue('10% (1.0-5.0)', $subHeaderStyle),
            Cell::fromValue('BAJO/BASICO/ALTO/SUPERIOR', $subHeaderStyle),
            Cell::fromValue('Número ≥0', $subHeaderStyle),
        ]);
        
        $writer->addRow(new Row($subHeaderCells));

        // Datos de estudiantes
        $rowNum = 0;
        foreach ($students as $enrollment) {
            $rowNum++;
            $grade = $existingGrades->get($enrollment->student_id);
            
            $currentEditStyle = ($rowNum % 2 == 0) ? $editableStyleAlt : $editableStyle;
            $currentStudentStyle = ($rowNum % 2 == 0) 
                ? (new Style())->setFontSize(10)->setBackgroundColor('E7E6E6')->setCellAlignment(CellAlignment::LEFT)
                : $studentStyle;
            
            $numStyle = (new Style())
                ->setFontSize(10)
                ->setFontBold()
                ->setBackgroundColor(($rowNum % 2 == 0) ? 'E7E6E6' : 'F2F2F2')
                ->setCellAlignment(CellAlignment::CENTER);

            $rowCells = [
                Cell::fromValue($rowNum, $numStyle),
                Cell::fromValue($enrollment->student->identification, $currentStudentStyle),
                Cell::fromValue($enrollment->student->name, $currentStudentStyle),
            ];
            
            if ($isMultigrade) {
                $rowCells[] = Cell::fromValue($enrollment->grade->name, $currentStudentStyle);
            }
            
            $rowCells = array_merge($rowCells, [
                Cell::fromValue($grade?->tasks_score ?? '', $currentEditStyle),
                Cell::fromValue($grade?->evaluations_score ?? '', $currentEditStyle),
                Cell::fromValue($grade?->self_score ?? '', $currentEditStyle),
                Cell::fromValue($grade?->behavior ?? '', $currentEditStyle),
                Cell::fromValue($grade?->absences ?? 0, $currentEditStyle),
            ]);

            $writer->addRow(new Row($rowCells));
        }

        // Separador
        $writer->addRow(new Row([Cell::fromValue('', $separatorStyle)]));
        $writer->addRow(new Row([Cell::fromValue('', $separatorStyle)]));

        // Instrucciones
        $writer->addRow(new Row([
            Cell::fromValue('⚠️ INSTRUCCIONES:', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
        ]));

        $instructions = [
            '• Las celdas AMARILLAS son editables. Las grises NO se deben modificar.',
            '• TAREAS, EVALUACIONES, AUTOEVAL.: Valores entre 1.0 y 5.0',
            '• COMPORTAMIENTO: BAJO, BASICO, ALTO o SUPERIOR',
            '• INASISTENCIAS: Número mayor o igual a 0',
            '• NO eliminar ni agregar filas.',
        ];

        foreach ($instructions as $instruction) {
            $writer->addRow(new Row([
                Cell::fromValue($instruction, $notesStyle),
                Cell::fromValue('', $notesStyle),
                Cell::fromValue('', $notesStyle),
                Cell::fromValue('', $notesStyle),
                Cell::fromValue('', $notesStyle),
                Cell::fromValue('', $notesStyle),
                Cell::fromValue('', $notesStyle),
                Cell::fromValue('', $notesStyle),
            ]));
        }
    }

    /**
     * Obtener estudiantes para una asignación (maneja multigrado)
     */
    protected function getStudentsForAssignment(TeachingAssignment $assignment): Collection
    {
        $assignedGrade = $assignment->grade;
        $isMultigrade = $assignedGrade->type === 'multi';

        if ($isMultigrade && $assignedGrade->min_grade && $assignedGrade->max_grade) {
            $gradesInRange = Grade::where('is_active', true)
                ->whereBetween('level', [$assignedGrade->min_grade, $assignedGrade->max_grade])
                ->pluck('id')
                ->toArray();

            return Enrollment::with(['student', 'grade'])
                ->where('school_year_id', $assignment->school_year_id)
                ->where('sede_id', $assignment->sede_id)
                ->whereIn('grade_id', $gradesInRange)
                ->where('status', 'active')
                ->get()
                ->sortBy(['grade.level', 'student.name']);
        }

        return Enrollment::with(['student', 'grade'])
            ->where('school_year_id', $assignment->school_year_id)
            ->where('sede_id', $assignment->sede_id)
            ->where('grade_id', $assignment->grade_id)
            ->where('status', 'active')
            ->get()
            ->sortBy('student.name');
    }

    /**
     * Importar archivo Excel con múltiples hojas
     */
    public function importAllSubjects(string $filePath, int $teacherId): array
    {
        $results = [
            'total_success' => 0,
            'total_errors' => 0,
            'total_skipped' => 0,
            'sheets' => [],
        ];

        $activeYear = SchoolYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            $results['sheets'][] = [
                'name' => 'General',
                'success' => 0,
                'errors' => ['No hay año escolar activo'],
                'skipped' => 0,
            ];
            return $results;
        }

        // Obtener todas las asignaciones del profesor indexadas por ID
        $assignments = TeachingAssignment::with(['sede', 'grade', 'subject'])
            ->where('teacher_id', $teacherId)
            ->where('school_year_id', $activeYear->id)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $reader = new Reader();
        $reader->open($filePath);

        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetResult = [
                'name' => $sheet->getName(),
                'success' => 0,
                'errors' => [],
                'skipped' => 0,
            ];

            $assignmentId = null;
            $periodId = null;
            $assignment = null;
            $rowNumber = 0;
            $hasGradeColumn = null;
            $enrolledStudents = null;

            foreach ($sheet->getRowIterator() as $row) {
                $rowNumber++;
                $cells = $row->toArray();

                // Saltar filas vacías
                if (count($cells) < 2) {
                    continue;
                }

                // Buscar la fila con el ID de asignación (formato: "ID:" en una celda)
                if ($assignmentId === null) {
                    foreach ($cells as $idx => $cell) {
                        $cellStr = trim((string)$cell);
                        if (preg_match('/^(\d+)-(\d+)$/', $cellStr, $matches)) {
                            $assignmentId = (int)$matches[1];
                            $periodId = (int)$matches[2];
                            break;
                        }
                    }
                    
                    if ($assignmentId !== null) {
                        // Verificar que la asignación pertenece al profesor
                        if (!isset($assignments[$assignmentId])) {
                            $sheetResult['errors'][] = "Asignación #{$assignmentId} no encontrada o no autorizada";
                            break;
                        }

                        $assignment = $assignments[$assignmentId];

                        // Verificar periodo
                        $period = Period::find($periodId);
                        if (!$period || $period->is_finalized) {
                            $sheetResult['errors'][] = "Periodo finalizado o no válido";
                            break;
                        }

                        // Cargar estudiantes matriculados
                        $enrolledStudents = $this->getEnrolledStudentsMap($assignment);
                    }
                    continue;
                }

                // Si no encontramos asignación válida, saltar
                if ($assignment === null) {
                    continue;
                }

                // Detectar si tiene columna de GRADO
                if ($hasGradeColumn === null) {
                    $cellsStr = array_map(fn($c) => strtoupper(trim((string)$c)), $cells);
                    if (in_array('GRADO', $cellsStr)) {
                        $hasGradeColumn = true;
                    } elseif (in_array('TAREAS', $cellsStr)) {
                        $hasGradeColumn = false;
                    }
                    continue;
                }

                // Procesar fila de datos
                $firstCell = trim((string)($cells[0] ?? ''));
                if (!is_numeric($firstCell) || intval($firstCell) < 1) {
                    continue;
                }

                $offset = $hasGradeColumn ? 1 : 0;
                $identification = $this->normalizeIdentification($cells[1] ?? '');
                
                if (empty($identification)) {
                    continue;
                }

                if (!isset($enrolledStudents[$identification])) {
                    $sheetResult['errors'][] = "Fila {$rowNumber}: Estudiante '{$identification}' no encontrado";
                    $sheetResult['skipped']++;
                    continue;
                }

                $studentId = $enrolledStudents[$identification];

                // Parsear valores
                $tasksScore = $this->parseScore($cells[3 + $offset] ?? null);
                $evaluationsScore = $this->parseScore($cells[4 + $offset] ?? null);
                $selfScore = $this->parseScore($cells[5 + $offset] ?? null);
                $behavior = $this->parseBehavior($cells[6 + $offset] ?? null);
                $absences = $this->parseAbsences($cells[7 + $offset] ?? null);

                // Validaciones
                $rowErrors = [];
                if ($tasksScore !== null && ($tasksScore < 1.0 || $tasksScore > 5.0)) {
                    $rowErrors[] = "Tareas fuera de rango";
                }
                if ($evaluationsScore !== null && ($evaluationsScore < 1.0 || $evaluationsScore > 5.0)) {
                    $rowErrors[] = "Evaluaciones fuera de rango";
                }
                if ($selfScore !== null && ($selfScore < 1.0 || $selfScore > 5.0)) {
                    $rowErrors[] = "Autoevaluación fuera de rango";
                }

                if (!empty($rowErrors)) {
                    $sheetResult['errors'][] = "Fila {$rowNumber}: " . implode(', ', $rowErrors);
                }

                // Guardar si tiene datos
                if ($tasksScore !== null || $evaluationsScore !== null || $selfScore !== null || $behavior !== null || $absences > 0) {
                    try {
                        StudentGrade::updateOrCreate(
                            [
                                'teaching_assignment_id' => $assignmentId,
                                'student_id' => $studentId,
                                'period_id' => $periodId,
                            ],
                            [
                                'tasks_score' => $tasksScore,
                                'evaluations_score' => $evaluationsScore,
                                'self_score' => $selfScore,
                                'behavior' => $behavior,
                                'absences' => $absences,
                            ]
                        );
                        $sheetResult['success']++;
                    } catch (\Exception $e) {
                        $sheetResult['errors'][] = "Fila {$rowNumber}: Error al guardar";
                    }
                } else {
                    $sheetResult['skipped']++;
                }
            }

            $results['sheets'][] = $sheetResult;
            $results['total_success'] += $sheetResult['success'];
            $results['total_errors'] += count($sheetResult['errors']);
            $results['total_skipped'] += $sheetResult['skipped'];
        }

        $reader->close();

        return $results;
    }

    /**
     * Obtener mapa de estudiantes matriculados por identificación
     */
    protected function getEnrolledStudentsMap(TeachingAssignment $assignment): array
    {
        $students = $this->getStudentsForAssignment($assignment);
        
        return $students->mapWithKeys(function ($enrollment) {
            $id = $this->normalizeIdentification($enrollment->student->identification);
            return [$id => $enrollment->student_id];
        })->toArray();
    }

    /**
     * Parsear valor de nota
     */
    protected function parseScore($value): ?float
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }
        
        $value = str_replace(',', '.', (string)$value);
        $value = floatval($value);
        
        return $value > 0 ? round($value, 1) : null;
    }

    /**
     * Parsear valor de comportamiento
     */
    protected function parseBehavior($value): ?string
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }
        
        $value = strtoupper(trim((string)$value));
        
        return in_array($value, self::BEHAVIOR_OPTIONS) ? $value : null;
    }

    /**
     * Parsear valor de inasistencias
     */
    protected function parseAbsences($value): int
    {
        if ($value === null || $value === '' || $value === '-') {
            return 0;
        }
        
        return max(0, intval($value));
    }

    /**
     * Normalizar identificación
     */
    protected function normalizeIdentification($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        
        $value = (string)$value;
        
        if (preg_match('/^\d+\.0+$/', $value)) {
            $value = preg_replace('/\.0+$/', '', $value);
        }
        
        return trim(strtoupper(str_replace(' ', '', $value)));
    }
}
