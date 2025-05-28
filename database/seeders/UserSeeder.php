<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\Gym;
use App\Models\Assistant;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $assistantRole = Role::firstOrCreate(['name' => 'assistant', 'guard_name' => 'web']);
        
        // Check if assistant_role exists, if not create it
        $assistantRoleExists = DB::table('roles')->where('name', 'assistant_role')->exists();
        if (!$assistantRoleExists) {
            $assistantRoleId = DB::table('roles')->insertGetId([
                'name' => 'assistant_role',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $assistantRoleId = DB::table('roles')->where('name', 'assistant_role')->value('id');
        }
        
        // Create permissions
        $viewClientsPermission = Permission::firstOrCreate(['name' => 'view-clients', 'guard_name' => 'web']);
        $editClientsPermission = Permission::firstOrCreate(['name' => 'edit-clients', 'guard_name' => 'web']);
        
        // Create admin user first
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        $admin->assignRole($adminRole);
        
        // Create a gym using the admin's ID
        $gym = Gym::firstOrCreate(
            ['name' => 'Main Gym'],
            [
                'location' => 'Downtown',
                'user_id' => $admin->id,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        
        // Create assistant user
        $assistant = User::firstOrCreate(
            ['email' => 'assistant@example.com'],
            [
                'name' => 'Assistant User',
                'password' => Hash::make('password'),
                'role' => 'assistant',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        $assistant->assignRole($assistantRole);
        
        // Create a team
        $team = Team::firstOrCreate(
            ['name' => 'Main Team'],
            [
                'display_name' => 'Main Team',
                'description' => 'The main team for the gym',
                'owner_id' => $admin->id,
                'gym_id' => $gym->id,
                'is_assistant_team' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        
        // Add admin to the team as admin
        if (!$team->hasMember($admin)) {
            $team->users()->attach($admin->id, [
                'role' => 'admin',
                'permissions' => json_encode(['*']),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Add assistant to the team as assistant
        if (!$team->hasMember($assistant)) {
            $team->users()->attach($assistant->id, [
                'role' => 'assistant',
                'permissions' => json_encode(['view-clients', 'edit-clients']),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Create assistant record
            $assistantRecord = Assistant::firstOrCreate(
                ['user_account_id' => $assistant->id],
                [
                    'name' => $assistant->name,
                    'email' => $assistant->email,
                    'user_id' => $admin->id,
                    'gym_id' => $gym->id,
                    'role_id' => $assistantRoleId,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        
        // Create some regular users
        User::factory(5)->create()->each(function ($user) use ($userRole) {
            $user->assignRole($userRole);
        });
    }
}
