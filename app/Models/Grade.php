<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'level',
        'min_grade',
        'max_grade',
        'description',
        'is_active',
        'director_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
        'min_grade' => 'integer',
        'max_grade' => 'integer',
    ];

    /**
     * Director de grupo (profesor asignado)
     */
    public function director()
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class);
    }
}
