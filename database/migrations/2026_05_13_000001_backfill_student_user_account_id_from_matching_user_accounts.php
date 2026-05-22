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

        if (!Schema::hasColumn('students', 'user_account_id')) {
            return;
        }

        // Legacy fix: some student rows were created without linking to user_accounts,
        // but a matching user_accounts row exists using the same ID.
        // Only link when the user account role is 'student' to avoid attaching admins.
        DB::statement(
            "UPDATE students s " .
            "JOIN user_accounts u ON u.id = s.id " .
            "SET s.user_account_id = u.id " .
            "WHERE s.user_account_id IS NULL AND u.role = 'student'"
        );
    }

    public function down(): void
    {
        // No-op: do not unlink on rollback.
    }
};
