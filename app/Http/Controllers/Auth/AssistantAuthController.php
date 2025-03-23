<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AssistantAuthController extends Controller
{
    /**
     * Handle assistant login attempt
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Add debug logging
        \Log::channel('daily')->info('Assistant login attempt', [
            'email' => $request->email,
            'exists' => Assistant::where('email', $request->email)->exists()
        ]);

        // Attempt direct authentication with credentials
        if (Auth::guard('assistant')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->remember)) {
            $assistant = Auth::guard('assistant')->user();
            
            \Log::channel('daily')->info('Assistant authenticated successfully', [
                'id' => $assistant->id,
                'name' => $assistant->name,
                'email' => $assistant->email
            ]);
            
            // Update last login time
            $assistant->last_login = now();
            $assistant->save();
            
            // Generate sanctum token for API authentication
            $token = $assistant->createToken('assistant-token', ['*'])->plainTextToken;
            
            // Store token in session for persistence
            session(['assistant_api_token' => $token]);
            
            \Log::channel('daily')->info('Token generated for assistant', [
                'token_length' => strlen($token)
            ]);
            
            return response()->json([
                'message' => 'Successfully logged in as assistant',
                'assistant' => $assistant,
                'role' => $assistant->role,
                'token' => $token
            ]);
        }
        
        \Log::channel('daily')->error('Authentication failed for assistant', [
            'email' => $request->email
        ]);
        
        throw ValidationException::withMessages([
            'email' => ['These credentials do not match our records.'],
        ]);
    }

    /**
     * Handle assistant logout
     */
    public function logout(Request $request)
    {
        // Revoke the current token if it exists
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        
        Auth::guard('assistant')->logout();
        
        // Clear the stored token
        session()->forget('assistant_api_token');
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json(['message' => 'Successfully logged out']);
    }
    
    /**
     * Get the currently authenticated assistant
     */
    public function me(Request $request)
    {
        // First try sanctum token authentication for API requests
        if ($request->bearerToken()) {
            $assistant = $request->user();
            
            if ($assistant && $assistant instanceof \App\Models\Assistant) {
                return response()->json([
                    'assistant' => $assistant,
                    'role' => $assistant->role,
                    'authenticated_via' => 'sanctum_token'
                ]);
            }
        }
        
        // Fallback to session-based authentication
        if (Auth::guard('assistant')->check()) {
            $assistant = Auth::guard('assistant')->user();
            return response()->json([
                'assistant' => $assistant,
                'role' => $assistant->role,
                'authenticated_via' => 'session'
            ]);
        }
        
        return response()->json(['message' => 'Not authenticated as assistant'], 401);
    }
}
// In your routes file (web.php or a dedicated routes file)

