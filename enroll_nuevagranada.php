<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeNuevaGranada = 10; // Nueva Granada

// IDs de grados
$gradeIds = [
    'Tercero' => 5,
    'Cuarto' => 6,
    'Quinto' => 7,
];

// Estudiantes de Nueva Granada
$students = [
    // Cuarto → Quinto
    ['name' => 'Dana Julieth Jara Guerrero', 'identification' => '1028878106', 'to' => 'Quinto'],
    ['name' => 'Danna Michel Bohorquez Gomez', 'identification' => '1070332359', 'to' => 'Quinto'],
    ['name' => 'Sara Valentina Gomez Castro', 'identification' => '1016094822', 'to' => 'Quinto'],
    
    // Segundo → Tercero
    ['name' => 'Luis Daniel Bohorquez Gomez', 'identification' => '1074825215', 'to' => 'Tercero'],
    
    // Tercero → Cuarto
    ['name' => 'Joelin Dahian Chaguala Martinez', 'identification' => '1105476130', 'to' => 'Cuarto'],
    ['name' => 'Juan David Garcia Niño', 'identification' => '1110118621', 'to' => 'Cuarto'],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - NUEVA GRANADA\n";
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
        'sede_id' => $sedeNuevaGranada,
        'grade_id' => $gradeIds[$data['to']],
        'enrollment_date' => now(),
        'status' => 'active',
    ]);
    
    echo "  → MATRICULADO: " . $data['to'] . " (Nueva Granada)\n";
    $enrolled++;
}

echo "\n===========================================\n";
echo "✓ Creados: " . $created . " | Matriculados: " . $enrolled . "\n";
echo "===========================================\n";
