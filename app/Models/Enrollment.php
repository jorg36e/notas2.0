<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_year_id',
        'student_id',
        'sede_id',
        'grade_id',
        'enrollment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function transferRequests()
    {
        return $this->hasMany(TransferRequest::class);
    }
}
