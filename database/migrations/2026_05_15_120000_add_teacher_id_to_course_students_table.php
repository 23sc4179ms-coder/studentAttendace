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
        Schema::table('course_students', function (Blueprint $table) {
            if (!Schema::hasColumn('course_students', 'teacher_id')) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('student_id');
                $table->index(['teacher_id', 'course_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_students', function (Blueprint $table) {
            if (Schema::hasColumn('course_students', 'teacher_id')) {
                $table->dropIndex(['teacher_id', 'course_id']);
                $table->dropColumn('teacher_id');
            }
        });
    }
};
