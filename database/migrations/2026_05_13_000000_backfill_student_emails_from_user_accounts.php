<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('students') || !Schema::hasTable('user_accounts')) {
            return;
        }

        if (!Schema::hasColumn('students', 'email') || !Schema::hasColumn('students', 'user_account_id')) {
            return;
        }

        // Backfill students.email from linked user_accounts.email
        DB::statement(
            "UPDATE students s " .
            "JOIN user_accounts u ON s.user_account_id = u.id " .
            "SET s.email = u.email " .
            "WHERE (s.email IS NULL OR s.email = '')"
        );
    }

    public function down(): void
    {
        // No-op: we don't want to blank out emails on rollback.
    }
};
