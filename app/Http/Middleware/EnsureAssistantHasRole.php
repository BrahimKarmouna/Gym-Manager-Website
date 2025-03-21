<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAssistantHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::guard('assistant')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            return redirect()->route('assistant.login');
        }

        $assistant = Auth::guard('assistant')->user();
        
        // If no specific roles are required, or if assistant has the required role
        if (empty($roles) || in_array($assistant->role, $roles)) {
            return $next($request);
        }
        
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden: Insufficient privileges'], 403);
        }
        
        return redirect()->route('home')->with('error', 'You do not have permission to access this resource.');
    }
}
