<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeSanEmilio = 12; // San Emilio
$sedeSanIsidro = 14; // San Isidro
$sedeSantaAna = 1; // Santa Ana

// IDs de grados
$gradeIds = [
    'Primero' => 3,
    'Tercero' => 5,
    'Cuarto' => 6,
    'Sexto' => 8,
];

// Estudiantes
$students = [
    // Pendiente de San Emilio - Preescolar → Primero
    ['name' => 'Zarith Yulieth Villa Guerrero', 'identification' => '1117976716', 'to' => 'Primero', 'sede' => $sedeSanEmilio, 'sedeNombre' => 'San Emilio'],
    
    // San Isidro - Preescolar → Primero
    ['name' => 'Luciano Alessandro Tovar Ayala', 'identification' => 'N44206867981', 'to' => 'Primero', 'sede' => $sedeSanIsidro, 'sedeNombre' => 'San Isidro'],
    
    // San Isidro - Quinto → Sexto (Santa Ana)
    ['name' => 'Lina Marcela Guerrero Leyton', 'identification' => '7075600619', 'to' => 'Sexto', 'sede' => $sedeSantaAna, 'sedeNombre' => 'Santa Ana'],
    
    // San Isidro - Segundo → Tercero
    ['name' => 'Julian David Torres Lozano', 'identification' => '1075601004', 'to' => 'Tercero', 'sede' => $sedeSanIsidro, 'sedeNombre' => 'San Isidro'],
    ['name' => 'Mahira Alejandra Firigua Guzman', 'identification' => '1110118799', 'to' => 'Tercero', 'sede' => $sedeSanIsidro, 'sedeNombre' => 'San Isidro'],
    
    // San Isidro - Tercero → Cuarto
    ['name' => 'Alba Yuderly Coronado Pineda', 'identification' => '1079608354', 'to' => 'Cuarto', 'sede' => $sedeSanIsidro, 'sedeNombre' => 'San Isidro'],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - SAN ISIDRO\n";
echo "   + Pendiente San Emilio\n";
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
    
    Enrollment::create([
        'school_year_id' => $schoolYearId,
        'student_id' => $student->id,
        'sede_id' => $data['sede'],
        'grade_id' => $gradeIds[$data['to']],
        'enrollment_date' => now(),
        'status' => 'active',
    ]);
    
    echo "  → MATRICULADO: " . $data['to'] . " (" . $data['sedeNombre'] . ")\n";
    $enrolled++;
}

echo "\n===========================================\n";
echo "✓ Creados: " . $created . " | Matriculados: " . $enrolled . "\n";
echo "===========================================\n";
