<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;

// Configuración
$schoolYearId = 1; // 2026
$sedeId = 1; // Santa Ana
$gradeId = 13; // Undécimo

// Lista de estudiantes de Décimo A → Undécimo
$studentsToEnroll = [
    'Karol Tatiana Alape Garcia',
    'Andres Santiago Cangrejo Tique',
    'Jhojan Fermin Cardozo Duran',
    'Danni Arley Celeita Parra',
    'Ana Maria Diaz Lozano',
    'Steveen Alejandro Duran Amador',
    'Diego Alejandro Firigua Lopez',
    'Yeison Elieser Garcia Benavides',
    'Jhon Fener Guerrero Leyton',
    'Yuliana Guerrero Ortigoza',
    'Camilo Andres Matta Cardozo',
    'Santiago Matta Parga',
    'Jeison Andres Mayorga Garcia',
    'Maria Jose Peña Buitrago',
    'Maria Lucia Sanchez Lozano',
    'Carlos Tique Bastidas',
    'Yury Melisa Ortigoza Matta',
];

echo "===========================================\n";
echo "   MATRÍCULA AUTOMÁTICA - AÑO 2026\n";
echo "   Décimo A → Undécimo\n";
echo "   Sede: Santa Ana\n";
echo "===========================================\n\n";

$enrolled = 0;
$skipped = 0;

foreach ($studentsToEnroll as $name) {
    $student = User::where('name', $name)
                   ->where('role', 'student')
                   ->first();
    
    if (!$student) {
        echo "⊘ OMITIDO: " . $name . " (no existe en BD)\n";
        $skipped++;
        continue;
    }
    
    // Verificar si ya está matriculado en este año escolar
    $existingEnrollment = Enrollment::where('student_id', $student->id)
                                     ->where('school_year_id', $schoolYearId)
                                     ->first();
    
    if ($existingEnrollment) {
        echo "⚠ YA MATRICULADO: " . $student->name . " en " . $existingEnrollment->grade->name . "\n";
        $skipped++;
        continue;
    }
    
    // Crear la matrícula
    try {
        Enrollment::create([
            'school_year_id' => $schoolYearId,
            'student_id' => $student->id,
            'sede_id' => $sedeId,
            'grade_id' => $gradeId,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);
        
        echo "✓ MATRICULADO: " . $student->name . " → Undécimo\n";
        $enrolled++;
    } catch (Exception $e) {
        echo "✗ ERROR: " . $student->name . " - " . $e->getMessage() . "\n";
    }
}

echo "\n===========================================\n";
echo "   RESUMEN\n";
echo "===========================================\n";
echo "✓ Matriculados: " . $enrolled . "\n";
echo "⊘ Omitidos/Ya matriculados: " . $skipped . "\n";
echo "===========================================\n";
