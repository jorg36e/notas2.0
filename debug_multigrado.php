<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\TeachingAssignment;
use App\Models\Enrollment;
use App\Models\User;

echo "=== PROFESORES CON ASIGNACIONES MULTIGRADO ===\n";

$activeYear = SchoolYear::where('is_active', true)->first();
echo "Año escolar activo: {$activeYear->name}\n\n";

// Buscar profesores con asignaciones multigrado
$multigradeAssignments = TeachingAssignment::where('school_year_id', $activeYear->id)
    ->where('is_active', true)
    ->whereHas('grade', function($query) {
        $query->where('type', 'multi');
    })
    ->with(['teacher', 'sede', 'grade'])
    ->get()
    ->unique('teacher_id');

echo "Profesores con asignaciones multigrado:\n";
foreach ($multigradeAssignments as $assignment) {
    $grade = $assignment->grade;
    $sede = $assignment->sede;
    $teacher = $assignment->teacher;
    
    // Obtener grados en rango
    $gradesInRange = Grade::where('is_active', true)
        ->whereBetween('level', [$grade->min_grade, $grade->max_grade])
        ->pluck('id')
        ->toArray();
    
    // Contar estudiantes
    $studentsCount = Enrollment::where('school_year_id', $activeYear->id)
        ->where('sede_id', $sede->id)
        ->where('status', 'active')
        ->whereIn('grade_id', $gradesInRange)
        ->count();
    
    echo "  - {$teacher->name}\n";
    echo "    Email: {$teacher->email}\n";
    echo "    Sede: {$sede->name}\n";
    echo "    Grado: {$grade->name} (nivel {$grade->min_grade} a {$grade->max_grade})\n";
    echo "    Estudiantes: {$studentsCount}\n\n";
}

echo "\n=== RESUMEN ===\n";
echo "Total profesores multigrado: " . $multigradeAssignments->count() . "\n";
