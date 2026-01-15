<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SchoolSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * Obtener un valor de configuración
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Establecer un valor de configuración
     */
    public static function set(string $key, $value, string $type = 'text', string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
        
        Cache::forget("setting_{$key}");
        Cache::forget('all_settings');
    }

    /**
     * Obtener todas las configuraciones como array
     */
    public static function getAllSettings(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Obtener configuraciones por grupo
     */
    public static function getByGroup(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->toArray();
    }

    /**
     * Limpiar toda la caché de configuraciones
     */
    public static function clearCache(): void
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
        Cache::forget('all_settings');
    }

    /**
     * Obtener la URL del logo
     */
    public static function getLogoUrl(): ?string
    {
        $logo = static::get('school_logo');
        if ($logo) {
            return asset('storage/' . $logo);
        }
        return null;
    }

    /**
     * Obtener el nombre del colegio
     */
    public static function getSchoolName(): string
    {
        return static::get('school_name', 'NOTAS 2.0');
    }
}
