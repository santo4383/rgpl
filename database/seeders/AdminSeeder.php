<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::create([
            'name' => 'SUPER_ADMIN',
            'description' => 'Super Administrator with full access',
        ]);

        $user = User::create([
            'name' => 'Super Admin',
            'mobile_number' => '1234567890',
            'dob' => '1990-01-01',
            'address' => 'Admin HQ',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->roles()->attach($superAdminRole->id);
    }
}
