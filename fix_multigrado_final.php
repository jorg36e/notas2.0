<?php

/**
 * Script para corregir el grado Multigrado Primaria
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Grade;
use App\Models\TeachingAssignment;

echo "=== CORRIGIENDO GRADO MULTIGRADO PRIMARIA ===\n\n";

// Corregir Multigrado Primaria
$multigrado = Grade::where('name', 'Multigrado Primaria')->first();

if ($multigrado) {
    $multigrado->type = 'multi';
    $multigrado->min_grade = 1;
    $multigrado->max_grade = 5;
    $multigrado->save();
    echo "✅ Grado 'Multigrado Primaria' corregido a type='multi'\n";
} else {
    echo "❌ No se encontró el grado 'Multigrado Primaria'\n";
}

// Verificar estado final
echo "\n=== ESTADO FINAL DE GRADOS ===\n";
$grades = Grade::orderBy('level')->get();
foreach ($grades as $g) {
    $typeLabel = $g->type === 'multi' ? '🔶 MULTI' : '⚪ SINGLE';
    $range = $g->type === 'multi' ? " (rango: {$g->min_grade}-{$g->max_grade})" : '';
    echo "  {$g->id}. {$g->name} - {$typeLabel}{$range} - level: {$g->level}\n";
}

// Ahora actualizar las asignaciones de La Inmaculada al grado Multigrado Primaria
echo "\n=== ACTUALIZANDO ASIGNACIONES DE LA INMACULADA ===\n";

// La Inmaculada es sede ID 8
$sedeInmaculada = 8;
$multigradoId = $multigrado->id;

// Obtener grados de primaria (1-5)
$gradesPrimaria = Grade::whereIn('level', [1, 2, 3, 4, 5])
    ->where('type', 'single')
    ->pluck('id')
    ->toArray();

echo "Grados de primaria (single): " . implode(', ', $gradesPrimaria) . "\n";
echo "Grado Multigrado Primaria ID: {$multigradoId}\n\n";

// Buscar asignaciones actuales
$assignmentsInmaculada = TeachingAssignment::where('sede_id', $sedeInmaculada)
    ->whereIn('grade_id', $gradesPrimaria)
    ->with(['teacher', 'subject', 'grade'])
    ->get();

echo "Asignaciones encontradas en La Inmaculada para grados 1-5:\n";
foreach ($assignmentsInmaculada as $a) {
    echo "  - Profesor: {$a->teacher->name} | Materia: {$a->subject->name} | Grado actual: {$a->grade->name}\n";
}

// Agrupar por profesor y materia para actualizar a multigrado
$grouped = $assignmentsInmaculada->groupBy(function ($item) {
    return "{$item->teacher_id}-{$item->subject_id}";
});

echo "\n>>> Actualizando a Multigrado Primaria <<<\n";
$updated = 0;
$deleted = 0;

foreach ($grouped as $key => $group) {
    $first = $group->first();
    
    // Verificar si ya existe asignación multigrado
    $existsMulti = TeachingAssignment::where('sede_id', $sedeInmaculada)
        ->where('teacher_id', $first->teacher_id)
        ->where('subject_id', $first->subject_id)
        ->where('grade_id', $multigradoId)
        ->exists();
    
    if (!$existsMulti) {
        // Actualizar la primera a multigrado
        $first->grade_id = $multigradoId;
        $first->save();
        $updated++;
        echo "  ✅ {$first->teacher->name} - {$first->subject->name} → Multigrado Primaria\n";
    }
    
    // Eliminar duplicados
    foreach ($group->skip(1) as $dup) {
        $dup->delete();
        $deleted++;
    }
}

echo "\n📊 Resumen: {$updated} asignaciones actualizadas, {$deleted} duplicados eliminados\n";

// Verificar resultado final
echo "\n=== ASIGNACIONES FINALES EN LA INMACULADA ===\n";
$finalAssignments = TeachingAssignment::where('sede_id', $sedeInmaculada)
    ->with(['teacher', 'subject', 'grade'])
    ->get();

foreach ($finalAssignments as $a) {
    $badge = $a->grade->type === 'multi' ? '🔶' : '⚪';
    echo "  {$badge} {$a->teacher->name} | {$a->subject->name} | {$a->grade->name}\n";
}
