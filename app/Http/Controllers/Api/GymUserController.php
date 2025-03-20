<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GymUserController extends Controller
{
    /**
     * Display a listing of the gyms the authenticated user has access to
     */
    public function index()
    {
        $user = Auth::user();
        $gyms = $user->isAdmin() ? Gym::all() : $user->gyms;
        
        return response()->json([
            'gyms' => $gyms,
            'is_admin' => $user->isAdmin()
        ]);
    }
    
    /**
     * Display a listing of all gyms (admin only)
     */
    public function allGyms()
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json(Gym::all());
    }
    
    /**
     * Assign a user to a gym
     */
    public function assignUserToGym(Request $request)
    {
        // Only admins can assign users to gyms
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gym_id' => 'required|exists:gyms,id'
        ]);
        
        $user = User::findOrFail($request->user_id);
        $gym = Gym::findOrFail($request->gym_id);
        
        // Check if already assigned
        if ($user->gyms()->where('gym_id', $gym->id)->exists()) {
            return response()->json([
                'message' => 'User already has access to this gym'
            ]);
        }
        
        // Assign the user to the gym
        $user->gyms()->attach($gym->id);
        
        Log::info("User {$user->id} ({$user->name}) assigned to Gym {$gym->id} ({$gym->name})");
        
        return response()->json([
            'message' => 'User assigned to gym successfully',
            'user' => $user->only(['id', 'name', 'email']),
            'gym' => $gym->only(['id', 'name', 'location'])
        ]);
    }
    
    /**
     * Remove a user from a gym
     */
    public function removeUserFromGym(Request $request)
    {
        // Only admins can remove users from gyms
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gym_id' => 'required|exists:gyms,id'
        ]);
        
        $user = User::findOrFail($request->user_id);
        $gym = Gym::findOrFail($request->gym_id);
        
        // Remove the user from the gym
        $user->gyms()->detach($gym->id);
        
        Log::info("User {$user->id} ({$user->name}) removed from Gym {$gym->id} ({$gym->name})");
        
        return response()->json([
            'message' => 'User removed from gym successfully'
        ]);
    }
    
    /**
     * List users with access to a specific gym
     */
    public function gymUsers($gymId)
    {
        // Get the authenticated user's authorized gyms
        $authorizedGymIds = Auth::user()->getAuthorizedGymIds();
        
        // Check if the user has access to the requested gym
        if (!in_array($gymId, $authorizedGymIds) && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $gym = Gym::findOrFail($gymId);
        $users = $gym->users()->select('users.id', 'users.name', 'users.email', 'users.is_admin')->get();
        
        return response()->json([
            'gym' => $gym->only(['id', 'name', 'location']),
            'users' => $users
        ]);
    }
    
    /**
     * Assign the current user to all gyms (for testing purposes only)
     */
    public function assignCurrentUserToAllGyms()
    {
        $user = Auth::user();
        $gyms = Gym::all();
        
        foreach ($gyms as $gym) {
            if (!$user->gyms()->where('gym_id', $gym->id)->exists()) {
                $user->gyms()->attach($gym->id);
                Log::info("User {$user->id} ({$user->name}) assigned to Gym {$gym->id} ({$gym->name})");
            }
        }
        
        return response()->json([
            'message' => 'Current user assigned to all gyms',
            'gym_count' => $gyms->count(),
            'user' => $user->only(['id', 'name', 'email'])
        ]);
    }
}
