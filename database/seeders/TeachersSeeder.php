<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeachersSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            ['name' => 'Manuel Fernando Adarme Moncayo', 'identification' => '1084223018', 'phone' => '3138063626', 'email' => 'manuel.adarme@sedhuila.edu.co'],
            ['name' => 'Carlos Eduardo Ardila Bermeo', 'identification' => '7692937', 'phone' => '3104003813', 'email' => 'pacheco62@hotmail.com'],
            ['name' => 'María Margarita Argote Guerrero', 'identification' => '27277968', 'phone' => '3103886539', 'email' => 'margaritaguererro80@yahoo.com'],
            ['name' => 'Willans Cangrego Díaz', 'identification' => '4899661', 'phone' => '3204942927', 'email' => 'wicady@hotmail.com'],
            ['name' => 'Julian Camilo Chaux Alvarez', 'identification' => '1075274321', 'phone' => '3106854075', 'email' => 'julianchaux93@gmail.com'],
            ['name' => 'Irma Divey Duran Reyes', 'identification' => '28699253', 'phone' => '3123615849', 'email' => 'diveydu16@hotmail.com'],
            ['name' => 'Flor Alba Firigua Preciado', 'identification' => '26480086', 'phone' => '3143579544', 'email' => 'FlorAlba@gmail.com'],
            ['name' => 'Idalí Florez Garzón', 'identification' => '26477572', 'phone' => '3138720887', 'email' => 'ifloga21@hotmail.com'],
            ['name' => 'Arinda Garcia Mora', 'identification' => '36174538', 'phone' => '3132625565', 'email' => 'arindagarcia-03@hotmail.com'],
            ['name' => 'Luz Dary Godoy Peña', 'identification' => '55161473', 'phone' => '3144475198', 'email' => 'luzdarygodoy@hotmail.com'],
            ['name' => 'Nury Maryerly Hoyos Hoyos', 'identification' => '1083867848', 'phone' => '3117791524', 'email' => 'numayerhoy@gmail.com'],
            ['name' => 'Fredy Omar Ibarra Sachez', 'identification' => '1094532880', 'phone' => '3134138239', 'email' => 'ibarrafredy2160@gmail.com'],
            ['name' => 'Stella Javela Firigua', 'identification' => '26480078', 'phone' => '3142912106', 'email' => 'adrianajavela@gmail.com'],
            ['name' => 'Aurora Matta Bastidas', 'identification' => '36167715', 'phone' => '3123457284', 'email' => 'aumattba@hotmail.com'],
            ['name' => 'Jesus Alexis Matta Bastidas', 'identification' => '80255506', 'phone' => '3124746176', 'email' => 'jesus.matta@sedhuila.edu.co'],
            ['name' => 'Monica Ortigoza Cangrego', 'identification' => '1075270751', 'phone' => '3142942487', 'email' => 'monica08.1@hotmail.com'],
            ['name' => 'Teresita Ortigoza Cangrego', 'identification' => '55151843', 'phone' => '3102207313', 'email' => 'tereortigoza@hotmail.com'],
            ['name' => 'Patricia Parga Torres', 'identification' => '52121867', 'phone' => '3143414181', 'email' => 'patriciaparga@hotmail.es'],
            ['name' => 'Ruby Jadyd Rojas Sánchez', 'identification' => '51698934', 'phone' => '3133503442', 'email' => 'rujarosa@hotmail.com'],
            ['name' => 'Pedro José Sanchez Javela', 'identification' => '4899509', 'phone' => '3142697881', 'email' => 'pjsanchez@hotmail.com'],
            ['name' => 'Israel Solorzano Salas', 'identification' => '7687894', 'phone' => '3174130172', 'email' => 'israel.solorzano@unad.edu.co'],
            ['name' => 'María Viela Lozano', 'identification' => '28696999', 'phone' => '3134660569', 'email' => 'profe@gmail.com'],
            ['name' => 'Marisol Viveros Serna', 'identification' => '1061805375', 'phone' => '3157959744', 'email' => 'marisolviverosserna@gmail.com'],
            ['name' => 'Jorge Ali Yanez Soto', 'identification' => '1067915948', 'phone' => '3022679917', 'email' => 'jorgealiyanez@gmail.com'],
        ];

        // Obtener las identificaciones válidas
        $validIdentifications = collect($teachers)->pluck('identification')->toArray();

        // Eliminar profesores que ya no están en la lista (excepto admin)
        $deleted = User::where('role', 'teacher')
            ->whereNotIn('identification', $validIdentifications)
            ->delete();
        
        if ($deleted > 0) {
            $this->command->error("✗ Eliminados {$deleted} profesores que ya no están en la lista");
        }

        $created = 0;
        $updated = 0;
        foreach ($teachers as $teacher) {
            $user = User::where('identification', $teacher['identification'])->first();
            if (!$user) {
                User::create([
                    'name' => $teacher['name'],
                    'identification' => $teacher['identification'],
                    'phone' => $teacher['phone'],
                    'email' => $teacher['email'],
                    'password' => Hash::make($teacher['identification']),
                    'role' => 'teacher',
                ]);
                $created++;
                $this->command->info("✓ Creado: {$teacher['name']} - {$teacher['email']}");
            } else {
                $user->update([
                    'name' => $teacher['name'],
                    'phone' => $teacher['phone'],
                    'email' => $teacher['email'],
                    'password' => Hash::make($teacher['identification']),
                    'role' => 'teacher',
                ]);
                $updated++;
                $this->command->warn("↻ Actualizado: {$teacher['name']} - {$teacher['email']}");
            }
        }

        $this->command->info("Total: {$created} creados, {$updated} actualizados");
    }
}
