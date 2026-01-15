<?php

namespace App\Livewire;

use App\Models\Period;
use App\Models\SchoolSetting;
use App\Models\SchoolYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TopbarInfo extends Component
{
    public string $panelType = 'admin';
    
    public function mount(string $panelType = 'admin')
    {
        $this->panelType = $panelType;
    }
    
    public function getUserProperty()
    {
        return Auth::user();
    }
    
    public function getActivePeriodProperty()
    {
        return Period::with('schoolYear')
            ->where('is_active', true)
            ->first();
    }
    
    public function getSchoolLogoProperty()
    {
        $logo = SchoolSetting::get('school_logo');
        return $logo ? asset('storage/' . $logo) : null;
    }
    
    public function getSchoolNameProperty()
    {
        return SchoolSetting::get('school_name', 'NOTAS 2.0');
    }
    
    public function getDaysRemainingProperty()
    {
        $period = $this->activePeriod;
        
        if (!$period || !$period->end_date) {
            return null;
        }
        
        $endDate = Carbon::parse($period->end_date);
        $now = Carbon::now();
        
        if ($now->gt($endDate)) {
            return 0;
        }
        
        return $now->diffInDays($endDate);
    }
    
    public function getTimeRemainingProperty()
    {
        $period = $this->activePeriod;
        
        if (!$period || !$period->end_date) {
            return null;
        }
        
        $endDate = Carbon::parse($period->end_date)->endOfDay();
        $now = Carbon::now();
        
        if ($now->gt($endDate)) {
            return [
                'days' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'expired' => true,
            ];
        }
        
        $totalSeconds = $now->diffInSeconds($endDate);
        $days = floor($totalSeconds / 86400);
        $hours = floor(($totalSeconds % 86400) / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        
        return [
            'days' => (int) $days,
            'hours' => (int) $hours,
            'minutes' => (int) $minutes,
            'seconds' => (int) $seconds,
            'expired' => false,
        ];
    }
    
    public function getRoleDisplayProperty()
    {
        return match($this->panelType) {
            'admin' => 'Administrador',
            'teacher' => 'Profesor',
            'student' => 'Estudiante',
            default => 'Usuario',
        };
    }
    
    public function getRoleColorProperty()
    {
        return match($this->panelType) {
            'admin' => 'blue',
            'teacher' => 'emerald',
            'student' => 'purple',
            default => 'gray',
        };
    }
    
    public function render()
    {
        return view('livewire.topbar-info');
    }
}
