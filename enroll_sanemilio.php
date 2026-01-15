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
$sedeSantaAna = 1; // Santa Ana

// IDs de grados
$gradeIds = [
    'Primero' => 3,
    'Segundo' => 4,
    'Cuarto' => 6,
    'Quinto' => 7,
    'Sexto' => 8,
];

// Estudiantes de San Emilio
$students = [
    // Cuarto → Quinto (San Emilio)
    ['name' => 'Dana Sofia Aldana Chala', 'identification' => '1075805278', 'to' => 'Quinto', 'sede' => $sedeSanEmilio],
    ['name' => 'Emily Sofia Guerrero Guerrero', 'identification' => '1072496799', 'to' => 'Quinto', 'sede' => $sedeSanEmilio],
    ['name' => 'Kevin Andres Garcia Diaz', 'identification' => '1118124018', 'to' => 'Quinto', 'sede' => $sedeSanEmilio],
    
    // Primero → Segundo (San Emilio)
    ['name' => 'Yostin David Aldana Chala', 'identification' => '1075509164', 'to' => 'Segundo', 'sede' => $sedeSanEmilio],
    
    // Quinto → Sexto (Santa Ana)
    ['name' => 'James David Garcia Garcia', 'identification' => '1075600563', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    ['name' => 'Jhon Stiven Guerrero Mora', 'identification' => '1075600601', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    ['name' => 'Maria Camila Yanquen Quevedo', 'identification' => '1013146376', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    
    // Tercero → Cuarto (San Emilio)
    ['name' => 'Avy Yuliana Castro Montero', 'identification' => '1075600866', 'to' => 'Cuarto', 'sede' => $sedeSanEmilio],
    ['name' => 'Emanuel Santiago Gomez Aviles', 'identification' => '1078778801', 'to' => 'Cuarto', 'sede' => $sedeSanEmilio],
    ['name' => 'Jhon Freider Aldana Chala', 'identification' => '1075804100', 'to' => 'Cuarto', 'sede' => $sedeSanEmilio],
    ['name' => 'Maria Fernanda Perafan Guerrero', 'identification' => '1010763907', 'to' => 'Cuarto', 'sede' => $sedeSanEmilio],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - SAN EMILIO\n";
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
    
    $sedeNombre = $data['sede'] == $sedeSantaAna ? 'Santa Ana' : 'San Emilio';
    
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
echo "\n⚠ NOTA: El estudiante de Preescolar 'Zarith Yulieth Villa Guerrero'\n";
echo "   no tiene documento visible. Proporciona el documento para crearlo.\n";
