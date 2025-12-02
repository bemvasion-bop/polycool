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
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();

            // Payroll type: office staff OR field workers
            $table->enum('payroll_type', ['office', 'field']);

            $table->date('period_start');
            $table->date('period_end');

            $table->enum('status', ['draft', 'finalized'])->default('draft');

            // totals summarized AFTER finalization
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);

            // audit logging
            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};
