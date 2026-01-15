<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GRADOS DISPONIBLES ===\n";
$grades = App\Models\Grade::select('id', 'name', 'level')->orderBy('level')->get();
foreach($grades as $g) {
    echo $g->id . ' | ' . $g->name . ' | Level: ' . $g->level . "\n";
}

echo "\n=== AÑOS ESCOLARES ===\n";
$years = App\Models\SchoolYear::all();
foreach($years as $y) {
    echo $y->id . ' | ' . $y->name . ' | Activo: ' . ($y->is_active ? 'SI' : 'NO') . "\n";
}

echo "\n=== SEDES ===\n";
$sedes = App\Models\Sede::all();
foreach($sedes as $s) {
    echo $s->id . ' | ' . $s->name . "\n";
}

echo "\n=== ESTUDIANTES CON SUS MATRÍCULAS ACTUALES ===\n";
$students = App\Models\User::where('role', 'student')
    ->with(['enrollments' => function($q) {
        $q->with(['grade', 'schoolYear'])->latest();
    }])
    ->orderBy('name')
    ->get();

foreach($students as $s) {
    $lastEnrollment = $s->enrollments->first();
    $gradeName = $lastEnrollment ? $lastEnrollment->grade->name : 'Sin matrícula';
    $yearName = $lastEnrollment ? $lastEnrollment->schoolYear->name : '-';
    echo $s->id . ' | ' . $s->name . ' | ' . $gradeName . ' | Año: ' . $yearName . "\n";
}
