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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'user_account_id')) {
                $table->foreignId('user_account_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('user_accounts')
                    ->cascadeOnDelete();

                $table->unique('user_account_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'user_account_id')) {
                $table->dropUnique(['user_account_id']);
                $table->dropConstrainedForeignId('user_account_id');
            }
        });
    }
};
