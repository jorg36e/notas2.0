<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Seeder;

class GradesSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['name' => 'Preescolar', 'code' => 'PRE', 'level' => 0, 'description' => 'Preescolar'],
            ['name' => 'Primero', 'code' => 'PRO', 'level' => 1, 'description' => 'Primaria'],
            ['name' => 'Segundo', 'code' => 'SGU', 'level' => 2, 'description' => 'Primaria'],
            ['name' => 'Tercero', 'code' => 'TER', 'level' => 3, 'description' => 'Primaria'],
            ['name' => 'Cuarto', 'code' => 'CUA', 'level' => 4, 'description' => 'Primaria'],
            ['name' => 'Quinto', 'code' => 'QUI', 'level' => 5, 'description' => 'Primaria'],
            ['name' => 'Sexto', 'code' => 'SEX', 'level' => 6, 'description' => 'Secundaria'],
            ['name' => 'Séptimo', 'code' => 'SEP', 'level' => 7, 'description' => 'Secundaria'],
            ['name' => 'Octavo', 'code' => 'OCT', 'level' => 8, 'description' => 'Secundaria'],
            ['name' => 'Noveno', 'code' => 'NOV', 'level' => 9, 'description' => 'Secundaria'],
            ['name' => 'Décimo', 'code' => 'DEC', 'level' => 10, 'description' => 'Media'],
            ['name' => 'Undécimo', 'code' => 'UND', 'level' => 11, 'description' => 'Media'],
        ];

        $created = 0;
        $updated = 0;
        foreach ($grades as $gradeData) {
            $grade = Grade::where('name', $gradeData['name'])->first();
            if (!$grade) {
                Grade::create($gradeData);
                $created++;
                $this->command->info("✓ Creado: {$gradeData['name']} ({$gradeData['code']}) - {$gradeData['description']}");
            } else {
                $grade->update($gradeData);
                $updated++;
                $this->command->warn("↻ Actualizado: {$gradeData['name']} ({$gradeData['code']}) - {$gradeData['description']}");
            }
        }

        $this->command->info("Total grados creados: {$created}, actualizados: {$updated}");
    }
}
