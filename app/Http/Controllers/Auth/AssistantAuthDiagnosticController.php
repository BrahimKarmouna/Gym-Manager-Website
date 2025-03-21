<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AssistantAuthDiagnosticController extends Controller
{
    /**
     * Run diagnostics on the assistant authentication system
     */
    public function runDiagnostics()
    {
        $output = [];
        
        // 1. Check if assistant table exists
        try {
            $tableExists = DB::getSchemaBuilder()->hasTable('assistants');
            $output['assistant_table_exists'] = $tableExists;
            
            if ($tableExists) {
                // Get table structure
                $columns = DB::getSchemaBuilder()->getColumnListing('assistants');
                $output['assistant_table_columns'] = $columns;
            }
        } catch (\Exception $e) {
            $output['assistant_table_error'] = $e->getMessage();
        }
        
        // 2. Create a test assistant
        try {
            // Get the first gym
            $gym = Gym::first();
            $output['gym_exists'] = $gym ? true : false;
            
            if (!$gym) {
                $gym = Gym::create([
                    'name' => 'Test Gym',
                    'location' => 'Test Location',
                    'description' => 'Test Description'
                ]);
                $output['created_test_gym'] = true;
            }
            
            // Check for existing test assistant
            $testAssistant = Assistant::where('email', 'test.assistant@example.com')->first();
            $output['test_assistant_exists'] = $testAssistant ? true : false;
            
            if (!$testAssistant) {
                // Create the test assistant
                $testAssistant = Assistant::create([
                    'name' => 'Test Assistant',
                    'email' => 'test.assistant@example.com',
                    'password' => Hash::make('password123'),
                    'role' => 'trainer',
                    'gym_id' => $gym->id,
                    'user_id' => 1, // Assuming user ID 1 exists
                    'active' => true
                ]);
                $output['created_test_assistant'] = true;
            } else {
                // Update password
                $testAssistant->password = Hash::make('password123');
                $testAssistant->save();
                $output['updated_test_assistant_password'] = true;
            }
            
            // Store assistant details
            $output['test_assistant'] = [
                'id' => $testAssistant->id,
                'email' => $testAssistant->email,
                'role' => $testAssistant->role,
                'password_hashed' => $testAssistant->password,
                'password_matches' => Hash::check('password123', $testAssistant->password)
            ];
            
        } catch (\Exception $e) {
            $output['assistant_creation_error'] = $e->getMessage();
        }
        
        // 3. Test authentication
        try {
            // Test auth attempt
            $authAttempt = Auth::guard('assistant')->attempt([
                'email' => 'test.assistant@example.com',
                'password' => 'password123'
            ]);
            
            $output['auth_attempt_result'] = $authAttempt;
            
            // Check auth configuration
            $output['auth_guards'] = config('auth.guards');
            $output['auth_providers'] = config('auth.providers');
            
            // Check if we can manually login
            if ($testAssistant) {
                Auth::guard('assistant')->login($testAssistant);
                $output['manual_login_successful'] = Auth::guard('assistant')->check();
                $output['current_assistant'] = Auth::guard('assistant')->user() ? 
                    Auth::guard('assistant')->user()->name : 'None';
            }
            
        } catch (\Exception $e) {
            $output['auth_test_error'] = $e->getMessage();
        }
        
        // 4. Test token generation
        try {
            if ($testAssistant) {
                // Create a token
                $token = $testAssistant->createToken('test-token')->plainTextToken;
                $output['token_generation'] = !empty($token);
                $output['token_length'] = strlen($token);
            }
        } catch (\Exception $e) {
            $output['token_generation_error'] = $e->getMessage();
        }
        
        // Format and display the results
        return response()->json($output);
    }
}
