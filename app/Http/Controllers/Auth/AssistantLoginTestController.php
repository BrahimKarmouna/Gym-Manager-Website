<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AssistantLoginTestController extends Controller
{
    /**
     * Display a login form with prefilled credentials for testing
     */
    public function showLoginForm()
    {
        // Create a test assistant if it doesn't exist
        $assistant = $this->createTestAssistant();
        
        return view('auth.assistant-login-test', [
            'email' => $assistant->email,
            'password' => 'password123'
        ]);
    }
    
    /**
     * Handle direct login without API
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        // Attempt login
        if (Auth::guard('assistant')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard');
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
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
        } else {
            // Update password for existing test assistant
            $testAssistant->password = Hash::make('password123');
            $testAssistant->save();
        }
        
        return $testAssistant;
    }
}
