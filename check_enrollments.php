<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Enrollment;
use App\Models\Sede;
use App\Models\Grade;

echo "=== RESUMEN DE MATRÍCULAS 2026 ===\n\n";

// Total por sede
echo "POR SEDE:\n";
$bySede = Enrollment::where('school_year_id', 1)
    ->selectRaw('sede_id, count(*) as total')
    ->groupBy('sede_id')
    ->get();

foreach($bySede as $row) {
    $sede = Sede::find($row->sede_id);
    echo "  - {$sede->name}: {$row->total} estudiantes\n";
}

// Total por grado
echo "\nPOR GRADO:\n";
$byGrade = Enrollment::where('school_year_id', 1)
    ->selectRaw('grade_id, count(*) as total')
    ->groupBy('grade_id')
    ->orderBy('grade_id')
    ->get();

foreach($byGrade as $row) {
    $grade = Grade::find($row->grade_id);
    echo "  - {$grade->name}: {$row->total} estudiantes\n";
}

$total = Enrollment::where('school_year_id', 1)->count();
echo "\n=================================\n";
echo "TOTAL GENERAL: {$total} estudiantes\n";
echo "=================================\n";
