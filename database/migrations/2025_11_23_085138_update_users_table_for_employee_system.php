<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Basic name fields
            $table->string('given_name')->nullable()->after('id');
            $table->string('middle_name')->nullable()->after('given_name');
            $table->string('last_name')->nullable()->after('middle_name');

            // Contact
            $table->string('phone_number')->nullable()->after('email');

            // Personal info
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_hired')->nullable();

            // Address
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();

            // Work-related
            $table->string('position_title')->nullable();
            $table->enum('employee_status', ['active', 'inactive'])->default('active');
            $table->enum('role', ['owner','manager','employee'])->default('employee');

            // Replace name column
            $table->string('name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop all added fields
            $table->dropColumn([
                'given_name',
                'middle_name',
                'last_name',
                'phone_number',
                'gender',
                'date_of_birth',
                'date_hired',
                'street_address',
                'city',
                'province',
                'postal_code',
                'position_title',
                'employee_status',
                'role',
            ]);
        });
    }
};
