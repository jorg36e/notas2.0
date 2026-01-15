<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            'Ciencias Económicas y Políticas',
            'Ciencias Naturales y Educación Ambiental',
            'Ciencias Sociales',
            'Constitución Política',
            'Dimensión Cognitiva',
            'Dimensión Comunicativa',
            'Dimensión Corporal',
            'Dimensión Espiritual',
            'Dimensión Estética',
            'Dimensión Ética',
            'Dimensión Socio Afectiva',
            'Economía',
            'Educación Artística',
            'Educación Ética y Valores Humanos',
            'Educación Física Recreación y Deportes',
            'Educación Religiosa',
            'Filosofía',
            'Física',
            'Idioma Extranjero Inglés',
            'Lengua Castellana',
            'Matemáticas',
            'Química',
            'Tecnología e Informática',
        ];

        $created = 0;
        $updated = 0;
        foreach ($subjects as $name) {
            $subject = Subject::where('name', $name)->first();
            if (!$subject) {
                Subject::create(['name' => $name, 'is_active' => true]);
                $created++;
                $this->command->info("✓ Creada: {$name}");
            } else {
                $updated++;
                $this->command->warn("↻ Ya existe: {$name}");
            }
        }

        $this->command->info("Total: {$created} creadas, {$updated} ya existían");
    }
}
