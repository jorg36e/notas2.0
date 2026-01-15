<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_year_id',
        'number',
        'name',
        'start_date',
        'end_date',
        'is_active',
        'is_finalized',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_finalized' => 'boolean',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function studentGrades()
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * Verificar si el periodo permite edición
     */
    public function isEditable(): bool
    {
        return $this->is_active && !$this->is_finalized;
    }
}
