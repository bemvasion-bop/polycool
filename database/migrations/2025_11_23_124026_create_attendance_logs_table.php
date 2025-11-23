<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Optional: If attendance is associated with a project
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');

            $table->date('date');

            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();

            // Auto-calculated based on time_in and time_out
            $table->decimal('hours_worked', 5, 2)->default(0);

            // present / absent / on_leave
            $table->enum('status', ['present', 'absent', 'on_leave'])->default('present');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'date']); // One log per day per employee
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
