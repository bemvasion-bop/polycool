<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@polysync.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@polysync.com',
                'password' => Hash::make('password'),

                // SYSTEM ROLE
                'role' => 'owner',

                // EMPLOYEE FIELDS
                'given_name' => 'System',
                'middle_name' => null,
                'last_name' => 'Administrator',
                'phone_number' => '0000000000',
                'employee_status' => 'active',
            ]
        );
    }
}
