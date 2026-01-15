<?php

namespace Database\Seeders;

use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class SchoolSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSettings = [
            // General
            ['key' => 'school_name', 'value' => 'NOTAS 2.0', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_slogan', 'value' => 'Sistema de Gestión Académica', 'type' => 'text', 'group' => 'general'],
            
            // Appearance
            ['key' => 'primary_color', 'value' => '#3b82f6', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'secondary_color', 'value' => '#10b981', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'accent_color', 'value' => '#f59e0b', 'type' => 'color', 'group' => 'appearance'],
        ];

        foreach ($defaultSettings as $setting) {
            SchoolSetting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'group' => $setting['group'],
                ]
            );
        }

        $this->command->info('✅ Configuración inicial del colegio creada!');
    }
}
