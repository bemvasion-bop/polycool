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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('quotation_id')->nullable();

            $table->foreign('quotation_id')
                ->references('id')
                ->on('quotations')
                ->nullOnDelete();

            $table->string('project_name');
            $table->string('location')->nullable();
            $table->decimal('total_price', 12, 2)->default(0);;

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
            $table->timestamps();
        });

     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
