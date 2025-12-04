<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('payroll_runs')) {
            Schema::create('payroll_runs', function (Blueprint $table) {
                $table->id();
                $table->enum('payroll_type', ['office', 'field']);
                $table->date('period_start');
                $table->date('period_end');
                $table->enum('status', ['draft', 'finalized'])->default('draft');
                $table->decimal('total_gross', 12, 2)->default('0');
                $table->decimal('total_deductions', 12, 2)->default('0');
                $table->decimal('total_net', 12, 2)->default('0');
                $table->unsignedBigInteger('generated_by');
                $table->unsignedBigInteger('finalized_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};
