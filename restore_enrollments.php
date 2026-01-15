<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\Grade;

echo "=== RESTAURACIÓN DE MATRÍCULAS 2026 ===\n\n";

// Obtener año escolar 2026
$schoolYear = SchoolYear::where('name', '2026')->orWhere('name', 'like', '%2026%')->first();
if (!$schoolYear) {
    // Si no existe, crearlo
    $schoolYear = SchoolYear::create([
        'name' => '2026',
        'is_active' => true,
    ]);
    echo "Año escolar 2026 creado.\n";
}
echo "Año escolar: {$schoolYear->name} (ID: {$schoolYear->id})\n\n";

// Obtener todas las sedes
$sedes = Sede::all();
echo "Sedes encontradas: " . $sedes->count() . "\n";
foreach($sedes as $sede) {
    echo "  - {$sede->id}: {$sede->name}\n";
}

// Obtener todos los grados
$grades = Grade::orderBy('id')->get();
echo "\nGrados encontrados: " . $grades->count() . "\n";
foreach($grades as $g) {
    echo "  - {$g->id}: {$g->name}\n";
}

// Obtener estudiantes
$students = User::where('role', 'student')->get();
echo "\nEstudiantes totales: " . $students->count() . "\n";

// Ver cuántas matrículas existen actualmente
$existingEnrollments = Enrollment::where('school_year_id', $schoolYear->id)->count();
echo "Matrículas actuales en 2026: {$existingEnrollments}\n\n";

// Preguntar si continuar
echo "¿Deseas restaurar las matrículas? (El script asignará estudiantes a sedes y grados)\n";
echo "Presiona Ctrl+C para cancelar o espera 3 segundos para continuar...\n";
sleep(3);

// Contador
$created = 0;
$skipped = 0;
$errors = 0;

// Agrupar estudiantes por sede
$studentsGrouped = $students->groupBy('sede_id');

foreach($studentsGrouped as $sedeId => $sedeStudents) {
    $sede = Sede::find($sedeId);
    $sedeName = $sede ? $sede->name : "Sin sede (ID: {$sedeId})";
    
    echo "\n--- Procesando sede: {$sedeName} ({$sedeStudents->count()} estudiantes) ---\n";
    
    foreach($sedeStudents as $student) {
        // Verificar si ya tiene matrícula en 2026
        $existing = Enrollment::where('student_id', $student->id)
            ->where('school_year_id', $schoolYear->id)
            ->first();
            
        if ($existing) {
            $skipped++;
            continue;
        }
        
        // Determinar el grado basado en algún criterio
        // Por defecto asignaremos un grado si el estudiante tiene uno asociado
        // o usaremos el grado 1 (Preescolar) como fallback
        $gradeId = $student->grade_id ?? 1;
        
        // Verificar que el grado existe
        $grade = Grade::find($gradeId);
        if (!$grade) {
            $gradeId = 1; // Fallback al primer grado
        }
        
        try {
            Enrollment::create([
                'student_id' => $student->id,
                'school_year_id' => $schoolYear->id,
                'grade_id' => $gradeId,
                'sede_id' => $sedeId ?: 1, // Si no tiene sede, asignar la primera
                'status' => 'active',
            ]);
            $created++;
            echo ".";
        } catch (\Exception $e) {
            $errors++;
            echo "X";
        }
    }
    echo "\n";
}

echo "\n\n=== RESUMEN ===\n";
echo "Matrículas creadas: {$created}\n";
echo "Omitidas (ya existían): {$skipped}\n";
echo "Errores: {$errors}\n";

// Verificar total final
$totalEnrollments = Enrollment::where('school_year_id', $schoolYear->id)->count();
echo "\nTotal de matrículas 2026: {$totalEnrollments}\n";
