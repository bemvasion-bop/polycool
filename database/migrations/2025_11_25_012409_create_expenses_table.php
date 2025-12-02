<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            // Project (required)
            $table->foreignId('project_id')
                  ->constrained()
                  ->onDelete('cascade');

            // User who submitted
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Material optional
            $table->foreignId('material_id')
                  ->nullable()
                  ->constrained('materials')
                  ->nullOnDelete();

            // Supplier optional
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained('suppliers')
                  ->nullOnDelete();

            // Material fields
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->decimal('quantity_used', 12, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();

            // Non-material expense fields
            $table->string('category')->nullable();
            $table->decimal('amount', 12, 2)->nullable();

            // Required
            $table->date('expense_date');

            // Description
            $table->text('description')->nullable();

            // pending / approved / rejected
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
