<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeLaEsperanza = 5; // La Esperanza
$sedeSantaAna = 1; // Santa Ana

// IDs de grados
$gradeIds = [
    'Primero' => 3,
    'Segundo' => 4,
    'Cuarto' => 6,
    'Sexto' => 8,
];

// Estudiantes de La Esperanza
$students = [
    // Preescolar → Primero (La Esperanza)
    ['name' => 'Dylan Camilo Guerrero Rosales', 'identification' => '1075809300', 'to' => 'Primero', 'sede' => $sedeLaEsperanza],
    ['name' => 'Yeiler Alfredo Ramirez Quintero', 'identification' => '1077737960', 'to' => 'Primero', 'sede' => $sedeLaEsperanza],
    
    // Primero → Segundo (La Esperanza)
    ['name' => 'Danna Sofia Serrato Rodriguez', 'identification' => '1075601033', 'to' => 'Segundo', 'sede' => $sedeLaEsperanza],
    ['name' => 'Darwin Guerrero Perez', 'identification' => '1081184201', 'to' => 'Segundo', 'sede' => $sedeLaEsperanza],
    ['name' => 'Laura Camila Guerrero Pérez', 'identification' => '1081183634', 'to' => 'Segundo', 'sede' => $sedeLaEsperanza],
    
    // Quinto → Sexto (Santa Ana)
    ['name' => 'Linda Valentina Guerrero Benavides', 'identification' => '1075600487', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    
    // Tercero → Cuarto (La Esperanza)
    ['name' => 'Brayan Jose Benavides Moncaleano', 'identification' => '1075600877', 'to' => 'Cuarto', 'sede' => $sedeLaEsperanza],
    ['name' => 'Jeidy Alexsandra Aldana Roman', 'identification' => '1215967679', 'to' => 'Cuarto', 'sede' => $sedeLaEsperanza],
];

echo "===========================================\n";
echo "   CREAR/MATRICULAR - LA ESPERANZA\n";
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
    
    $sedeNombre = $data['sede'] == $sedeSantaAna ? 'Santa Ana' : 'La Esperanza';
    
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
