<?php

namespace Database\Seeders;

use App\Models\Sede;
use Illuminate\Database\Seeder;

class SedesSeeder extends Seeder
{
    public function run(): void
    {
        $sedes = [
            ['name' => 'Buenos Aires', 'code' => '24120600107812', 'address' => 'Vereda Buenos Aires', 'phone' => '3022679917'],
            ['name' => 'El Amparo', 'code' => '24120600107803', 'address' => 'Vereda El Amparo', 'phone' => '3142288800'],
            ['name' => 'La Cabaña', 'code' => '24120600107805', 'address' => 'La cabaña', 'phone' => '32434543423'],
            ['name' => 'La Esperanza', 'code' => '241206000021', 'address' => 'Vereda La Esperanza', 'phone' => '323122355'],
            ['name' => 'La Florida', 'code' => '24120600107815', 'address' => 'Vereda La Florida', 'phone' => null],
            ['name' => 'La Granja', 'code' => '24120600107808', 'address' => 'Vereda La Granja', 'phone' => null],
            ['name' => 'La Inmaculada', 'code' => '24120600107806', 'address' => 'Vereda La Inmaculada', 'phone' => null],
            ['name' => 'La Sonora', 'code' => '24120600107816', 'address' => 'Vereda La Sonora', 'phone' => null],
            ['name' => 'Nueva Granada', 'code' => '24120600107813', 'address' => 'Vereda Nueva Granada', 'phone' => null],
            ['name' => 'Palacio', 'code' => '24120600107811', 'address' => 'Vereda Palacio', 'phone' => null],
            ['name' => 'San Emilio', 'code' => '24120600107814', 'address' => 'Vereda San Emilio', 'phone' => null],
            ['name' => 'San Isidro', 'code' => '24120600107809', 'address' => 'Vereda San Isidro', 'phone' => null],
            ['name' => 'San Marcos', 'code' => '241206000063', 'address' => 'Vereda San Marcos', 'phone' => '3022679922'],
            ['name' => 'San Rafael', 'code' => '24120600107817', 'address' => 'San Rafael', 'phone' => '3117791524'],
            ['name' => 'Santa Ana', 'code' => '24120600107801', 'address' => 'Corregimiento Santa Ana', 'phone' => '3184353455'],
            ['name' => 'Santa Elena', 'code' => '24120600107802', 'address' => 'Saba', 'phone' => '3425465454'],
        ];

        $created = 0;
        $updated = 0;
        foreach ($sedes as $sedeData) {
            $sede = Sede::where('name', $sedeData['name'])->first();
            if (!$sede) {
                Sede::create($sedeData);
                $created++;
                $this->command->info("✓ Creada: {$sedeData['name']} ({$sedeData['code']})");
            } else {
                $sede->update($sedeData);
                $updated++;
                $this->command->warn("↻ Actualizada: {$sedeData['name']} ({$sedeData['code']})");
            }
        }

        $this->command->info("Total: {$created} creadas, {$updated} actualizadas");
    }
}
