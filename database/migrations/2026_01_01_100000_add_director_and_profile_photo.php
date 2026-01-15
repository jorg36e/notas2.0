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
        // Agregar director de grupo a los grados
        Schema::table('grades', function (Blueprint $table) {
            $table->foreignId('director_id')->nullable()->after('is_active')
                ->constrained('users')->nullOnDelete();
        });

        // Agregar imagen de perfil a los usuarios
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('guardian_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['director_id']);
            $table->dropColumn('director_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });
    }
};
