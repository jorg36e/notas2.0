<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedePalacio = 11; // Palacio
$sedeSantaAna = 1; // Santa Ana

// IDs de grados
$gradeIds = [
    'Segundo' => 4,
    'Tercero' => 5,
    'Cuarto' => 6,
    'Quinto' => 7,
    'Sexto' => 8,
];

// Estudiantes de Palacio
$students = [
    // Cuarto → Quinto (Palacio)
    ['name' => 'Alexandra Leyva Niño', 'identification' => '1188971249', 'to' => 'Quinto', 'sede' => $sedePalacio],
    ['name' => 'Karol Liceth Oviedo Castro', 'identification' => '1075600737', 'to' => 'Quinto', 'sede' => $sedePalacio],
    ['name' => 'Luis Alfredo Mayorga Garcia', 'identification' => '1075600693', 'to' => 'Quinto', 'sede' => $sedePalacio],
    
    // Primero → Segundo (Palacio)
    ['name' => 'Iam Arley Arevalo Niño', 'identification' => '1075323871', 'to' => 'Segundo', 'sede' => $sedePalacio],
    
    // Quinto → Sexto (Santa Ana)
    ['name' => 'Dilan Miguel Garcia Garcia', 'identification' => '1075600477', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    ['name' => 'Marly Sofia Garcia Garcia', 'identification' => '1075600492', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    
    // Segundo → Tercero (Palacio)
    ['name' => 'Alann Julian Manrique Garcia', 'identification' => '1023038022', 'to' => 'Tercero', 'sede' => $sedePalacio],
    ['name' => 'Matias Diaz Garcia', 'identification' => '1141359952', 'to' => 'Tercero', 'sede' => $sedePalacio],
    
    // Tercero → Cuarto (Palacio)
    ['name' => 'Fabian Andres Castro Ramirez', 'identification' => '1033812869', 'to' => 'Cuarto', 'sede' => $sedePalacio],
    ['name' => 'Liceth Samara Cubillos Ramirez', 'identification' => '1075600798', 'to' => 'Cuarto', 'sede' => $sedePalacio],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - PALACIO\n";
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
    
    $sedeNombre = $data['sede'] == $sedeSantaAna ? 'Santa Ana' : 'Palacio';
    
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
