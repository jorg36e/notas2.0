<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\StudentGrade;
use App\Models\TeachingAssignment;
use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Reader\XLSX\Reader;

class GradesExcelService
{
    protected const BEHAVIOR_OPTIONS = ['BAJO', 'BASICO', 'ALTO', 'SUPERIOR'];

    /**
     * Generar archivo Excel para descargar con diseño mejorado
     */
    public function export(TeachingAssignment $assignment, int $periodId): string
    {
        // Verificar si es multigrado
        $assignedGrade = $assignment->grade;
        $isMultigrade = $assignedGrade->type === 'multi';

        // Cargar estudiantes matriculados
        if ($isMultigrade && $assignedGrade->min_grade && $assignedGrade->max_grade) {
            // MULTIGRADO: Buscar estudiantes de todos los grados en el rango de niveles
            $gradesInRange = Grade::where('is_active', true)
                ->whereBetween('level', [$assignedGrade->min_grade, $assignedGrade->max_grade])
                ->pluck('id')
                ->toArray();

            $students = Enrollment::with(['student', 'grade'])
                ->where('school_year_id', $assignment->school_year_id)
                ->where('sede_id', $assignment->sede_id)
                ->whereIn('grade_id', $gradesInRange)
                ->where('status', 'active')
                ->get()
                ->sortBy(['grade.level', 'student.name']);
        } else {
            // GRADO ÚNICO: Comportamiento normal
            $students = Enrollment::with(['student', 'grade'])
                ->where('school_year_id', $assignment->school_year_id)
                ->where('sede_id', $assignment->sede_id)
                ->where('grade_id', $assignment->grade_id)
                ->where('status', 'active')
                ->get()
                ->sortBy('student.name');
        }

        // Cargar notas existentes
        $existingGrades = StudentGrade::where('teaching_assignment_id', $assignment->id)
            ->where('period_id', $periodId)
            ->get()
            ->keyBy('student_id');

        // Crear nombre del archivo
        $period = \App\Models\Period::find($periodId);
        $gradeName = str_replace(' ', '', $assignment->grade->name);
        $sedeName = str_replace(' ', '', $assignment->sede->name);
        $fileName = "Periodo_{$period->number}_Grado_{$gradeName}_Sede_{$sedeName}.xlsx";
        
        $filePath = storage_path("app/temp/{$fileName}");
        
        // Asegurar que el directorio existe
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $options = new Options();
        $writer = new Writer($options);
        $writer->openToFile($filePath);

        // ========== ESTILOS ==========
        
        // Estilo título principal
        $titleStyle = (new Style())
            ->setFontBold()
            ->setFontSize(16)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('1F4E79')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);

        // Estilo subtítulo/información
        $infoStyle = (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor('1F4E79')
            ->setBackgroundColor('D6DCE4')
            ->setCellAlignment(CellAlignment::LEFT);

        // Estilo encabezados de columnas
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('2E75B6')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);

        // Estilo datos estudiante (identificación y nombre)
        $studentStyle = (new Style())
            ->setFontSize(10)
            ->setBackgroundColor('F2F2F2')
            ->setCellAlignment(CellAlignment::LEFT);

        // Estilo celdas editables (notas)
        $editableStyle = (new Style())
            ->setFontSize(10)
            ->setBackgroundColor('FFFFD6')
            ->setCellAlignment(CellAlignment::CENTER);

        // Estilo celdas editables alternativo (filas pares)
        $editableStyleAlt = (new Style())
            ->setFontSize(10)
            ->setBackgroundColor('FFF2CC')
            ->setCellAlignment(CellAlignment::CENTER);

        // Estilo notas de validación
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

        // Estilo fila vacía separadora
        $separatorStyle = (new Style())
            ->setBackgroundColor('FFFFFF');

        // ========== CONTENIDO ==========

