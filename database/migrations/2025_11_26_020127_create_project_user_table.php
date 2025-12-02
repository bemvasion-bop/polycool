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
            Schema::create('project_user', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('project_id');
                $table->unsignedBigInteger('user_id');

                // Employee's specific job role inside this project (optional)
                $table->string('role_in_project')->nullable();

                // When the worker was added to the project
                $table->timestamp('assigned_at')->nullable();

                $table->timestamps();

                $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                // Prevent duplicate assignment
                $table->unique(['project_id', 'user_id']);
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('project_user');
        }
    };
