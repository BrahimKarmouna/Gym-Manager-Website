<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestAssistantController extends Controller
{
    /**
     * Create a test assistant account and return login credentials
     */
    public function createTestAssistant()
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
        } else {
            // Update password for existing test assistant
            $testAssistant->password = Hash::make('password123');
            $testAssistant->save();
        }
        
        return response()->json([
            'message' => 'Test assistant created successfully',
            'credentials' => [
                'email' => 'test.assistant@example.com',
                'password' => 'password123'
            ],
            'assistant' => $testAssistant,
            'login_url' => url('/assistant-login')
        ]);
    }
    
    /**
     * Test assistant login directly without going through the frontend
     */
    public function testLogin()
    {
        // Find the test assistant
        $assistant = Assistant::where('email', 'test.assistant@example.com')->first();
        
        if (!$assistant) {
            return response()->json(['message' => 'Test assistant not found. Please create one first.'], 404);
        }
        
        // Attempt login with the assistant
        if (Auth::guard('assistant')->attempt([
            'email' => 'test.assistant@example.com',
            'password' => 'password123'
        ])) {
            // Generate token for API authentication
            $token = $assistant->createToken('test-token')->plainTextToken;
            
            return response()->json([
                'message' => 'Test login successful',
                'assistant' => $assistant,
                'token' => $token
            ]);
        }
        
        return response()->json(['message' => 'Test login failed'], 401);
    }
}
