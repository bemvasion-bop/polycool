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

            if (!Schema::hasColumn('expenses', 'original_amount')) {
                $table->decimal('original_amount', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('expenses', 'corrected_by')) {
                $table->unsignedBigInteger('corrected_by')->nullable()->after('added_by');
            }

            if (!Schema::hasColumn('expenses', 'correction_reason')) {
                $table->text('correction_reason')->nullable();
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {

            if (Schema::hasColumn('expenses', 'original_amount')) {
                $table->dropColumn('original_amount');
            }

            if (Schema::hasColumn('expenses', 'corrected_by')) {
                $table->dropColumn('corrected_by');
            }

            if (Schema::hasColumn('expenses', 'correction_reason')) {
                $table->dropColumn('correction_reason');
            }
        });
    }
};
