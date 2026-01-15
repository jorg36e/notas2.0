<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, color, boolean, json
            $table->string('group')->default('general'); // general, appearance, contact, social
            $table->timestamps();
        });

        // Insertar configuraciones por defecto
        $defaults = [
            // General
            ['key' => 'school_name', 'value' => 'Institución Educativa', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_slogan', 'value' => 'Educación de calidad para todos', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_logo', 'value' => null, 'type' => 'image', 'group' => 'general'],
            ['key' => 'school_favicon', 'value' => null, 'type' => 'image', 'group' => 'general'],
            ['key' => 'school_nit', 'value' => '', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_dane', 'value' => '', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_resolution', 'value' => '', 'type' => 'text', 'group' => 'general'],
            
            // Contacto
            ['key' => 'school_address', 'value' => '', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'school_phone', 'value' => '', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'school_email', 'value' => '', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'school_website', 'value' => '', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'school_city', 'value' => '', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'school_department', 'value' => '', 'type' => 'text', 'group' => 'contact'],
            
            // Apariencia
            ['key' => 'primary_color', 'value' => '#3b82f6', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'secondary_color', 'value' => '#10b981', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'accent_color', 'value' => '#f59e0b', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'danger_color', 'value' => '#ef4444', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'dark_mode', 'value' => 'system', 'type' => 'text', 'group' => 'appearance'], // light, dark, system
            ['key' => 'sidebar_collapsed', 'value' => 'false', 'type' => 'boolean', 'group' => 'appearance'],
            
            // Redes sociales
            ['key' => 'social_facebook', 'value' => '', 'type' => 'text', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => '', 'type' => 'text', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => '', 'type' => 'text', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => '', 'type' => 'text', 'group' => 'social'],
        ];

        foreach ($defaults as $setting) {
            DB::table('school_settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'type' => $setting['type'],
                'group' => $setting['group'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
