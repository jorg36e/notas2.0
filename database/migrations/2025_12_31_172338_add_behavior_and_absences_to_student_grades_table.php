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
        Schema::table('student_grades', function (Blueprint $table) {
            $table->enum('behavior', ['BAJO', 'BASICO', 'ALTO', 'SUPERIOR'])->nullable()->after('observations');
            $table->unsignedInteger('absences')->default(0)->after('behavior');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_grades', function (Blueprint $table) {
            $table->dropColumn(['behavior', 'absences']);
        });
    }
};
