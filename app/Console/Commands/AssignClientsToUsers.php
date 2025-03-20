<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignClientsToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-clients-to-users {user_id? : ID of the user to assign clients to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign existing clients without user_id to a specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get user ID from argument or prompt for it
        $userId = $this->argument('user_id');
        
        if (!$userId) {
            // List all users for selection
            $users = User::select('id', 'name', 'email', 'is_admin')->get();
            
            $this->info('Available users:');
            foreach ($users as $user) {
                $this->line("[{$user->id}] {$user->name} ({$user->email}) " . ($user->is_admin ? '[Admin]' : ''));
            }
            
            $userId = $this->ask('Enter the ID of the user to assign clients to:');
        }
        
        // Verify the user exists
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }
        
        // Count clients without user_id
        $unassignedCount = Client::whereNull('user_id')->count();
        
        if ($unassignedCount === 0) {
            $this->info('No unassigned clients found. All clients already have a user_id assigned.');
            return 0;
        }
        
        $this->info("Found {$unassignedCount} clients without a user assignment.");
        
        if ($this->confirm("Do you want to assign all {$unassignedCount} clients to {$user->name}?")) {
            // Update all unassigned clients to belong to this user
            $updated = DB::table('clients')
                ->whereNull('user_id')
                ->update(['user_id' => $userId]);
            
            $this->info("Successfully assigned {$updated} clients to user {$user->name}.");
        } else {
            $this->info('Operation cancelled.');
        }
        
        return 0;
    }
}
