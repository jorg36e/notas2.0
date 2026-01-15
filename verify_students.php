<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;

// Año escolar activo (2026)
$schoolYear = SchoolYear::where('is_active', true)->first();
echo "Año escolar activo: " . $schoolYear->name . " (ID: " . $schoolYear->id . ")\n\n";

// Mapeo de grados actuales al siguiente grado
$gradeMapping = [
    'Primero' => 'Segundo',
    'Segundo' => 'Tercero', 
    'Tercero' => 'Cuarto',
    'Cuarto' => 'Quinto',
    'Quinto' => 'Sexto',
];

// Obtener IDs de grados
$grades = Grade::pluck('id', 'name')->toArray();
echo "Grados disponibles:\n";
print_r($grades);

// Lista de estudiantes a matricular (del grado anterior al nuevo)
$studentsToEnroll = [
    // Cuarto A → Quinto (ID: 7)
    ['name' => 'Jhojan Darley Hernandez Garcia', 'from' => 'Cuarto', 'to' => 'Quinto'],
    ['name' => 'Yesid Quintero Garzon', 'from' => 'Cuarto', 'to' => 'Quinto'],
    
    // Primero A → Segundo (ID: 4)
    ['name' => 'Eliss Zamuel Solorzano Otaya', 'from' => 'Primero', 'to' => 'Segundo'],
    ['name' => 'Melany Sofia Diaz Peña', 'from' => 'Primero', 'to' => 'Segundo'],
    ['name' => 'Sharit Sofia Mendez Rodriguez', 'from' => 'Primero', 'to' => 'Segundo'],
    ['name' => 'Thiago David Torres Mora', 'from' => 'Primero', 'to' => 'Segundo'],
    ['name' => 'Wendy Johanna Lozano Pastor', 'from' => 'Primero', 'to' => 'Segundo'],
    
    // Quinto A → Sexto (ID: 8)
    ['name' => 'Alejandro Cangrejo Guerrero', 'from' => 'Quinto', 'to' => 'Sexto'],
    ['name' => 'Camilo Andres Garcia Londoño', 'from' => 'Quinto', 'to' => 'Sexto'],
    ['name' => 'David Santiago Torres Hurtado', 'from' => 'Quinto', 'to' => 'Sexto'],
    ['name' => 'Juan Manuel Sanchez Mora', 'from' => 'Quinto', 'to' => 'Sexto'],
    ['name' => 'Julian Santiago Firigua Mendez', 'from' => 'Quinto', 'to' => 'Sexto'],
    ['name' => 'Leandro Hernandez Trujillo', 'from' => 'Quinto', 'to' => 'Sexto'],
    ['name' => 'Mauro Alejandro Lopez Otaya', 'from' => 'Quinto', 'to' => 'Sexto'],
    
    // Segundo A → Tercero (ID: 5)
    ['name' => 'Breyner Andres Cangrejo Firigua', 'from' => 'Segundo', 'to' => 'Tercero'],
    ['name' => 'Breyner Kaleth Ramirez Cardozo', 'from' => 'Segundo', 'to' => 'Tercero'],
    ['name' => 'Dayana Michell Muñoz Pabon', 'from' => 'Segundo', 'to' => 'Tercero'],
    ['name' => 'Luis Miguel Garcia Bastidas', 'from' => 'Segundo', 'to' => 'Tercero'],
    ['name' => 'Maicol Andres Hernandez Mora', 'from' => 'Segundo', 'to' => 'Tercero'],
    ['name' => 'Maria Jose Rodriguez Garcia', 'from' => 'Segundo', 'to' => 'Tercero'],
    ['name' => 'Pedro Luis Firigua Lopez', 'from' => 'Segundo', 'to' => 'Tercero'],
    ['name' => 'Yudi Vanessa Perez Lizcano', 'from' => 'Segundo', 'to' => 'Tercero'],
    
    // Tercero A → Cuarto (ID: 6)
    ['name' => 'Betulia Tique Bastidas', 'from' => 'Tercero', 'to' => 'Cuarto'],
    ['name' => 'Hasly Michel Cortez Garcia', 'from' => 'Tercero', 'to' => 'Cuarto'],
    ['name' => 'Heidy Liceth Pastor Guerrero', 'from' => 'Tercero', 'to' => 'Cuarto'],
];

// Necesitamos saber la sede - Asumo que es una sede específica
// Primero busquemos si hay alguna matrícula anterior para estos estudiantes
echo "\n=== VERIFICANDO ESTUDIANTES ===\n";

$sedeId = null; // Necesitamos determinar la sede

foreach ($studentsToEnroll as $studentData) {
    $student = User::where('name', 'LIKE', '%' . $studentData['name'] . '%')
                   ->where('role', 'student')
                   ->first();
    
    if ($student) {
        echo "✓ Encontrado: " . $student->name . " (ID: " . $student->id . ")\n";
        
        // Buscar matrícula anterior para obtener la sede
        $prevEnrollment = Enrollment::where('student_id', $student->id)->first();
        if ($prevEnrollment && !$sedeId) {
            $sedeId = $prevEnrollment->sede_id;
            echo "  → Sede encontrada: ID " . $sedeId . "\n";
        }
    } else {
        echo "✗ NO encontrado: " . $studentData['name'] . "\n";
    }
}

echo "\n¿Sede a usar? Si no se encontró, especifica manualmente el ID de la sede.\n";
