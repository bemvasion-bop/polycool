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
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // --- Salary/Commission Calculations ---
            $table->decimal('gross_pay', 14, 2)->default(0);
            $table->decimal('total_hours', 8, 2)->nullable();
            $table->decimal('overtime_hours', 8, 2)->nullable();
            $table->integer('late_minutes')->nullable();
            $table->integer('absences')->nullable();

            // Commission values
            $table->decimal('commission_earnings', 14, 2)->nullable();

            // Salary values
            $table->decimal('fixed_salary', 14, 2)->nullable();

            // Deductions
            $table->decimal('deductions', 14, 2)->default(0);

            // Final pay
            $table->decimal('net_pay', 14, 2)->default(0);

            // Flexible JSON details (e.g., breakdown)
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
