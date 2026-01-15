<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Grade;
use App\Models\Enrollment;
use App\Models\Sede;

echo "=== SEDES ===\n\n";
$sedes = Sede::all();
foreach ($sedes as $s) {
    echo "ID: {$s->id} | {$s->name}\n";
}

echo "\n=== MATRÍCULAS POR SEDE Y GRADO ===\n\n";

$enrollments = Enrollment::with(['grade', 'sede', 'student'])
    ->where('status', 'active')
    ->get()
    ->groupBy(['sede_id', 'grade_id']);

foreach ($enrollments as $sedeId => $gradeGroups) {
    $sedeName = Sede::find($sedeId)?->name ?? 'Sin sede';
    echo "\n--- SEDE: {$sedeName} (ID: {$sedeId}) ---\n";
    foreach ($gradeGroups as $gradeId => $students) {
        $gradeName = Grade::find($gradeId)?->name ?? 'Sin grado';
        echo "  Grado: {$gradeName} - {$students->count()} estudiantes\n";
    }
}
