<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeSanRafael = 16; // San Rafael
$sedeSantaAna = 1; // Santa Ana

// IDs de grados
$gradeIds = [
    'Primero' => 3,
    'Tercero' => 5,
    'Quinto' => 7,
    'Sexto' => 8,
];

// Estudiantes de San Rafael
$students = [
    // Preescolar → Primero
    ['name' => 'Brayan Alexander Garcia Juspian', 'identification' => '1075601130', 'to' => 'Primero', 'sede' => $sedeSanRafael],
    
    // Cuarto → Quinto
    ['name' => 'Lina Maria Bocanegra Correa', 'identification' => '1028670326', 'to' => 'Quinto', 'sede' => $sedeSanRafael],
    
    // Quinto → Sexto (Santa Ana)
    ['name' => 'Emerson David Osorio Peralta', 'identification' => '1016735170', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    
    // Segundo → Tercero
    ['name' => 'Faisury Garcia Juspian', 'identification' => '1075600961', 'to' => 'Tercero', 'sede' => $sedeSanRafael],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - SAN RAFAEL\n";
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
    
    $sedeNombre = $data['sede'] == $sedeSantaAna ? 'Santa Ana' : 'San Rafael';
    
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
