<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeLaCabana = 4; // La Cabaña
$gradeId = 3; // Primero

// Estudiantes de Preescolar La Cabaña → Primero
$students = [
    ['name' => 'Heidy Valentina Murcia Argote', 'identification' => '1075601131'],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - LA CABAÑA\n";
echo "   Preescolar → Primero\n";
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
        'sede_id' => $sedeLaCabana,
        'grade_id' => $gradeId,
        'enrollment_date' => now(),
        'status' => 'active',
    ]);
    
    echo "  → MATRICULADO: Primero (La Cabaña)\n";
    $enrolled++;
}

echo "\n===========================================\n";
echo "✓ Creados: " . $created . " | Matriculados: " . $enrolled . "\n";
echo "===========================================\n";
