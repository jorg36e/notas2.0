<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $identification = '1234567890';
        
        User::firstOrCreate(
            ['identification' => $identification],
            [
                'name' => 'Administrador',
                'identification' => $identification,
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make($identification),
            ]
        );

        $this->command->info('✅ Usuario administrador creado exitosamente!');
        $this->command->info('📧 Email: admin@notas.com');
        $this->command->info('🆔 Identificación: ' . $identification);
        $this->command->info('🔑 Contraseña: ' . $identification);
    }
}
