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
        if (!Schema::hasTable('payroll_entries')) {
            Schema::create('payroll_entries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payroll_run_id');
                $table->unsignedBigInteger('user_id');
                $table->enum('employment_type', ['field_worker', 'office_staff']);
                $table->decimal('commission_earnings', 12, 2)->nullable();
                $table->decimal('fixed_salary_portion', 12, 2)->nullable();
                $table->decimal('gross_pay', 12, 2)->default('0');
                $table->decimal('cash_advance_deduction', 12, 2)->default('0');
                $table->decimal('other_deductions', 12, 2)->default('0');
                $table->decimal('net_pay', 12, 2)->default('0');
                $table->json('details')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('payroll_entries');
    }
};
