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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('identification');
            $table->string('phone', 20)->nullable()->after('role');
            $table->text('address')->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('address');
            $table->string('guardian_name')->nullable()->after('birth_date');
            $table->string('guardian_phone', 20)->nullable()->after('guardian_name');
            $table->boolean('is_active')->default(true)->after('guardian_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address', 'birth_date', 'guardian_name', 'guardian_phone', 'is_active']);
        });
    }
};
