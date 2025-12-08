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
        Schema::table('expenses', function (Blueprint $table) {

            // Add cancel audit fields (just append at end)
            if (!Schema::hasColumn('expenses', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable();
            }

            if (!Schema::hasColumn('expenses', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable();
            }

            if (!Schema::hasColumn('expenses', 'related_expense_id')) {
                $table->unsignedBigInteger('related_expense_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'cancelled_by',
                'cancel_reason',
                'related_expense_id'
            ]);
        });
    }
};
