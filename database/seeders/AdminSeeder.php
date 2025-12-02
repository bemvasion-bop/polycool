<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'email'          => 'admin@polysync.com',
            'name'           => 'System Administrator',

            // Add our structured name fields
            'given_name'     => 'System',
            'middle_name'    => null,
            'last_name'      => 'Administrator',

            // Contact
            'phone_number'   => '0000000000',

            // NEW: System role field
            'system_role'    => 'owner',

            // Status
            'employee_status' => 'active',

            // Password
            'password' => Hash::make('password'),

        ]);
    }
}
