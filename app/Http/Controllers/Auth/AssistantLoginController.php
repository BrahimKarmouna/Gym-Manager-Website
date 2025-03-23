<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AssistantLoginController extends Controller
{
    /**
     * Where to redirect assistants after login.
     *
     * @var string
     */
    protected $redirectTo = '/assistant';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Controllers don't have direct access to middleware in Laravel 9+
        // This middleware will need to be applied in the routes
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.assistant-login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Add debug logging
        Log::channel('daily')->info('Assistant login attempt', [
            'email' => $request->email,
            'exists' => Assistant::where('email', $request->email)->exists()
        ]);

        // Attempt authentication with credentials
        if (Auth::guard('assistant')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->filled('remember'))) {
            
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            $assistant = Auth::guard('assistant')->user();
            $assistant->last_login = now();
            $assistant->save();

            if ($request->wantsJson()) {
                // Generate sanctum token for API authentication
                $token = $assistant->createToken('assistant-token')->plainTextToken;
                
                return response()->json([
                    'message' => 'Successfully logged in as assistant',
                    'assistant' => $assistant,
                    'role' => $assistant->role,
                    'token' => $token
                ]);
            }

            return redirect()->intended($this->redirectTo);
        }

        // If the login attempt was unsuccessful
        throw ValidationException::withMessages([
            'email' => ['These credentials do not match our records.'],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('assistant')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Successfully logged out'], 200);
        }

        return redirect()->route('assistant.login');
    }
}
