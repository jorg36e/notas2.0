<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Buscar estudiantes que podrían coincidir con los no encontrados
$searchTerms = [
    'Alejandro Cangrejo',
    'Camilo Andres Garcia',
    'David Santiago Torres',
    'Juan Manuel Sanchez',
    'Julian Santiago Firigua',
    'Leandro Hernandez',
    'Mauro Alejandro Lopez',
];

echo "=== BUSCANDO ESTUDIANTES FALTANTES ===\n\n";

foreach ($searchTerms as $term) {
    echo "Buscando: '$term'\n";
    $students = User::where('role', 'student')
                    ->where(function($q) use ($term) {
                        $parts = explode(' ', $term);
                        foreach ($parts as $part) {
                            $q->where('name', 'LIKE', '%' . $part . '%');
                        }
                    })
                    ->get();
    
    if ($students->count() > 0) {
        foreach ($students as $s) {
            echo "  → " . $s->id . " | " . $s->name . "\n";
        }
    } else {
        echo "  → No encontrado\n";
    }
    echo "\n";
}

// También mostrar todos los estudiantes que tienen "Cangrejo" en el nombre
echo "=== TODOS LOS ESTUDIANTES CON 'Cangrejo' ===\n";
User::where('role', 'student')->where('name', 'LIKE', '%Cangrejo%')->get()->each(function($s) {
    echo $s->id . " | " . $s->name . "\n";
});

echo "\n=== TODOS LOS ESTUDIANTES CON 'Garcia' Y 'Londoño' ===\n";
User::where('role', 'student')
    ->where('name', 'LIKE', '%Garcia%')
    ->get()->each(function($s) {
    echo $s->id . " | " . $s->name . "\n";
});
