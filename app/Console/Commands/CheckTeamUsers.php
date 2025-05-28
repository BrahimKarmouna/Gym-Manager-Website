<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Team;
use App\Models\Assistant;

class CheckTeamUsers extends Command
{
    protected $signature = 'check:team-users';
    protected $description = 'Check users and their team assignments';

    public function handle()
    {
        $this->info('Checking users and their team assignments...');
        
        // Check admin user
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $this->info("Admin user found: {$admin->name} (ID: {$admin->id})");
            $this->info("Roles: " . implode(', ', $admin->roles->pluck('name')->toArray()));
            
            $adminTeams = $admin->teams;
            if ($adminTeams->count() > 0) {
                $this->info("Admin belongs to " . $adminTeams->count() . " teams:");
                foreach ($adminTeams as $team) {
                    $this->info("- {$team->name} (Role: {$team->pivot->role})");
                }
            } else {
                $this->error("Admin doesn't belong to any teams");
            }
        } else {
            $this->error("Admin user not found");
        }
        
        $this->line('');
        
        // Check assistant user
        $assistant = User::where('email', 'assistant@example.com')->first();
        if ($assistant) {
            $this->info("Assistant user found: {$assistant->name} (ID: {$assistant->id})");
            $this->info("Roles: " . implode(', ', $assistant->roles->pluck('name')->toArray()));
            
            $assistantTeams = $assistant->teams;
            if ($assistantTeams->count() > 0) {
                $this->info("Assistant belongs to " . $assistantTeams->count() . " teams:");
                foreach ($assistantTeams as $team) {
                    $this->info("- {$team->name} (Role: {$team->pivot->role})");
                }
            } else {
                $this->error("Assistant doesn't belong to any teams");
            }
            
            // Check assistant record
            $assistantRecord = Assistant::where('user_account_id', $assistant->id)->first();
            if ($assistantRecord) {
                $this->info("Assistant record found (ID: {$assistantRecord->id})");
            } else {
                $this->error("Assistant record not found");
            }
        } else {
            $this->error("Assistant user not found");
        }
        
        $this->line('');
        
        // Check teams
        $teams = Team::with('users')->get();
        $this->info("Found " . $teams->count() . " teams:");
        foreach ($teams as $team) {
            $this->info("Team: {$team->name} (ID: {$team->id})");
            $this->info("Members: " . $team->users->count());
            foreach ($team->users as $user) {
                $this->info("- {$user->name} (Role: {$user->pivot->role})");
            }
            $this->line('');
        }
        
        return 0;
    }
}
