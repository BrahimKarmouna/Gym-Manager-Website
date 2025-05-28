<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Gym;
use App\Models\Assistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    /**
     * Display a listing of teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = Team::with(['owner', 'gym'])->get();
        return response()->json(['teams' => $teams]);
    }

    /**
     * Store a newly created team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'gym_id' => 'nullable|exists:gyms,id',
            'is_assistant_team' => 'boolean',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'owner_id' => Auth::id(),
            'gym_id' => $request->gym_id,
            'is_assistant_team' => $request->is_assistant_team ?? false,
        ]);

        // Add the creator as an admin of the team
        $team->users()->attach(Auth::id(), [
            'role' => 'admin',
            'permissions' => json_encode(['*']),
        ]);

        return response()->json([
            'message' => 'Team created successfully',
            'team' => $team->load(['owner', 'gym']),
        ], 201);
    }

    /**
     * Display the specified team.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        $team->load(['owner', 'gym', 'users']);
        return response()->json(['team' => $team]);
    }
    
    /**
     * Get all members of a team.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function getMembers(Team $team)
    {
        $members = $team->users()->with(['roles', 'permissions'])->get()->map(function ($user) use ($team) {
            $pivotData = $user->pivot;
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $pivotData->role,
                'permissions' => json_decode($pivotData->permissions),
                'joined_at' => $pivotData->created_at,
                'is_admin' => $user->is_admin,
                'spatie_roles' => $user->roles->pluck('name'),
                'spatie_permissions' => $user->permissions->pluck('name')
            ];
        });
        
        return response()->json([
            'team' => $team->only(['id', 'name', 'display_name', 'description']),
            'members' => $members
        ]);
    }

    /**
     * Update the specified team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'gym_id' => 'nullable|exists:gyms,id',
            'is_assistant_team' => 'boolean',
        ]);

        $team->update($request->only([
            'name',
            'display_name',
            'description',
            'gym_id',
            'is_assistant_team',
        ]));

        return response()->json([
            'message' => 'Team updated successfully',
            'team' => $team->fresh(['owner', 'gym']),
        ]);
    }

    /**
     * Remove the specified team.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        $team->delete();
        return response()->json(['message' => 'Team deleted successfully']);
    }

    /**
     * Add a user to a team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function addMember(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => ['required', Rule::in(['member', 'admin', 'assistant'])],
            'permissions' => 'nullable|array',
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if user is already in the team
        if ($team->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is already a member of this team',
            ], 422);
        }

        // Add user to the team
        $team->users()->attach($user->id, [
            'role' => $request->role,
            'permissions' => json_encode($request->permissions ?? []),
        ]);
        
        // If the user is being added as an assistant and this is an assistant team,
        // create or update their assistant record
        if ($request->role === 'assistant' && $team->is_assistant_team) {
            // Get a role_id for the assistant (required field)
            $roleId = \DB::table('roles')->where('name', 'assistant_role')->value('id');
            if (!$roleId) {
                // If no assistant_role exists, create one
                $roleId = \DB::table('roles')->insertGetId([
                    'name' => 'assistant_role',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Create or update the assistant record
            $assistant = Assistant::firstOrNew(['user_account_id' => $user->id]);
            if (!$assistant->exists) {
                $assistant->name = $user->name;
                $assistant->email = $user->email;
                $assistant->user_id = Auth::id();
                $assistant->gym_id = $team->gym_id ?? 1; // Use team's gym_id or default to 1
                $assistant->role_id = $roleId;
                $assistant->active = true;
                $assistant->save();
            }
        }

        // Assistant creation is now handled above

        return response()->json([
            'message' => 'User added to team successfully',
            'team' => $team->fresh(['users']),
        ]);
    }

    /**
     * Update a team member's role or permissions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateMember(Request $request, Team $team, User $user)
    {
        $request->validate([
            'role' => ['sometimes', 'required', Rule::in(['member', 'admin', 'assistant'])],
            'permissions' => 'nullable|array',
        ]);

        // Check if user is in the team
        if (!$team->hasMember($user)) {
            return response()->json([
                'message' => 'User is not a member of this team',
            ], 422);
        }

        $pivotData = [];
        
        if ($request->has('role')) {
            $pivotData['role'] = $request->role;
        }
        
        if ($request->has('permissions')) {
            $pivotData['permissions'] = json_encode($request->permissions);
        }

        // Update user's role and/or permissions
        $team->users()->updateExistingPivot($user->id, $pivotData);

        // If the user's role is being changed to or from assistant, handle the assistant record
        if ($request->has('role')) {
            $oldRole = $team->users()->where('user_id', $user->id)->first()->pivot->role;
            $newRole = $request->role;
            
            if ($oldRole !== 'assistant' && $newRole === 'assistant' && $team->is_assistant_team) {
                // User is becoming an assistant
                $assistant = Assistant::firstOrNew(['user_account_id' => $user->id]);
                
                if (!$assistant->exists) {
                    // Get a role_id for the assistant (required field)
                    $roleId = \DB::table('roles')->where('name', 'assistant_role')->value('id');
                    if (!$roleId) {
                        // If no assistant_role exists, create one
                        $roleId = \DB::table('roles')->insertGetId([
                            'name' => 'assistant_role',
                            'guard_name' => 'web',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                    
                    $assistant->fill([
                        'name' => $user->name,
                        'email' => $user->email,
                        'user_id' => Auth::id(),
                        'gym_id' => $team->gym_id,
                        'role_id' => $roleId,
                        'active' => true,
                    ]);
                    $assistant->save();
                }
            }
        }

        return response()->json([
            'message' => 'Team member updated successfully',
            'team' => $team->fresh(['users']),
        ]);
    }

    /**
     * Remove a user from a team.
     *
     * @param  \App\Models\Team  $team
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function removeMember(Team $team, User $user)
    {
        // Check if user is in the team
        if (!$team->hasMember($user)) {
            return response()->json([
                'message' => 'User is not a member of this team',
            ], 422);
        }

        // Get the user's role before removing them
        $userRole = $team->users()->where('user_id', $user->id)->first()->pivot->role;
        
        // Remove user from the team
        $team->users()->detach($user->id);

        // If the user was an assistant and this was their only assistant team,
        // consider updating their assistant record
        if ($userRole === 'assistant' && $team->is_assistant_team) {
            $otherAssistantTeams = $user->teams()
                ->where('teams.id', '!=', $team->id)
                ->wherePivot('role', 'assistant')
                ->count();
                
            if ($otherAssistantTeams === 0) {
                // This was their only assistant team, so we can optionally
                // deactivate or remove their assistant record
                $assistant = Assistant::where('user_account_id', $user->id)->first();
                if ($assistant) {
                    // Option 1: Deactivate the assistant
                    $assistant->update(['active' => false]);
                    
                    // Option 2: Delete the assistant (uncomment if preferred)
                    // $assistant->delete();
                }
            }
        }

        return response()->json([
            'message' => 'User removed from team successfully',
        ]);
    }
}
