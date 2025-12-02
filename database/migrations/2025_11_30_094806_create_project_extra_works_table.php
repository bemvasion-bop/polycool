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
        Schema::create('project_extra_works', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            // What extra work was done
            $table->string('description');

            // Optional bd.ft breakdown
            $table->decimal('volume_bdft', 10, 2)->nullable();
            $table->decimal('rate_per_bdft', 10, 2)->nullable();

            // Final extra amount for this line
            $table->decimal('amount', 12, 2);

            // Who recorded this extra work
            $table->foreignId('added_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_extra_works');
    }
};
