<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            // Project the payment belongs to
            $table->foreignId('project_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Payment Details
            $table->decimal('amount', 14, 2);
            $table->enum('payment_method', ['cash', 'gcash', 'bank', 'cheque']);
            $table->date('payment_date');
            $table->text('notes')->nullable();

            // Status for Workflow
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');

            // Who submitted the payment (Manager OR Owner)
            $table->foreignId('submitted_by')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Who approved the payment (Owner or Accounting)
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Optional receipt photo / attachment
            $table->string('receipt_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
