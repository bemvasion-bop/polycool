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
        Schema::table('cash_advances', function (Blueprint $table) {
            // Only add if it doesn't exist yet
            if (!Schema::hasColumn('cash_advances', 'remaining_balance')) {
                $table->decimal('remaining_balance', 10, 2)
                      ->default(0)
                      ->after('amount'); // adjust 'amount' to the proper column if different
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_advances', function (Blueprint $table) {
            if (Schema::hasColumn('cash_advances', 'remaining_balance')) {
                $table->dropColumn('remaining_balance');
            }
        });
    }
};
