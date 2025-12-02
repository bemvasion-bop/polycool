<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Add new payroll fields
            if (!Schema::hasColumn('users', 'payroll_type')) {
                $table->enum('payroll_type', ['salary', 'commission'])
                      ->default('commission')
                      ->after('system_role'); // FIXED
            }

            if (!Schema::hasColumn('users', 'daily_rate')) {
                $table->decimal('daily_rate', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('users', 'commission_rate')) {
                $table->decimal('commission_rate', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('users', 'allowance')) {
                $table->decimal('allowance', 12, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $cols = ['payroll_type', 'daily_rate', 'commission_rate', 'allowance'];

            foreach ($cols as $c) {
                if (Schema::hasColumn('users', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
