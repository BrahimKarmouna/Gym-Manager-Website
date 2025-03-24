<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        // Assign admin role to the user using role ID 1
        $admin->roles()->sync([1]);
        
        // Create a manager user
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        // Assign manager role to the user using role ID 2
        $manager->roles()->sync([2]);
        
        // Create a trainer user
        $trainer = User::create([
            'name' => 'Trainer User',
            'email' => 'trainer@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        // Assign trainer role to the user using role ID 3
        $trainer->roles()->sync([3]);
    }
}
