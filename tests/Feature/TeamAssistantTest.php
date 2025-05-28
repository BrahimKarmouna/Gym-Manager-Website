<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Team;
use App\Models\Assistant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TeamAssistantTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'user', 'guard_name' => 'web']);
        Role::create(['name' => 'assistant', 'guard_name' => 'web']);
        
        // Create permissions
        Permission::create(['name' => 'view-clients', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-clients', 'guard_name' => 'web']);
        
        // Create a role for assistants in the roles table (for the role_id field)
        \DB::table('roles')->insert([
            'name' => 'assistant_role',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function test_admin_can_create_team()
    {
        $admin = User::factory()->create([
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $response = $this->postJson('/api/teams', [
            'name' => 'Test Team',
            'display_name' => 'Test Team Display',
            'description' => 'A team for testing',
            'is_assistant_team' => true
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('teams', [
            'name' => 'Test Team',
            'display_name' => 'Test Team Display',
            'description' => 'A team for testing',
            'is_assistant_team' => true
        ]);
    }

    public function test_admin_can_assign_user_as_assistant_to_team()
    {
        // Get the existing assistant role ID
        $roleId = \DB::table('roles')->where('name', 'assistant_role')->value('id');
        
        $admin = User::factory()->create([
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        $admin->assignRole('admin');
        
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);
        $this->actingAs($admin);

        // Create a gym first
        $gym = \DB::table('gyms')->insertGetId([
            'name' => 'Test Gym',
            'location' => 'Test Location',
            'user_id' => $admin->id, // Add the user_id field
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create a team
        $team = Team::create([
            'name' => 'Assistant Team',
            'display_name' => 'Assistant Team',
            'description' => 'Team for assistants',
            'owner_id' => $admin->id,
            'gym_id' => $gym,
            'is_assistant_team' => true
        ]);

        // Add the user as an assistant to the team
        $response = $this->postJson("/api/teams/{$team->id}/members", [
            'user_id' => $user->id,
            'role' => 'assistant',
            'permissions' => ['view-clients', 'edit-clients']
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => 'assistant'
        ]);
    }

    public function test_user_management_can_assign_teams_to_assistant()
    {
        $admin = User::factory()->create([
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        // Create teams
        $team1 = Team::create([
            'name' => 'Team 1',
            'display_name' => 'Team 1',
            'owner_id' => $admin->id,
            'is_assistant_team' => true
        ]);

        $team2 = Team::create([
            'name' => 'Team 2',
            'display_name' => 'Team 2',
            'owner_id' => $admin->id,
            'is_assistant_team' => true
        ]);

        // Create a new user as an assistant with team assignments
        $response = $this->postJson('/api/user-management/users', [
            'name' => 'Assistant User',
            'email' => 'assistant@example.com',
            'password' => 'password123',
            'role' => 'assistant',
            'teams' => [$team1->id, $team2->id]
        ]);

        $response->assertStatus(201);
        
        // Get the created user
        $user = User::where('email', 'assistant@example.com')->first();
        
        // Check team assignments
        $this->assertCount(2, $user->teams);
        $this->assertTrue($user->teams->contains($team1->id));
        $this->assertTrue($user->teams->contains($team2->id));
    }
}
