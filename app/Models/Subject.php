<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sedes()
    {
        return $this->belongsToMany(Sede::class, 'subject_sede')
            ->withTimestamps();
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class);
    }
}
