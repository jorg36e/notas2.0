<?php

namespace App\Filament\Widgets;

use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\Subject;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class SystemInfoWidget extends Widget
{
    protected static string $view = 'filament.widgets.system-info-widget';
    
    protected static ?int $sort = 7;
    
    protected int | string | array $columnSpan = 'full';
    
    public function getSystemInfo(): array
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        
        return [
            'school_year' => $activeSchoolYear?->name ?? 'Sin definir',
            'total_sedes' => Sede::where('is_active', true)->count(),
            'total_grades' => Grade::where('is_active', true)->count(),
            'total_subjects' => Subject::where('is_active', true)->count(),
            'total_students' => User::where('role', 'student')->where('is_active', true)->count(),
            'total_teachers' => User::where('role', 'teacher')->where('is_active', true)->count(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_size' => $this->getDatabaseSize(),
            'last_backup' => 'No disponible',
        ];
    }
    
    private function getDatabaseSize(): string
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $result = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$dbName]);
            
            return ($result[0]->size_mb ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    
    public function getQuickStats(): array
    {
        $today = now()->toDateString();
        
        return [
            'students_today' => User::where('role', 'student')
                ->whereDate('created_at', $today)
                ->count(),
            'teachers_today' => User::where('role', 'teacher')
                ->whereDate('created_at', $today)
                ->count(),
            'active_sessions' => DB::table('sessions')->count(),
        ];
    }
}
