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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');

            // Header
            $table->date('quotation_date');
            $table->string('project_name');         // Vessel or Project name
            $table->string('address')->nullable();
            $table->string('system')->default('Spray in place polyurethane foam');
            $table->text('scope_of_work')->nullable();
            $table->integer('duration_days')->nullable();

            // Costing
            $table->decimal('total_bdft', 10, 2)->default(0);
            $table->decimal('rate_per_bdft', 10, 2)->default(45);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('contract_price', 10, 2)->default(0);
            $table->decimal('down_payment', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);

            // Conditions
            $table->longText('conditions')->nullable();

            // Status
            $table->enum('status', ['pending', 'approved', 'declined', 'converted'])
                  ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
