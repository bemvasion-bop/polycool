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
        Schema::create('expense_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expense_id')->constrained()->onDelete('cascade');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('old_quantity', 10, 2)->nullable();
            $table->decimal('new_quantity', 10, 2)->nullable();

            $table->decimal('old_amount', 12, 2)->nullable();
            $table->decimal('new_amount', 12, 2)->nullable();

            $table->string('change_reason')->nullable();
            $table->string('type')->default('quantity'); // quantity or cost change

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_histories');
    }
};
