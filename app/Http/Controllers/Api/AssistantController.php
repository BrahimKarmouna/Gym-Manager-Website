<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssistantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Get assistants belonging to gyms owned by this user
        $assistants = Assistant::where('user_id', $user->id)
            ->with('gym:id,name')
            ->get();

        return response()->json($assistants);
    }

    /**
     * Get the authenticated user's associated gyms.
     */
    public function getUserGyms(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Get gyms associated with this user - explicitly select columns to avoid ambiguity
        $gyms = $user->gyms()->get(['gyms.id', 'gyms.name']);

        return response()->json($gyms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:assistants',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', 'string', Rule::in(['trainer', 'receptionist', 'manager'])],
            'gym_id' => 'required|exists:gyms,id',
            'photo' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'notes' => 'nullable|string',
            'user_account_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the authenticated user
        $user = $request->user();

        // Check if the gym belongs to the user
        $gym = Gym::where('id', $request->gym_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$gym) {
            return response()->json(['message' => 'You do not have permission to add assistants to this gym'], 403);
        }

        // Handle photo upload if provided
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('assistants', 'public');
        }

        // Create the assistant
        $assistant = Assistant::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'gym_id' => $request->gym_id,
            'user_id' => $user->id,
            'user_account_id' => $request->user_account_id,
            'photo' => $photoPath,
            'active' => $request->has('active') ? $request->active : true,
            'notes' => $request->notes,
        ]);

        // Assign the appropriate role to the user account if it exists
        if ($request->user_account_id) {
            $userAccount = \App\Models\User::find($request->user_account_id);
            if ($userAccount) {
                // Map assistant role to role ID
                $roleId = 3; // default role (trainer - ID 3)
                
                // Map assistant roles to system role IDs
                if ($request->role === 'manager') {
                    $roleId = 2; // manager role ID
                } else if ($request->role === 'trainer') {
                    $roleId = 3; // trainer role ID
                } else if ($request->role === 'receptionist') {
                    $roleId = 3; // Use trainer role for receptionists as well
                }
                
                // Sync the role to ensure it's the only role the user has
                $userAccount->roles()->sync([$roleId]);
            }
        }

        return response()->json(['message' => 'Assistant created successfully', 'assistant' => $assistant]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        // Get the authenticated user
        $user = $request->user();

        // Get assistant belonging to user's gym
        $assistant = Assistant::where('id', $id)
            ->where('user_id', $user->id)
            ->with('gym:id,name')
            ->first();

        if (!$assistant) {
            return response()->json(['message' => 'Assistant not found'], 404);
        }

        return response()->json($assistant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Get the authenticated user
        $user = $request->user();

        // Get assistant belonging to user
        $assistant = Assistant::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$assistant) {
            return response()->json(['message' => 'Assistant not found'], 404);
        }

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes', 'required', 'string', 'email', 'max:255',
                Rule::unique('assistants')->ignore($assistant->id),
            ],
            'phone' => 'nullable|string|max:20',
            'role' => ['sometimes', 'required', 'string', Rule::in(['trainer', 'receptionist', 'manager'])],
            'gym_id' => 'sometimes|required|exists:gyms,id',
            'photo' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'notes' => 'nullable|string',
            'user_account_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // If gym_id is provided, check if it belongs to the user
        if ($request->has('gym_id')) {
            $gym = Gym::where('id', $request->gym_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$gym) {
                return response()->json(['message' => 'You do not have permission to assign assistants to this gym'], 403);
            }
        }

        // Handle photo upload if provided
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($assistant->photo && Storage::exists('public/' . $assistant->photo)) {
                Storage::delete('public/' . $assistant->photo);
            }

            $photoPath = $request->file('photo')->store('assistants', 'public');
            $assistant->photo = $photoPath;
        }

        // Update the assistant
        if ($request->has('name')) $assistant->name = $request->name;
        if ($request->has('email')) $assistant->email = $request->email;
        if ($request->has('phone')) $assistant->phone = $request->phone;
        if ($request->has('gym_id')) $assistant->gym_id = $request->gym_id;
        if ($request->has('active')) $assistant->active = $request->active;
        if ($request->has('notes')) $assistant->notes = $request->notes;
        if ($request->has('user_account_id')) $assistant->user_account_id = $request->user_account_id;

        // Update role if provided
        if ($request->has('role')) {
            // Update the role directly
            $assistant->role = $request->role;
            
            // Update the user account role if it exists
            if ($assistant->user_account_id) {
                $userAccount = \App\Models\User::find($assistant->user_account_id);
                if ($userAccount) {
                    // Map assistant role to role ID
                    $roleId = 3; // default role (trainer - ID 3)
                    
                    // Map assistant roles to system role IDs
                    if ($request->role === 'manager') {
                        $roleId = 2; // manager role ID
                    } else if ($request->role === 'trainer') {
                        $roleId = 3; // trainer role ID
                    } else if ($request->role === 'receptionist') {
                        $roleId = 3; // Use trainer role for receptionists as well
                    }
                    
                    // Sync the role to ensure it's the only role the user has
                    $userAccount->roles()->sync([$roleId]);
                }
            }
        }

        $assistant->save();

        return response()->json(['message' => 'Assistant updated successfully', 'assistant' => $assistant]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        // Get the authenticated user
        $user = $request->user();

        // Get assistant belonging to user
        $assistant = Assistant::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$assistant) {
            return response()->json(['message' => 'Assistant not found'], 404);
        }

        // Delete photo if exists
        if ($assistant->photo && Storage::exists('public/' . $assistant->photo)) {
            Storage::delete('public/' . $assistant->photo);
        }

        // Delete the assistant
        $assistant->delete();

        return response()->json(['message' => 'Assistant deleted successfully']);
    }

    /**
     * Get permissions for an assistant
     */
    public function getPermissions(Request $request, $id)
    {
        // Get the authenticated user
        $user = $request->user();

        // Get assistant belonging to user
        $assistant = Assistant::where('id', $id)
            ->where('user_id', $user->id)
            ->with('userAccount')
            ->first();

        if (!$assistant) {
            return response()->json(['message' => 'Assistant not found'], 404);
        }

        // If assistant is linked to a user account, get permissions from the user
        if ($assistant->userAccount) {
            $permissions = $assistant->userAccount->getAllPermissions();
            $rolePermissions = $assistant->userAccount->roles->flatMap(function ($role) {
                return $role->permissions;
            })->pluck('name')->toArray();
            
            return response()->json([
                'permissions' => $permissions,
                'role_permissions' => $rolePermissions,
                'message' => 'Permissions are managed through the linked user account'
            ]);
        }

        // For assistants without user accounts, return a simplified set of permissions based on role
        $rolePermissions = [];

        switch ($assistant->role) {
            case 'manager':
                $rolePermissions = [
                    'view-dashboard',
                    'manage-members',
                    'manage-clients',
                    'manage-payments',
                    'view-expenses',
                    'manage-products',
                    'view-reports'
                ];
                break;

            case 'trainer':
                $rolePermissions = [
                    'view-dashboard',
                    'view-members',
                    'manage-clients',
                    'view-payments'
                ];
                break;

            case 'receptionist':
                $rolePermissions = [
                    'view-dashboard',
                    'view-members',
                    'view-clients',
                    'manage-payments',
                    'manage-products'
                ];
                break;
        }

        // Create a simplified permission structure
        $permissions = [];
        foreach ($rolePermissions as $permName) {
            $permissions[] = [
                'id' => $permName,
                'name' => $permName,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        return response()->json([
            'permissions' => $permissions,
            'role_permissions' => $rolePermissions,
            'message' => 'This assistant has no linked user account. Consider creating a user account for more granular permissions.'
        ]);
    }

    /**
     * Update permissions for an assistant
     */
    public function updatePermissions(Request $request, $id)
    {
        // Get the authenticated user
        $user = $request->user();

        // Get assistant belonging to user
        $assistant = Assistant::where('id', $id)
            ->where('user_id', $user->id)
            ->with('userAccount')
            ->first();

        if (!$assistant) {
            return response()->json(['message' => 'Assistant not found'], 404);
        }

        // If assistant is linked to a user account, permissions must be updated through the user
        if ($assistant->userAccount) {
            return response()->json([
                'message' => 'This assistant is linked to a user account. Please update permissions through the user management interface.',
                'user_id' => $assistant->user_account_id
            ], 422);
        }

        // For assistants without user accounts, we'll just acknowledge the request
        // since we're moving to a user-based permission system
        return response()->json([
            'message' => 'This assistant has no linked user account. Create a user account for this assistant to manage permissions.'
        ]);
    }
}
