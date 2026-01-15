<?php

/**
 * Script para reorganizar la estructura de grados multigrado
 * 
 * ANTES: Primero, Segundo, Tercero, Cuarto, Quinto todos como 'multi' con mismo rango
 * DESPUÉS: 
 *   - Primero-Quinto como 'single' (para matrículas)
 *   - Nuevo grado "Multigrado Primaria" como 'multi' (para asignaciones docentes)
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Grade;
use App\Models\TeachingAssignment;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "  REORGANIZACIÓN DE ESTRUCTURA MULTIGRADO\n";
echo "===========================================\n\n";

// Mostrar estado actual
echo ">>> ESTADO ACTUAL DE GRADOS <<<\n";
$grades = Grade::orderBy('level')->get();
foreach ($grades as $g) {
    $typeLabel = $g->type === 'multi' ? '🔶 MULTI' : '⚪ SINGLE';
    $range = $g->type === 'multi' ? " (rango: {$g->min_grade}-{$g->max_grade})" : '';
    echo "  {$g->id}. {$g->name} - {$typeLabel}{$range} - level: {$g->level}\n";
}

echo "\n>>> EJECUTANDO REORGANIZACIÓN... <<<\n";

DB::beginTransaction();

try {
    echo "\n>>> PASO 1: Convertir grados 1-5 a 'single' <<<\n";
    
    $gradesToConvert = Grade::whereIn('level', [1, 2, 3, 4, 5])->get();
    foreach ($gradesToConvert as $grade) {
        if ($grade->type === 'multi') {
            $grade->type = 'single';
            $grade->min_grade = null;
            $grade->max_grade = null;
            $grade->save();
            echo "  ✅ {$grade->name} convertido a 'single'\n";
        } else {
            echo "  ⏭️ {$grade->name} ya es 'single'\n";
        }
    }

    echo "\n>>> PASO 2: Crear grado 'Multigrado Primaria' <<<\n";
    
    // Verificar si ya existe
    $multigradeExists = Grade::where('name', 'Multigrado Primaria')->first();
    
    if ($multigradeExists) {
        echo "  ⚠️ El grado 'Multigrado Primaria' ya existe (ID: {$multigradeExists->id})\n";
        $multigradePrimaria = $multigradeExists;
    } else {
        $multigradePrimaria = Grade::create([
            'name' => 'Multigrado Primaria',
            'code' => 'MULTI-PRIM',
            'type' => 'multi',
            'level' => 1, // Se considera nivel 1 para ordenamiento
            'min_grade' => 1,
            'max_grade' => 5,
            'description' => 'Grupo multigrado que incluye estudiantes de Primero a Quinto',
            'is_active' => true,
        ]);
        echo "  ✅ Grado 'Multigrado Primaria' creado (ID: {$multigradePrimaria->id})\n";
    }

    echo "\n>>> PASO 3: Actualizar asignaciones docentes multigrado <<<\n";
    
    // Buscar asignaciones que estaban en grados 1-5 y moverlas al nuevo multigrado
    $gradesIds = Grade::whereIn('level', [1, 2, 3, 4, 5])
        ->where('id', '!=', $multigradePrimaria->id)
        ->pluck('id')
        ->toArray();
    
    // Obtener sedes que tienen configuración multigrado (por ahora todas las que tienen asignaciones en estos grados)
    $assignmentsToUpdate = TeachingAssignment::whereIn('grade_id', $gradesIds)->get();
    
    // Agrupar por sede, año escolar y asignatura para evitar duplicados
    $groupedAssignments = $assignmentsToUpdate->groupBy(function ($item) {
        return "{$item->school_year_id}-{$item->sede_id}-{$item->subject_id}-{$item->teacher_id}";
    });
    
    $updated = 0;
    $deleted = 0;
    
    foreach ($groupedAssignments as $key => $group) {
        // Tomar la primera asignación y actualizarla al grado multigrado
        $firstAssignment = $group->first();
        
        // Verificar si ya existe una asignación para este multigrado
        $existingMultigrade = TeachingAssignment::where('school_year_id', $firstAssignment->school_year_id)
            ->where('sede_id', $firstAssignment->sede_id)
            ->where('subject_id', $firstAssignment->subject_id)
            ->where('teacher_id', $firstAssignment->teacher_id)
            ->where('grade_id', $multigradePrimaria->id)
            ->first();
        
        if (!$existingMultigrade) {
            $firstAssignment->grade_id = $multigradePrimaria->id;
            $firstAssignment->save();
            $updated++;
            echo "  ✅ Asignación actualizada: Profesor #{$firstAssignment->teacher_id} - Materia #{$firstAssignment->subject_id} - Sede #{$firstAssignment->sede_id}\n";
        }
        
        // Eliminar las demás asignaciones del grupo (duplicados)
        foreach ($group->skip(1) as $duplicate) {
            $duplicate->delete();
            $deleted++;
        }
    }
    
    echo "\n  📊 Resumen: {$updated} asignaciones actualizadas, {$deleted} duplicados eliminados\n";

    DB::commit();
    
    echo "\n>>> ESTADO FINAL DE GRADOS <<<\n";
    $grades = Grade::orderBy('level')->get();
    foreach ($grades as $g) {
        $typeLabel = $g->type === 'multi' ? '🔶 MULTI' : '⚪ SINGLE';
        $range = $g->type === 'multi' ? " (rango: {$g->min_grade}-{$g->max_grade})" : '';
        echo "  {$g->id}. {$g->name} - {$typeLabel}{$range} - level: {$g->level}\n";
    }
    
    echo "\n✅ ¡Reorganización completada exitosamente!\n";
    echo "\n📋 PRÓXIMOS PASOS:\n";
    echo "   1. Las asignaciones docentes multigrado ahora usan 'Multigrado Primaria'\n";
    echo "   2. Los estudiantes permanecen matriculados en su grado real (Primero, Segundo, etc.)\n";
    echo "   3. Al calificar 'Multigrado Primaria', el profesor verá estudiantes de 1° a 5°\n";
    echo "   4. Para grados unigrado (6°-11°), las asignaciones se mantienen igual\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Se ha revertido la transacción.\n";
}
