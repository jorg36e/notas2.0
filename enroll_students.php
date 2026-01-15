<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;

// Configuración
$schoolYearId = 1; // 2026
$sedeId = 1; // Santa Ana

// IDs de grados
$gradeIds = [
    'Segundo' => 4,
    'Tercero' => 5,
    'Cuarto' => 6,
    'Quinto' => 7,
    'Sexto' => 8,
];

// Lista de estudiantes a matricular con su nuevo grado
$studentsToEnroll = [
    // Cuarto A → Quinto
    ['name' => 'Jhojan Darley Hernandez Garcia', 'newGrade' => 'Quinto'],
    ['name' => 'Yesid Quintero Garzon', 'newGrade' => 'Quinto'],
    
    // Primero A → Segundo
    ['name' => 'Eliss Zamuel Solorzano Otaya', 'newGrade' => 'Segundo'],
    ['name' => 'Melany Sofia Diaz Peña', 'newGrade' => 'Segundo'],
    ['name' => 'Sharit Sofia Mendez Rodriguez', 'newGrade' => 'Segundo'],
    ['name' => 'Thiago David Torres Mora', 'newGrade' => 'Segundo'],
    ['name' => 'Wendy Johanna Lozano Pastor', 'newGrade' => 'Segundo'],
    
    // Quinto A → Sexto (estos probablemente no existen, se omitirán)
    ['name' => 'Alejandro Cangrejo Guerrero', 'newGrade' => 'Sexto'],
    ['name' => 'Camilo Andres Garcia Londoño', 'newGrade' => 'Sexto'],
    ['name' => 'David Santiago Torres Hurtado', 'newGrade' => 'Sexto'],
    ['name' => 'Juan Manuel Sanchez Mora', 'newGrade' => 'Sexto'],
    ['name' => 'Julian Santiago Firigua Mendez', 'newGrade' => 'Sexto'],
    ['name' => 'Leandro Hernandez Trujillo', 'newGrade' => 'Sexto'],
    ['name' => 'Mauro Alejandro Lopez Otaya', 'newGrade' => 'Sexto'],
    
    // Segundo A → Tercero
    ['name' => 'Breyner Andres Cangrejo Firigua', 'newGrade' => 'Tercero'],
    ['name' => 'Breyner Kaleth Ramirez Cardozo', 'newGrade' => 'Tercero'],
    ['name' => 'Dayana Michell Muñoz Pabon', 'newGrade' => 'Tercero'],
    ['name' => 'Luis Miguel Garcia Bastidas', 'newGrade' => 'Tercero'],
    ['name' => 'Maicol Andres Hernandez Mora', 'newGrade' => 'Tercero'],
    ['name' => 'Maria Jose Rodriguez Garcia', 'newGrade' => 'Tercero'],
    ['name' => 'Pedro Luis Firigua Lopez', 'newGrade' => 'Tercero'],
    ['name' => 'Yudi Vanessa Perez Lizcano', 'newGrade' => 'Tercero'],
    
    // Tercero A → Cuarto
    ['name' => 'Betulia Tique Bastidas', 'newGrade' => 'Cuarto'],
    ['name' => 'Hasly Michel Cortez Garcia', 'newGrade' => 'Cuarto'],
    ['name' => 'Heidy Liceth Pastor Guerrero', 'newGrade' => 'Cuarto'],
];

echo "===========================================\n";
echo "   MATRÍCULA AUTOMÁTICA - AÑO 2026\n";
echo "   Sede: Santa Ana\n";
echo "===========================================\n\n";

$enrolled = 0;
$skipped = 0;
$errors = [];

foreach ($studentsToEnroll as $studentData) {
    $student = User::where('name', $studentData['name'])
                   ->where('role', 'student')
                   ->first();
    
    if (!$student) {
        echo "⊘ OMITIDO: " . $studentData['name'] . " (no existe en BD)\n";
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
        $enrollment = Enrollment::create([
            'school_year_id' => $schoolYearId,
            'student_id' => $student->id,
            'sede_id' => $sedeId,
            'grade_id' => $gradeIds[$studentData['newGrade']],
            'enrollment_date' => now(),
            'status' => 'active',
        ]);
        
        echo "✓ MATRICULADO: " . $student->name . " → " . $studentData['newGrade'] . "\n";
        $enrolled++;
    } catch (Exception $e) {
        echo "✗ ERROR: " . $student->name . " - " . $e->getMessage() . "\n";
        $errors[] = $student->name;
    }
}

echo "\n===========================================\n";
echo "   RESUMEN\n";
echo "===========================================\n";
echo "✓ Matriculados: " . $enrolled . "\n";
echo "⊘ Omitidos: " . $skipped . "\n";
echo "✗ Errores: " . count($errors) . "\n";
echo "===========================================\n";