        // Fila 1: Título principal
        $writer->addRow(new Row([
            Cell::fromValue('📊 PLANTILLA DE CALIFICACIONES', $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
            Cell::fromValue('', $titleStyle),
        ]));

        // Fila 2: Información del curso
        $gradeLabel = $isMultigrade ? $assignment->grade->name . ' (Multigrado)' : $assignment->grade->name;
        $infoCells2 = [
            Cell::fromValue('📚 Asignatura:', $infoStyle),
            Cell::fromValue($assignment->subject->name, $infoStyle),
            Cell::fromValue('🎓 Grado:', $infoStyle),
            Cell::fromValue($gradeLabel, $infoStyle),
            Cell::fromValue('🏫 Sede:', $infoStyle),
            Cell::fromValue($assignment->sede->name, $infoStyle),
            Cell::fromValue('', $infoStyle),
        ];
        if ($isMultigrade) {
            $infoCells2[] = Cell::fromValue('', $infoStyle);
        }
        $writer->addRow(new Row($infoCells2));

        // Fila 3: Información del periodo
        $infoCells3 = [
            Cell::fromValue('📅 Periodo:', $infoStyle),
            Cell::fromValue($period->name, $infoStyle),
            Cell::fromValue('📆 Año Escolar:', $infoStyle),
            Cell::fromValue($assignment->schoolYear->name, $infoStyle),
            Cell::fromValue('🔑 ID:', $infoStyle),
            Cell::fromValue($assignment->id . '-' . $periodId, $infoStyle),
            Cell::fromValue('', $infoStyle),
        ];
        if ($isMultigrade) {
            $infoCells3[] = Cell::fromValue('', $infoStyle);
        }
        $writer->addRow(new Row($infoCells3));

        // Fila 4: Separador
        $writer->addRow(new Row([
            Cell::fromValue('', $separatorStyle),
        ]));

        // Fila 5: Encabezados de columnas
        $headerCells = [
            Cell::fromValue('N°', $headerStyle),
            Cell::fromValue('IDENTIFICACIÓN', $headerStyle),
            Cell::fromValue('NOMBRE COMPLETO DEL ESTUDIANTE', $headerStyle),
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

        // Fila 6: Subencabezados (porcentajes/rangos)
        $subHeaderStyle = (new Style())
            ->setFontSize(8)
            ->setFontColor('666666')
            ->setBackgroundColor('BDD7EE')
            ->setCellAlignment(CellAlignment::CENTER);

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
            
            // Alternar estilos para filas pares/impares
            $currentEditStyle = ($rowNum % 2 == 0) ? $editableStyleAlt : $editableStyle;
            $currentStudentStyle = ($rowNum % 2 == 0) 
                ? (new Style())->setFontSize(10)->setBackgroundColor('E7E6E6')->setCellAlignment(CellAlignment::LEFT)
                : $studentStyle;
            
            $numStyle = (new Style())
                ->setFontSize(10)
                ->setFontBold()
                ->setBackgroundColor(($rowNum % 2 == 0) ? 'E7E6E6' : 'F2F2F2')
                ->setCellAlignment(CellAlignment::CENTER);

            // Construir celdas de la fila
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

        // Filas separadoras
        $writer->addRow(new Row([Cell::fromValue('', $separatorStyle)]));
        $writer->addRow(new Row([Cell::fromValue('', $separatorStyle)]));

        // Notas de validación
        $writer->addRow(new Row([
            Cell::fromValue('⚠️ INSTRUCCIONES Y VALIDACIONES:', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
            Cell::fromValue('', $notesHeaderStyle),
        ]));

        $instructions = [
            '1️⃣  Las columnas AMARILLAS son editables. Las grises NO se deben modificar.',
            '2️⃣  TAREAS, EVALUACIONES, AUTOEVAL.: Valores numéricos entre 1.0 y 5.0 (ej: 3.5, 4.0)',
            '3️⃣  COMPORTAMIENTO: Solo valores permitidos → BAJO, BASICO, ALTO, SUPERIOR (o dejar vacío)',
            '4️⃣  INASISTENCIAS: Número entero mayor o igual a 0 (ej: 0, 1, 2, 5)',
            '5️⃣  NO eliminar ni agregar filas. NO cambiar la estructura del archivo.',
            '6️⃣  Guarde el archivo y súbalo en el sistema para actualizar las calificaciones.',
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

        $writer->close();

        return $filePath;
    }

    /**
     * Importar archivo Excel y actualizar notas
     */
    public function import(string $filePath, TeachingAssignment $assignment, int $periodId): array
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'skipped' => 0,
        ];

        // Verificar que el periodo no esté finalizado
        $period = \App\Models\Period::find($periodId);
        if ($period && $period->is_finalized) {
            $results['errors'][] = "El periodo está finalizado y no permite modificaciones.";
            return $results;
        }

        // Verificar si es multigrado
        $assignedGrade = $assignment->grade;
        $isMultigrade = $assignedGrade->type === 'multi';

        // Obtener estudiantes matriculados - mapear por identificación normalizada
        if ($isMultigrade && $assignedGrade->min_grade && $assignedGrade->max_grade) {
            // MULTIGRADO: Buscar estudiantes de todos los grados en el rango
            $gradesInRange = Grade::where('is_active', true)
                ->whereBetween('level', [$assignedGrade->min_grade, $assignedGrade->max_grade])
                ->pluck('id')
                ->toArray();

            $enrolledStudents = Enrollment::with('student')
                ->where('school_year_id', $assignment->school_year_id)
                ->where('sede_id', $assignment->sede_id)
                ->whereIn('grade_id', $gradesInRange)
                ->where('status', 'active')
                ->get()
                ->mapWithKeys(function ($enrollment) {
                    $id = $this->normalizeIdentification($enrollment->student->identification);
                    return [$id => $enrollment->student_id];
                });
        } else {
            // GRADO ÚNICO: Comportamiento normal
            $enrolledStudents = Enrollment::with('student')
                ->where('school_year_id', $assignment->school_year_id)
                ->where('sede_id', $assignment->sede_id)
                ->where('grade_id', $assignment->grade_id)
                ->where('status', 'active')
                ->get()
                ->mapWithKeys(function ($enrollment) {
                    $id = $this->normalizeIdentification($enrollment->student->identification);
                    return [$id => $enrollment->student_id];
                });
        }

        $reader = new Reader();
        $reader->open($filePath);

        $rowNumber = 0;
        $hasGradeColumn = null; // Detectar automáticamente

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rowNumber++;
                
                $cells = $row->toArray();
                
                // Si no hay suficientes celdas, saltar
                if (count($cells) < 8) {
                    continue;
                }

                // Detectar si tiene columna de GRADO (en fila de encabezados)
                if ($hasGradeColumn === null && $rowNumber <= 10) {
                    $cellsStr = array_map(fn($c) => strtoupper(trim((string)$c)), $cells);
                    if (in_array('GRADO', $cellsStr)) {
                        $hasGradeColumn = true;
                        \Log::info("Import Excel - Detectada columna GRADO");
                    } elseif (in_array('TAREAS', $cellsStr)) {
                        // Si encontramos TAREAS pero no GRADO, no tiene columna grado
                        $hasGradeColumn = false;
                        \Log::info("Import Excel - Sin columna GRADO");
                    }
                }

                // Obtener primer celda como string
                $firstCell = trim((string)($cells[0] ?? ''));
                
                // Verificar que la primera columna sea un número (N° del estudiante)
                if (!is_numeric($firstCell) || intval($firstCell) < 1) {
                    continue;
                }

                // Determinar índices de columnas según si tiene columna GRADO
                // Si no se detectó aún, asumir basándose en si es multigrado
                if ($hasGradeColumn === null) {
                    $hasGradeColumn = $isMultigrade;
                }

                // Columnas: N°(0), Identificación(1), Nombre(2), [Grado(3)], Tareas, Evaluaciones, Autoeval, Comportamiento, Inasistencias
                $offset = $hasGradeColumn ? 1 : 0;

                // Normalizar la identificación del Excel
                $rawIdentification = $cells[1] ?? '';
                $identification = $this->normalizeIdentification($rawIdentification);
                $name = trim((string)($cells[2] ?? ''));
                
                // Si la identificación está vacía, saltar
                if (empty($identification)) {
                    continue;
                }

                // Verificar si el estudiante está matriculado
                if (!isset($enrolledStudents[$identification])) {
                    $results['errors'][] = "Fila {$rowNumber}: Estudiante con identificación '{$identification}' no encontrado en la matrícula actual.";
                    $results['skipped']++;
                    continue;
                }

                $studentId = $enrolledStudents[$identification];

                // Obtener valores (ajustados por offset si tiene columna GRADO)
                $tasksScore = $this->parseScore($cells[3 + $offset] ?? null);
                $evaluationsScore = $this->parseScore($cells[4 + $offset] ?? null);
                $selfScore = $this->parseScore($cells[5 + $offset] ?? null);
                $behavior = $this->parseBehavior($cells[6 + $offset] ?? null);
                $absences = $this->parseAbsences($cells[7 + $offset] ?? null);
                
                \Log::info("Import Excel - Fila {$rowNumber}", [
                    'identification' => $identification,
                    'has_grade_column' => $hasGradeColumn,
                    'offset' => $offset,
                    'raw_cells' => array_map(fn($c) => (string)$c, array_slice($cells, 0, 10)),
                    'parsed' => [
                        'tasks' => $tasksScore,
                        'evaluations' => $evaluationsScore,
                        'self' => $selfScore,
                        'behavior' => $behavior,
                        'absences' => $absences,
                    ]
                ]);

                // Validaciones
                $rowErrors = [];

                if ($tasksScore !== null && ($tasksScore < 1.0 || $tasksScore > 5.0)) {
                    $rowErrors[] = "Tareas debe estar entre 1.0 y 5.0 (valor: {$tasksScore})";
                }

                if ($evaluationsScore !== null && ($evaluationsScore < 1.0 || $evaluationsScore > 5.0)) {
                    $rowErrors[] = "Evaluaciones debe estar entre 1.0 y 5.0 (valor: {$evaluationsScore})";
                }

                if ($selfScore !== null && ($selfScore < 1.0 || $selfScore > 5.0)) {
                    $rowErrors[] = "Autoevaluación debe estar entre 1.0 y 5.0 (valor: {$selfScore})";
                }

                $rawBehavior = trim((string)($cells[6 + $offset] ?? ''));
                if (!empty($rawBehavior) && $behavior === null) {
                    $rowErrors[] = "Comportamiento inválido: '{$rawBehavior}' (usar: BAJO, BASICO, ALTO, SUPERIOR)";
                }

                if ($absences < 0) {
                    $rowErrors[] = "Inasistencias debe ser >= 0";
                    $absences = 0;
                }

                if (!empty($rowErrors)) {
                    $results['errors'][] = "Fila {$rowNumber} ({$name}): " . implode(', ', $rowErrors);
                }

                // Guardar si tiene al menos un valor válido
                if ($tasksScore !== null || $evaluationsScore !== null || $selfScore !== null || $behavior !== null || $absences > 0) {
                    try {
                        StudentGrade::updateOrCreate(
                            [
                                'teaching_assignment_id' => $assignment->id,
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
                        $results['success']++;
                    } catch (\Exception $e) {
                        $results['errors'][] = "Fila {$rowNumber}: Error al guardar - " . $e->getMessage();
                    }
                } else {
                    $results['skipped']++;
                }
            }
            break; // Solo procesar la primera hoja
        }

        $reader->close();

        return $results;
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
     * Normalizar identificación para comparación
     * Maneja valores que vienen como float desde Excel (ej: 12345.0 -> 12345)
     */
    protected function normalizeIdentification($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        
        // Convertir a string
        $value = (string)$value;
        
        // Si es un número con decimales .0, quitarlos
        if (preg_match('/^\d+\.0+$/', $value)) {
            $value = preg_replace('/\.0+$/', '', $value);
        }
        
        // Eliminar espacios y convertir a mayúsculas para comparación uniforme
        $value = trim(strtoupper(str_replace(' ', '', $value)));
        
        return $value;
    }
}
