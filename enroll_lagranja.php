<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeLaGranja = 7; // La Granja
$sedeSantaAna = 1; // Santa Ana

// IDs de grados
$gradeIds = [
    'Primero' => 3,
    'Cuarto' => 6,
    'Sexto' => 8,
];

// Estudiantes de La Granja
$students = [
    // Preescolar → Primero (La Granja)
    ['name' => 'Gabriela Lozano Ramirez', 'identification' => '1077251234', 'to' => 'Primero', 'sede' => $sedeLaGranja],
    
    // Quinto → Sexto (Santa Ana)
    ['name' => 'Johan Andres Garcia Montes', 'identification' => '1075600592', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    ['name' => 'Joiner Garcia Montes', 'identification' => '1029888644', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    ['name' => 'Leimar Bastidas Cardozo', 'identification' => '1077242306', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    ['name' => 'Marlon Garcia Montes', 'identification' => '1029888645', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    
    // Tercero → Cuarto (La Granja)
    ['name' => 'Sofia Diaz Gonzalez', 'identification' => '1075600791', 'to' => 'Cuarto', 'sede' => $sedeLaGranja],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - LA GRANJA\n";
echo "   Año 2026\n";
echo "===========================================\n\n";

$created = 0;
$enrolled = 0;

foreach ($students as $data) {
    $student = User::where('identification', $data['identification'])->first();
    
    if (!$student) {
        $student = User::create([
            'name' => $data['name'],
            'identification' => $data['identification'],
            'role' => 'student',
            'phone' => '3000000000',
            'address' => 'Por actualizar',
            'birth_date' => '2010-01-01',
            'guardian_name' => 'Por actualizar',
            'guardian_phone' => '3000000000',
            'is_active' => true,
            'password' => Hash::make($data['identification']),
        ]);
        
        echo "✓ CREADO: " . $student->name . " (Doc: " . $data['identification'] . ")\n";
        $created++;
    } else {
        echo "⚠ YA EXISTE: " . $student->name . "\n";
    }
    
    $existingEnrollment = Enrollment::where('student_id', $student->id)
                                     ->where('school_year_id', $schoolYearId)
                                     ->first();
    
    if ($existingEnrollment) {
        echo "  → Ya matriculado en " . $existingEnrollment->grade->name . "\n";
        continue;
    }
    
    $sedeNombre = $data['sede'] == $sedeSantaAna ? 'Santa Ana' : 'La Granja';
    
    Enrollment::create([
        'school_year_id' => $schoolYearId,
        'student_id' => $student->id,
        'sede_id' => $data['sede'],
        'grade_id' => $gradeIds[$data['to']],
        'enrollment_date' => now(),
        'status' => 'active',
    ]);
    
    echo "  → MATRICULADO: " . $data['to'] . " (" . $sedeNombre . ")\n";
    $enrolled++;
}

echo "\n===========================================\n";
echo "✓ Creados: " . $created . " | Matriculados: " . $enrolled . "\n";
echo "===========================================\n";
