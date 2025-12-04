<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@polysync.com'],
            [
                'name'            => 'System Administrator',
                'email'           => 'admin@polysync.com',
                'system_role'     => 'owner',
                'employment_type' => 'office_staff',
                'password'        => Hash::make('password'),
            ]
        );
    }
}
