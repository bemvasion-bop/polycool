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
        // PAYMENTS
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'reversal_of')) {
                $table->unsignedBigInteger('reversal_of')->nullable()->after('status');
            }
            if (!Schema::hasColumn('payments', 'corrected_by')) {
                $table->unsignedBigInteger('corrected_by')->nullable()->after('reversal_of');
            }
            if (!Schema::hasColumn('payments', 'correction_reason')) {
                $table->text('correction_reason')->nullable()->after('corrected_by');
            }
        });

        // EXPENSES
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'reversal_of')) {
                $table->unsignedBigInteger('reversal_of')->nullable()->after('status');
            }
            if (!Schema::hasColumn('expenses', 'corrected_by')) {
                $table->unsignedBigInteger('corrected_by')->nullable()->after('reversal_of');
            }
            if (!Schema::hasColumn('expenses', 'correction_reason')) {
                $table->text('correction_reason')->nullable()->after('corrected_by');
            }
        });
    }

    public function down(): void
    {
        // PAYMENTS
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['reversal_of', 'corrected_by', 'correction_reason']);
        });

        // EXPENSES
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['reversal_of', 'corrected_by', 'correction_reason']);
        });
    }
};
