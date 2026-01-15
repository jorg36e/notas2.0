<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            
            // Estudiante a trasladar
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            
            // Sedes involucradas
            $table->foreignId('origin_sede_id')->constrained('sedes')->onDelete('cascade');
            $table->foreignId('destination_sede_id')->constrained('sedes')->onDelete('cascade');
            
            // Grados
            $table->foreignId('origin_grade_id')->constrained('grades')->onDelete('cascade');
            $table->foreignId('destination_grade_id')->constrained('grades')->onDelete('cascade');
            
            // Año escolar
            $table->foreignId('school_year_id')->constrained('school_years')->onDelete('cascade');
            
            // Usuarios involucrados
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Estado: pending, approved, rejected, cancelled
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            
            // Tipo de traslado: direct (admin), request (profesor)
            $table->enum('type', ['direct', 'request'])->default('request');
            
            // Información adicional
            $table->text('reason')->nullable(); // Motivo del traslado
            $table->text('rejection_reason')->nullable(); // Motivo de rechazo
            $table->text('notes')->nullable(); // Notas adicionales
            
            // Fechas
            $table->timestamp('processed_at')->nullable(); // Fecha de procesamiento
            
            $table->timestamps();
            
            // Índices
            $table->index(['status', 'origin_sede_id']);
            $table->index(['status', 'destination_sede_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};
