<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeLaInmaculada = 8; // La Inmaculada

// IDs de grados
$gradeIds = [
    'Primero' => 3,
    'Segundo' => 4,
    'Tercero' => 5,
    'Quinto' => 7,
];

// Estudiantes de La Inmaculada
$students = [
    // Preescolar → Primero
    ['name' => 'Santiago Peña Bastidas', 'identification' => '1075809001', 'to' => 'Primero'],
    ['name' => 'Miguel Peña Lozano', 'identification' => '1075601153', 'to' => 'Primero'],
    
    // Cuarto → Quinto
    ['name' => 'Sihara Duran Martinez', 'identification' => '1011242190', 'to' => 'Quinto'],
    
    // Primero → Segundo
    ['name' => 'Juan David Lozano Torres', 'identification' => '1075600916', 'to' => 'Segundo'],
    ['name' => 'Stiven Cangrejo Villalba', 'identification' => '1075601032', 'to' => 'Segundo'],
    
    // Segundo → Tercero
    ['name' => 'Karen Liceth Lozano Lugo', 'identification' => '1075600997', 'to' => 'Tercero'],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - LA INMACULADA\n";
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
        'sede_id' => $sedeLaInmaculada,
        'grade_id' => $gradeIds[$data['to']],
        'enrollment_date' => now(),
        'status' => 'active',
    ]);
    
    echo "  → MATRICULADO: " . $data['to'] . " (La Inmaculada)\n";
    $enrolled++;
}

echo "\n===========================================\n";
echo "✓ Creados: " . $created . " | Matriculados: " . $enrolled . "\n";
echo "===========================================\n";
