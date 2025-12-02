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
        //
        Schema::table('project_user', function (Blueprint $table) {

            if (!Schema::hasColumn('project_user', 'role_in_project')) {
                $table->string('role_in_project')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('project_user', 'assigned_at')) {
                $table->date('assigned_at')->nullable()->after('role_in_project');
            }

            if (!Schema::hasColumn('project_user', 'days_present')) {
                $table->integer('days_present')->default(0)->after('assigned_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('project_user', function (Blueprint $table) {
            $table->dropColumn(['role_in_project', 'assigned_at', 'days_present']);
        });
    }
};
