<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\Enrollment;
use App\Models\TransferRequest;

$activeYear = SchoolYear::where('is_active', true)->first();
$santaElena = Sede::where('name', 'like', '%Santa Elena%')->first();

echo "Año activo: " . ($activeYear ? $activeYear->id . " ({$activeYear->year})" : 'No encontrado') . "\n";
echo "Santa Elena ID: " . ($santaElena ? $santaElena->id . " ({$santaElena->name})" : 'No encontrado') . "\n\n";

if ($activeYear && $santaElena) {
    // Sin el whereDoesntHave
    echo "=== SIN whereDoesntHave ===\n";
    $enrollments = Enrollment::where('school_year_id', $activeYear->id)
        ->where('sede_id', $santaElena->id)
        ->where('status', 'active')
        ->with(['student', 'grade'])
        ->get();
    
    echo "Enrollments encontrados: " . $enrollments->count() . "\n";
    
    // Con whereDoesntHave
    echo "\n=== CON whereDoesntHave ===\n";
    try {
        $enrollments2 = Enrollment::where('school_year_id', $activeYear->id)
            ->where('sede_id', $santaElena->id)
            ->where('status', 'active')
            ->whereDoesntHave('transferRequests', function ($q) {
                $q->where('status', TransferRequest::STATUS_PENDING);
            })
            ->with(['student', 'grade'])
            ->get();
        
        echo "Enrollments encontrados: " . $enrollments2->count() . "\n";
        foreach ($enrollments2 as $e) {
            echo "- [{$e->student_id}] {$e->student->name} ({$e->grade->name})\n";
        }
    } catch (\Exception $ex) {
        echo "Error: " . $ex->getMessage() . "\n";
    }
}
