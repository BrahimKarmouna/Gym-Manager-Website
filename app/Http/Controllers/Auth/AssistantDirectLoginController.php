<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AssistantDirectLoginController extends Controller
{
    /**
     * Handle direct login without a form submission (for testing only)
     */
    public function loginDirect()
    {
        // Create test assistant first
        $assistant = $this->createTestAssistant();
        
        // Force login directly 
        Auth::guard('assistant')->login($assistant);
        
        // Check if login was successful
        if (Auth::guard('assistant')->check()) {
            // Generate token for API authentication
            $token = $assistant->createToken('direct-login-token')->plainTextToken;
            
            return response()->json([
                'message' => 'Successfully logged in as assistant',
                'assistant' => [
                    'id' => $assistant->id,
                    'name' => $assistant->name,
                    'email' => $assistant->email,
                    'role' => $assistant->role,
                    'gym_id' => $assistant->gym_id,
                ],
                'token' => $token,
                'links' => [
                    'check_status' => '/api/assistant/me',
                    'dashboard' => '/assistant/dashboard'
                ]
            ]);
        }
        
        return response()->json(['message' => 'Failed to login as assistant'], 401);
    }
    
    /**
     * Display the dashboard for authenticated assistants
     */
    public function dashboard()
    {
        if (!Auth::guard('assistant')->check()) {
            return redirect()->route('assistant.login');
        }
        
        $assistant = Auth::guard('assistant')->user();
        
        return view('assistant.dashboard', [
            'assistant' => $assistant,
            'gym' => $assistant->gym
        ]);
    }
    
    /**
     * Create a test assistant account
     */
    private function createTestAssistant()
    {
        // Get the first gym or create one if none exists
        $gym = Gym::first();
        if (!$gym) {
            $gym = Gym::create([
                'name' => 'Test Gym',
                'location' => 'Test Location',
                'description' => 'Test Description'
            ]);
        }
        
        // Check if test assistant already exists
        $testAssistant = Assistant::where('email', 'test.assistant@example.com')->first();
        
        // Create a new test assistant account if it doesn't exist
        if (!$testAssistant) {
            $testAssistant = Assistant::create([
                'name' => 'Test Assistant',
                'email' => 'test.assistant@example.com',
                'password' => Hash::make('password123'),
                'role' => 'trainer',
                'gym_id' => $gym->id,
                'user_id' => 1, // Assuming user ID 1 exists
                'active' => true
            ]);
        }
        
        return $testAssistant;
    }
}
