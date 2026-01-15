<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'teaching_assignment_id',
        'student_id',
        'period_id',
        'tasks_score',
        'evaluations_score',
        'self_score',
        'final_score',
        'observations',
        'behavior',
        'absences',
    ];

    protected $casts = [
        'tasks_score' => 'decimal:1',
        'evaluations_score' => 'decimal:1',
        'self_score' => 'decimal:1',
        'final_score' => 'decimal:1',
        'absences' => 'integer',
    ];

    const BEHAVIOR_OPTIONS = [
        'BAJO' => 'BAJO',
        'BASICO' => 'BASICO',
        'ALTO' => 'ALTO',
        'SUPERIOR' => 'SUPERIOR',
    ];

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    /**
     * Calcular nota final automáticamente
     * Tareas: 40%, Evaluaciones: 50%, Autoevaluación: 10%
     */
    public function calculateFinalScore(): ?float
    {
        if ($this->tasks_score === null && $this->evaluations_score === null && $this->self_score === null) {
            return null;
        }

        $tasks = $this->tasks_score ?? 0;
        $evaluations = $this->evaluations_score ?? 0;
        $self = $this->self_score ?? 0;

        return round(($tasks * 0.40) + ($evaluations * 0.50) + ($self * 0.10), 1);
    }

    /**
     * Verificar si está aprobado (>=3.0)
     */
    public function isPassing(): bool
    {
        return $this->final_score !== null && $this->final_score >= 3.0;
    }

    /**
     * Boot del modelo para calcular final_score automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($grade) {
            $grade->final_score = $grade->calculateFinalScore();
        });
    }
}
