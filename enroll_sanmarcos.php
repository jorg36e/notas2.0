<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeSanMarcos = 15; // San Marcos
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

// Estudiantes de San Marcos
$students = [
    // Preescolar → Primero
    ['name' => 'Breiner Stiven Lugo Avila', 'identification' => '1075601118', 'to' => 'Primero', 'sede' => $sedeSanMarcos],
    
    // Cuarto → Quinto
    ['name' => 'María Lucia Marroquín Rodríguez', 'identification' => '1075304654', 'to' => 'Quinto', 'sede' => $sedeSanMarcos],
    ['name' => 'Milán Andrés Matta Ortigoza', 'identification' => '1073711419', 'to' => 'Quinto', 'sede' => $sedeSanMarcos],
    ['name' => 'Moisés Barrera Medina', 'identification' => '1070620834', 'to' => 'Quinto', 'sede' => $sedeSanMarcos],
    ['name' => 'Salome Trujillo Ávila', 'identification' => '1079185856', 'to' => 'Quinto', 'sede' => $sedeSanMarcos],
    ['name' => 'Sofía Matta García', 'identification' => '1075600716', 'to' => 'Quinto', 'sede' => $sedeSanMarcos],
    
    // Primero → Segundo
    ['name' => 'Maille Celeste Bahos Hernández', 'identification' => '1075324190', 'to' => 'Segundo', 'sede' => $sedeSanMarcos],
    ['name' => 'Mara Victoria Matta Bastidas', 'identification' => '1075324340', 'to' => 'Segundo', 'sede' => $sedeSanMarcos],
    ['name' => 'Maria José Gutiérrez Torres', 'identification' => '1075605065', 'to' => 'Segundo', 'sede' => $sedeSanMarcos],
    
    // Quinto → Sexto (Santa Ana)
    ['name' => 'Johan Smeth González Lozano', 'identification' => '1110118335', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    
    // Segundo → Tercero
    ['name' => 'Alejandro Guerrero Matta', 'identification' => '1075600964', 'to' => 'Tercero', 'sede' => $sedeSanMarcos],
    ['name' => 'Emmanuel Rodríguez Hernández', 'identification' => '1075600739', 'to' => 'Tercero', 'sede' => $sedeSanMarcos],
    ['name' => 'Sebastián Lugo Cardozo', 'identification' => '1075601009', 'to' => 'Tercero', 'sede' => $sedeSanMarcos],
    ['name' => 'Wilmar Lozano Cardozo', 'identification' => '1110118607', 'to' => 'Tercero', 'sede' => $sedeSanMarcos],
    
    // Tercero → Cuarto
    ['name' => 'Alejandro Matta Marroquín', 'identification' => '1075600780', 'to' => 'Cuarto', 'sede' => $sedeSanMarcos],
    ['name' => 'Ana Sofia Gaitán Suarez', 'identification' => '1075600832', 'to' => 'Cuarto', 'sede' => $sedeSanMarcos],
    ['name' => 'Francisco Cardozo Rodríguez', 'identification' => '1075600748', 'to' => 'Cuarto', 'sede' => $sedeSanMarcos],
    ['name' => 'Maria del Mar Naranjo García', 'identification' => '1077245777', 'to' => 'Cuarto', 'sede' => $sedeSanMarcos],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - SAN MARCOS\n";
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
    
    $sedeNombre = $data['sede'] == $sedeSantaAna ? 'Santa Ana' : 'San Marcos';
    
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
