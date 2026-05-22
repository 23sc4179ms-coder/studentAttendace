<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('user_accounts', 'must_change_password')) {
                $table->boolean('must_change_password')->default(0)->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('user_accounts', 'must_change_password')) {
                $table->dropColumn('must_change_password');
            }
        });
    }
};
