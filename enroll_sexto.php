<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;

// Configuración
$schoolYearId = 1; // 2026
$sedeId = 1; // Santa Ana
$gradeId = 9; // Séptimo

// Lista de estudiantes de Sexto A → Séptimo
$studentsToEnroll = [
    'Gabriel Bastidas Firigua',
    'Laura Valentina Cangrejo Garcia',
    'Jeremy Alejandro Celeita Romero',
    'Erick Diaz Duran',
    'Alexis Diaz Gonzalez',
    'Juan Jose Diaz Sanchez',
    'Nikoll Sofia Firigua Pastor',
    'Leidy Jhoana Guerrero Leyton',
    'Valery Javela Diaz',
    'Ashly Mariana Matta Ortigoza',
    'Kevin Andres Mora Ortigoza',
    'Kevin Adrian Oliveros Peralta',
    'Jefferson Arley Riaño Ortigoza',
    'Nicolas Rojas Bocanegra',
    'Santiago Rojas Bocanegra',
    'Jeison Adrian Serrato Serrato',
    'Ingry Tatiana Torres Muñoz',
    'Paula Andrea Bastidas Bastidas',
    'Diana Marcela Torres Lozano',
    'Zanny Alexandra Lozano Sanchez',
];

echo "===========================================\n";
echo "   MATRÍCULA AUTOMÁTICA - AÑO 2026\n";
echo "   Sexto A → Séptimo\n";
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
        
        echo "✓ MATRICULADO: " . $student->name . " → Séptimo\n";
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
