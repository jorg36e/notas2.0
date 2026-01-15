<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeLaFlorida = 6; // La Florida
$sedeSantaAna = 1; // Santa Ana

// IDs de grados
$gradeIds = [
    'Primero' => 3,
    'Segundo' => 4,
    'Tercero' => 5,
    'Cuarto' => 6,
    'Quinto' => 7,
    'Sexto' => 8,
];

// Estudiantes de La Florida
$students = [
    // Preescolar → Primero (La Florida)
    ['name' => 'Mauricio Morales Gomez', 'identification' => '1075601135', 'to' => 'Primero', 'sede' => $sedeLaFlorida],
    
    // Cuarto → Quinto (La Florida)
    ['name' => 'Deuris Alexander Firigua Matta', 'identification' => '1075600583', 'to' => 'Quinto', 'sede' => $sedeLaFlorida],
    ['name' => 'Sara Valentina Joya Viassus', 'identification' => '1074577229', 'to' => 'Quinto', 'sede' => $sedeLaFlorida],
    
    // Primero → Segundo (La Florida)
    ['name' => 'Dayron David Celeita Ramirez', 'identification' => '1075601040', 'to' => 'Segundo', 'sede' => $sedeLaFlorida],
    ['name' => 'Emanuel Santiago Quitumbo Romero', 'identification' => '1075601069', 'to' => 'Segundo', 'sede' => $sedeLaFlorida],
    
    // Quinto → Sexto (Santa Ana)
    ['name' => 'Alejandro Enciso Torres', 'identification' => '1023939099', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    ['name' => 'Lina Maryuri Hernandez Chala', 'identification' => '1076600539', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    
    // Segundo → Tercero (La Florida)
    ['name' => 'Anyerson Leandro Viassus Viasus', 'identification' => '1075600918', 'to' => 'Tercero', 'sede' => $sedeLaFlorida],
    ['name' => 'Evelyn Sofía Méndez Gaona', 'identification' => '1074823846', 'to' => 'Tercero', 'sede' => $sedeLaFlorida],
    ['name' => 'Herminson Arley Enciso Torres', 'identification' => '1023978435', 'to' => 'Tercero', 'sede' => $sedeLaFlorida],
    
    // Tercero → Cuarto (La Florida)
    ['name' => 'Andry Gisett Celeita Romero', 'identification' => '1075600848', 'to' => 'Cuarto', 'sede' => $sedeLaFlorida],
    ['name' => 'Wendy Yulieth Celeita Torres', 'identification' => '1013682571', 'to' => 'Cuarto', 'sede' => $sedeLaFlorida],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - LA FLORIDA\n";
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
    
    $sedeNombre = $data['sede'] == $sedeSantaAna ? 'Santa Ana' : 'La Florida';
    
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
