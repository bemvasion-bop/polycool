<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'position_title')) {
            $table->string('position_title')->nullable();
        }
        if (!Schema::hasColumn('users', 'daily_rate')) {
            $table->decimal('daily_rate', 10, 2)->nullable();
        }
        if (!Schema::hasColumn('users', 'employment_type')) {
            $table->string('employment_type')->nullable();
        }
        if (!Schema::hasColumn('users', 'system_role')) {
            $table->string('system_role')->default('employee');
        }
    });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        if (Schema::hasColumn('users', 'position_title')) {
            $table->dropColumn('position_title');
        }
        if (Schema::hasColumn('users', 'daily_rate')) {
            $table->dropColumn('daily_rate');
        }
        if (Schema::hasColumn('users', 'employment_type')) {
            $table->dropColumn('employment_type');
        }
        if (Schema::hasColumn('users', 'system_role')) {
            $table->dropColumn('system_role');
        }
    });
    }
};
