<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeLaSonora = 9; // La Sonora

// IDs de grados
$gradeIds = [
    'Primero' => 3,
    'Tercero' => 5,
    'Quinto' => 7,
];

// Estudiantes de La Sonora
$students = [
    // Preescolar → Primero
    ['name' => 'Didier Sneider Celeita Torres', 'identification' => '1075601143', 'to' => 'Primero'],
    ['name' => 'Junior Arley Pastor Ramirez', 'identification' => '1075601166', 'to' => 'Primero'],
    
    // Cuarto → Quinto
    ['name' => 'Ivan Camilo Celeita Parra', 'identification' => '1141133946', 'to' => 'Quinto'],
    
    // Segundo → Tercero
    ['name' => 'Dilan Alejandro Pastor Ramirez', 'identification' => '1029293072', 'to' => 'Tercero'],
    ['name' => 'Yoban Andrey Celeita Celeita', 'identification' => '1069231335', 'to' => 'Tercero'],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - LA SONORA\n";
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
        'sede_id' => $sedeLaSonora,
        'grade_id' => $gradeIds[$data['to']],
        'enrollment_date' => now(),
        'status' => 'active',
    ]);
    
    echo "  → MATRICULADO: " . $data['to'] . " (La Sonora)\n";
    $enrolled++;
}

echo "\n===========================================\n";
echo "✓ Creados: " . $created . " | Matriculados: " . $enrolled . "\n";
echo "===========================================\n";
