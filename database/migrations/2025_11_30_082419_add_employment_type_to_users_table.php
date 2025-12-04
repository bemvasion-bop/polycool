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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'employment_type')) {
                $table->enum('employment_type', ['field_worker', 'office_staff'])
                    ->default('field_worker')
                    ->after('system_role');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'employment_type')) {
                $table->dropColumn('employment_type');
            }
    });
}
};
