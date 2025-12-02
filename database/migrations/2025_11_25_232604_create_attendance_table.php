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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');      // The employee
            $table->unsignedBigInteger('project_id');   // Project assigned

            $table->date('date');                       // Attendance day (unique per employee per project)
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();

            $table->decimal('total_hours', 8, 2)->default(0);

            $table->string('status')->default('present'); // present, late, absent, on-site, incomplete, etc.

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->unique(['user_id', 'project_id', 'date']); // Prevent double time-ins in same day
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
