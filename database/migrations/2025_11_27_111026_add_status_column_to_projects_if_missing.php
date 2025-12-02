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
        if (!Schema::hasColumn('projects', 'status')) {
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'completed', 'declined'])
                  ->default('pending');
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects_if_missing', function (Blueprint $table) {
            //
        });
    }
};
