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
        Schema::create('payroll_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_run_id')
                  ->constrained('payroll_runs')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // From the user table
            $table->enum('employment_type', ['field_worker', 'office_staff']);

            // FIELD WORKERS: commission-based
            $table->decimal('commission_earnings', 12, 2)->nullable();

            // OFFICE STAFF: fixed salary
            $table->decimal('fixed_salary_portion', 12, 2)->nullable();

            // COMMON TO BOTH
            $table->decimal('gross_pay', 12, 2)->default(0);
            $table->decimal('cash_advance_deduction', 12, 2)->default(0);
            $table->decimal('other_deductions', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);

            // JSON breakdown (optional)
            $table->json('details')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_entries');
    }
};
