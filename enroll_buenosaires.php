<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;

// Configuración
$schoolYearId = 1; // 2026
$sedeBuenosAires = 2; // Buenos Aires
$sedeSantaAna = 1; // Santa Ana

// IDs de grados
$gradeIds = [
    'Segundo' => 4,
    'Tercero' => 5,
    'Cuarto' => 6,
    'Quinto' => 7,
    'Sexto' => 8,
];

// Lista de estudiantes con su grado actual y nuevo grado
// Si viene de Quinto, va a Sexto en Santa Ana
$studentsToEnroll = [
    ['name' => 'Alan David Calvo Saboya', 'from' => 'Cuarto', 'to' => 'Quinto', 'sede' => $sedeBuenosAires],
    ['name' => 'Jeinni Yulianis Vanegas Garcia', 'from' => 'Cuarto', 'to' => 'Quinto', 'sede' => $sedeBuenosAires],
    ['name' => 'Jhohan Darley Garcia Peralta', 'from' => 'Quinto', 'to' => 'Sexto', 'sede' => $sedeSantaAna], // Va a Santa Ana
    ['name' => 'Kimberly Stefania Gaitan Tarquino', 'from' => 'Segundo', 'to' => 'Tercero', 'sede' => $sedeBuenosAires],
    ['name' => 'Yoselinn Sharith Gaitan Tarquino', 'from' => 'Tercero', 'to' => 'Cuarto', 'sede' => $sedeBuenosAires],
];

echo "===========================================\n";
echo "   MATRÍCULA AUTOMÁTICA - AÑO 2026\n";
echo "   Grupo Multigrado - Buenos Aires\n";
echo "===========================================\n\n";

$enrolled = 0;
$skipped = 0;

foreach ($studentsToEnroll as $data) {
    $student = User::where('name', $data['name'])
                   ->where('role', 'student')
                   ->first();
    
    if (!$student) {
        echo "⊘ OMITIDO: " . $data['name'] . " (no existe en BD)\n";
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
    
    // Determinar sede (Quinto→Sexto va a Santa Ana)
    $sedeNombre = $data['sede'] == $sedeSantaAna ? 'Santa Ana' : 'Buenos Aires';
    
    // Crear la matrícula
    try {
        Enrollment::create([
            'school_year_id' => $schoolYearId,
            'student_id' => $student->id,
            'sede_id' => $data['sede'],
            'grade_id' => $gradeIds[$data['to']],
            'enrollment_date' => now(),
            'status' => 'active',
        ]);
        
        echo "✓ MATRICULADO: " . $student->name . " → " . $data['to'] . " (" . $sedeNombre . ")\n";
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
