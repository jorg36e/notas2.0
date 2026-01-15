<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'identification',
        'role',
        'sede_id',
        'phone',
        'address',
        'birth_date',
        'guardian_name',
        'guardian_phone',
        'profile_photo',
        'signature',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->role === 'admin';
        }
        
        if ($panel->getId() === 'teacher') {
            return $this->role === 'teacher';
        }
        
        if ($panel->getId() === 'student') {
            return $this->role === 'student';
        }
        
        return false;
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class, 'teacher_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}
