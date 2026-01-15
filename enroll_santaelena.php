<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeSantaElena = 17; // Santa Elena
$sedeSantaAna = 1; // Santa Ana

// IDs de grados
$gradeIds = [
    'Segundo' => 4,
    'Tercero' => 5,
    'Cuarto' => 6,
    'Quinto' => 7,
    'Sexto' => 8,
];

// Estudiantes de Santa Elena
$students = [
    // Cuarto → Quinto
    ['name' => 'Eliana Lizeth Cangrejo Torres', 'identification' => '1075600639', 'to' => 'Quinto', 'sede' => $sedeSantaElena],
    
    // Primero → Segundo
    ['name' => 'Salome Javela Gonzalez', 'identification' => '1076922844', 'to' => 'Segundo', 'sede' => $sedeSantaElena],
    
    // Quinto → Sexto (Santa Ana)
    ['name' => 'Eduar Jose Coronado Quintero', 'identification' => '1079608042', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    ['name' => 'Liced Yuliana Firigua Hernandez', 'identification' => '1076916766', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    ['name' => 'Luiz Santiago Rosario Garcia', 'identification' => '1206217703', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    
    // Segundo → Tercero
    ['name' => 'Maria Camila Mora Garcia', 'identification' => '1106230988', 'to' => 'Tercero', 'sede' => $sedeSantaElena],
    ['name' => 'Maria Jose Bocanegra Guerrero', 'identification' => '1075600893', 'to' => 'Tercero', 'sede' => $sedeSantaElena],
    
    // Tercero → Cuarto
    ['name' => 'Alex David Firigua Guerrero', 'identification' => '1077734417', 'to' => 'Cuarto', 'sede' => $sedeSantaElena],
    ['name' => 'Diego Andres Peña Hernandez', 'identification' => '1076919822', 'to' => 'Cuarto', 'sede' => $sedeSantaElena],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - SANTA ELENA\n";
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
    
    $sedeNombre = $data['sede'] == $sedeSantaAna ? 'Santa Ana' : 'Santa Elena';
    
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
