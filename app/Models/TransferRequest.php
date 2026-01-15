<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'enrollment_id',
        'origin_sede_id',
        'destination_sede_id',
        'origin_grade_id',
        'destination_grade_id',
        'school_year_id',
        'requested_by',
        'approved_by',
        'status',
        'type',
        'reason',
        'rejection_reason',
        'notes',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    // Constantes de estado
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // Constantes de tipo
    const TYPE_DIRECT = 'direct';
    const TYPE_REQUEST = 'request';

    /**
     * Estudiante a trasladar
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Matrícula actual
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Sede de origen
     */
    public function originSede()
    {
        return $this->belongsTo(Sede::class, 'origin_sede_id');
    }

    /**
     * Sede de destino
     */
    public function destinationSede()
    {
        return $this->belongsTo(Sede::class, 'destination_sede_id');
    }

    /**
     * Grado de origen
     */
    public function originGrade()
    {
        return $this->belongsTo(Grade::class, 'origin_grade_id');
    }

    /**
     * Grado de destino
     */
    public function destinationGrade()
    {
        return $this->belongsTo(Grade::class, 'destination_grade_id');
    }

    /**
     * Año escolar
     */
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * Usuario que solicitó
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Usuario que aprobó
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope para pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope para aprobados
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope por sede origen
     */
    public function scopeFromSede($query, $sedeId)
    {
        return $query->where('origin_sede_id', $sedeId);
    }

    /**
     * Scope por sede destino
     */
    public function scopeToSede($query, $sedeId)
    {
        return $query->where('destination_sede_id', $sedeId);
    }

    /**
     * Verificar si está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Verificar si fue aprobado
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Verificar si fue rechazado
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Obtener etiqueta de estado
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_APPROVED => 'Aprobado',
            self::STATUS_REJECTED => 'Rechazado',
            self::STATUS_CANCELLED => 'Cancelado',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener color del estado
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'gray',
            default => 'gray',
        };
    }
}
