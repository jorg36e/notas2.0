<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\Grade;

echo "=== RESTAURACIÓN DE MATRÍCULAS 2026 ===\n\n";

// Obtener año escolar 2026
$schoolYear = SchoolYear::where('name', '2026')->first();
if (!$schoolYear) {
    echo "ERROR: No existe el año escolar 2026\n";
    exit;
}
echo "Año escolar: {$schoolYear->name} (ID: {$schoolYear->id})\n\n";

// Obtener sedes
$sedes = Sede::all()->keyBy('name');
echo "Sedes:\n";
foreach($sedes as $name => $sede) {
    echo "  - {$sede->id}: {$name}\n";
}

// Mapa de direcciones a sedes (basado en el campo address del estudiante)
$addressToSede = [
    'Santa Ana' => 1,
    'Buenos Aires' => 2,
    'El Amparo' => 3,
    'La Cabaña' => 4,
    'La Esperanza' => 5,
    'La Florida' => 6,
    'La Granja' => 7,
    'La Inmaculada' => 8,
    'La Sonora' => 9,
    'Nueva Granada' => 10,
    'Palacio' => 11,
    'San Emilio' => 12,
    'San Isidro' => 14,
    'San Marcos' => 15,
    'San Rafael' => 16,
    'Santa Elena' => 17,
];

// Obtener todos los grados
$grades = Grade::all()->keyBy('name');

// Obtener estudiantes
$students = User::where('role', 'student')->get();
echo "\nEstudiantes totales: " . $students->count() . "\n";

// Verificar direcciones únicas
$addresses = $students->pluck('address')->unique();
echo "\nDirecciones encontradas:\n";
foreach($addresses as $addr) {
    echo "  - '{$addr}'\n";
}

// Contador
$created = 0;
$skipped = 0;
$errors = 0;

// Asignar un grado por defecto (Sexto - ID 8) para todos los estudiantes
// Ya que no tenemos información del grado anterior
$defaultGradeId = 8; // Sexto

echo "\n\nProcesando estudiantes...\n";

foreach($students as $student) {
    // Verificar si ya tiene matrícula
    $existing = Enrollment::where('student_id', $student->id)
        ->where('school_year_id', $schoolYear->id)
        ->first();
        
    if ($existing) {
        $skipped++;
        continue;
    }
    
    // Determinar sede basada en address
    $sedeId = 1; // Default: Santa Ana
    $address = trim($student->address ?? '');
    
    foreach($addressToSede as $sedeName => $sId) {
        if (stripos($address, $sedeName) !== false) {
            $sedeId = $sId;
            break;
        }
    }
    
    try {
        Enrollment::create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_id' => $defaultGradeId,
            'sede_id' => $sedeId,
            'status' => 'active',
        ]);
        $created++;
        echo ".";
    } catch (\Exception $e) {
        $errors++;
        echo "X({$e->getMessage()})";
    }
}

echo "\n\n=== RESUMEN ===\n";
echo "Matrículas creadas: {$created}\n";
echo "Omitidas (ya existían): {$skipped}\n";
echo "Errores: {$errors}\n";

// Mostrar distribución por sede
echo "\n=== Distribución por Sede ===\n";
$enrollmentsBySede = Enrollment::where('school_year_id', $schoolYear->id)
    ->selectRaw('sede_id, count(*) as total')
    ->groupBy('sede_id')
    ->get();

foreach($enrollmentsBySede as $row) {
    $sede = Sede::find($row->sede_id);
    echo "  - {$sede->name}: {$row->total} estudiantes\n";
}

$total = Enrollment::where('school_year_id', $schoolYear->id)->count();
echo "\nTOTAL de matrículas: {$total}\n";
