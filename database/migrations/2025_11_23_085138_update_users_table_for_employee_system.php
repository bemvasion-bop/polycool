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

            // Position (for HR only — not access role)
            $table->string('position_title')->nullable()->change();

            // Employee account status
            $table->enum('employee_status', ['active', 'inactive'])
                  ->default('active');

            // 🔥 NEW unified system roles
            $table->enum('system_role', ['owner','manager','employee','accounting','audit'])
                  ->default('employee')
                  ->after('employee_status');

            // Allow name to be nullable
            $table->string('name')->nullable()->change();

            // 🔥 SAFE DROP ROLE — only if it exists
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // restore old role field if rollback occurs
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['owner','manager','employee'])
                      ->default('employee');
            }

            // remove new system_role
            if (Schema::hasColumn('users', 'system_role')) {
                $table->dropColumn('system_role');
            }

            // drop additional fields
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
            ]);
        });
    }
};
