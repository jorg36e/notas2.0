<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Configuración
$schoolYearId = 1; // 2026
$sedeBuenosAires = 2; // Buenos Aires
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

// Lista de estudiantes de Buenos Aires a crear y matricular
$studentsToCreate = [
    // Cuarto → Quinto (Buenos Aires)
    ['name' => 'Alan David Calvo Saboya', 'identification' => '1073707499', 'to' => 'Quinto', 'sede' => $sedeBuenosAires],
    ['name' => 'Jeinni Yulianis Vanegas Garcia', 'identification' => '1110118587', 'to' => 'Quinto', 'sede' => $sedeBuenosAires],
    // Quinto → Sexto (Santa Ana)
    ['name' => 'Jhohan Darley Garcia Peralta', 'identification' => '1075600122', 'to' => 'Sexto', 'sede' => $sedeSantaAna],
    // Segundo → Tercero (Buenos Aires)
    ['name' => 'Kimberly Stefania Gaitan Tarquino', 'identification' => '1233502831', 'to' => 'Tercero', 'sede' => $sedeBuenosAires],
    // Tercero → Cuarto (Buenos Aires)
    ['name' => 'Yoselinn Sharith Gaitan Tarquino', 'identification' => '1070924043', 'to' => 'Cuarto', 'sede' => $sedeBuenosAires],
];

// Estudiantes de Preescolar Santa Ana que faltaban
$studentsPreescolar = [
    ['name' => 'Breiner Alejandro Bolaños Garcia', 'identification' => '1075601133', 'to' => 'Primero', 'sede' => $sedeSantaAna],
    ['name' => 'Adrian Javela Oliveros', 'identification' => '1077250744', 'to' => 'Primero', 'sede' => $sedeSantaAna],
    ['name' => 'Arly Sureya Quintero Castro', 'identification' => '1033826818', 'to' => 'Primero', 'sede' => $sedeSantaAna],
    ['name' => 'Angel David Quintero Garzon', 'identification' => '1110118913', 'to' => 'Primero', 'sede' => $sedeSantaAna],
];

$allStudents = array_merge($studentsToCreate, $studentsPreescolar);

echo "===========================================\n";
echo "   CREAR ESTUDIANTES Y MATRICULAR\n";
echo "   Año 2026\n";
echo "===========================================\n\n";

$created = 0;
$enrolled = 0;
$skipped = 0;

foreach ($allStudents as $data) {
    // Verificar si ya existe
    $student = User::where('identification', $data['identification'])->first();
    
    if (!$student) {
        // Crear el estudiante
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
            'password' => Hash::make($data['identification']), // Contraseña = documento
        ]);
        
        echo "✓ CREADO: " . $student->name . " (Doc: " . $data['identification'] . ")\n";
        $created++;
    } else {
        echo "⚠ YA EXISTE: " . $student->name . "\n";
    }
    
    // Verificar si ya está matriculado
    $existingEnrollment = Enrollment::where('student_id', $student->id)
                                     ->where('school_year_id', $schoolYearId)
                                     ->first();
    
    if ($existingEnrollment) {
        echo "  → Ya matriculado en " . $existingEnrollment->grade->name . "\n";
        $skipped++;
        continue;
    }
    
    // Crear matrícula
    $sedeNombre = $data['sede'] == $sedeSantaAna ? 'Santa Ana' : 'Buenos Aires';
    
    try {
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
    } catch (Exception $e) {
        echo "  → ERROR matrícula: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "===========================================\n";
echo "   RESUMEN\n";
echo "===========================================\n";
echo "✓ Estudiantes creados: " . $created . "\n";
echo "✓ Matrículas realizadas: " . $enrolled . "\n";
echo "⊘ Omitidos: " . $skipped . "\n";
echo "===========================================\n";
