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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained()->cascadeOnDelete();
            $table->decimal('tasks_score', 3, 1)->nullable()->comment('Tareas 40%');
            $table->decimal('evaluations_score', 3, 1)->nullable()->comment('Evaluaciones 50%');
            $table->decimal('self_score', 3, 1)->nullable()->comment('Autoevaluación 10%');
            $table->decimal('final_score', 3, 1)->nullable()->comment('Nota final calculada');
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->unique(['teaching_assignment_id', 'student_id', 'period_id'], 'unique_grade_per_student_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};
