<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'corrected_at')) {
                $table->timestamp('corrected_at')->nullable()->after('corrected_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'corrected_at')) {
                $table->dropColumn('corrected_at');
            }
        });
    }
};
