<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserAssistantController extends Controller
{
  /**
   * Display a listing of the users.
   */
  public function index(Request $request)
  {
    // Get the authenticated user
    $user = $request->user();

    // Admin or users with specific permissions can view users
    if (!$user->isAdmin() && !$user->hasPermissionTo('view-users') && !$user->hasPermissionTo('view users')) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Get users with their roles and linked assistants
    $users = User::with(['assistant', 'roles', 'permissions'])
      ->where('id', '!=', $user->id) // Exclude current user
      ->get();

    return response()->json($users);
  }

  /**
   * Store a newly created user with role.
   */
  public function store(Request $request)
  {
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8',
      'role' => ['nullable', 'string'],
      'role_id' => ['nullable', 'exists:roles,id'],
      'assistant_id' => 'nullable|exists:assistants,id',
      'permissions' => 'nullable|array',
      'permissions.*' => 'exists:permissions,id',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $authedUser = auth()->user();


    DB::beginTransaction();

    try {
      // Create the user
      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
      ]);

      // Assign role
      if ($request->has('role_id')) {
        $role = Role::findOrFail($request->role_id);
      } elseif ($request->has('role')) {
        $role = Role::firstOrCreate(['name' => $request->role]);
      } else {
        return response()->json(['message' => 'Either role or role_id is required'], 422);
      }

      $user->assignRole($role);

      // Assign permissions if provided
      if ($request->has('permissions')) {
        $user->syncPermissions(permissions: $request->permissions);
      }

      // Link to assistant if provided
      if ($request->has('assistant_id') && $request->assistant_id) {
        $assistant = Assistant::findOrFail($request->assistant_id);

        // Check if assistant belongs to the authenticated user
        if ($assistant->user_id === $authedUser->id) {
          $assistant->user_account_id = $user->id;
          $assistant->save();
        }
      }

      DB::commit();

      return response()->json([
        'message' => 'User created successfully',
        'user' => $user->load(['assistant', 'roles', 'permissions'])
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Error creating user: ' . $e->getMessage()], 500);
    }
  }

  /**
   * Display the specified user.
   */
  public function show(Request $request, $id)
  {
    $authedUser = $request->user();

    // Only admin can view other users or self
    if (!$authedUser->isAdmin() && $authedUser->id != $id) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $user = User::with(['assistant', 'roles', 'permissions'])
      ->findOrFail($id);

    return response()->json($user);
  }

  /**
   * Update the specified user.
   */
  public function update(Request $request, $id)
  {
    $authedUser = $request->user();

    // Allow admin, self-update, or users with update/manage permission
    if (
      !$authedUser->isAdmin() && $authedUser->id != $id &&
      !$authedUser->hasPermissionTo('update-users') &&
      !$authedUser->hasPermissionTo('update users') &&
      !$authedUser->hasPermissionTo('manage-users') &&
      !$authedUser->hasPermissionTo('manage users')
    ) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $user = User::findOrFail($id);

    // Validate the incoming request
    $validator = Validator::make($request->all(), [
      'name' => 'sometimes|required|string|max:255',
      'email' => [
        'sometimes',
        'required',
        'string',
        'email',
        'max:255',
        Rule::unique('users')->ignore($user->id),
      ],
      'password' => 'nullable|string|min:8',
      'role' => ['sometimes', 'required', 'string'],
      'assistant_id' => 'nullable|exists:assistants,id',
      'permissions' => 'nullable|array',
      'permissions.*' => 'exists:permissions,id',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    DB::beginTransaction();

    try {
      // Update user fields
      if ($request->has('name'))
        $user->name = $request->name;
      if ($request->has('email'))
        $user->email = $request->email;

      // Update password if provided
      if ($request->has('password') && !empty($request->password)) {
        $user->password = Hash::make($request->password);
      }

      // Only admin can update roles and permissions
      if ($authedUser->isAdmin()) {
        // Update role if provided
        if ($request->has('role')) {
          $user->role = $request->role;

          // Sync role in spatie system
          $role = Role::firstOrCreate(['name' => $request->role]);
          $user->syncRoles([$role]);
        }

        // Update permissions if provided
        if ($request->has('permissions')) {
          $user->syncPermissions($request->permissions);
        }

        // Update assistant link if provided
        if ($request->has('assistant_id')) {
          // First, unlink any existing assistant
          Assistant::where('user_account_id', $user->id)
            ->update(['user_account_id' => null]);

          // Link to new assistant if provided
          if ($request->assistant_id) {
            $assistant = Assistant::findOrFail($request->assistant_id);

            // Check if assistant belongs to the authenticated user
            if ($assistant->user_id === $authedUser->id) {
              $assistant->user_account_id = $user->id;
              $assistant->save();
            }
          }
        }
      }

      $user->save();
      DB::commit();

      return response()->json([
        'message' => 'User updated successfully',
        'user' => $user->load(['assistant', 'roles', 'permissions'])
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Error updating user: ' . $e->getMessage()], 500);
    }
  }

  /**
   * Remove the specified user.
   */
  public function destroy(Request $request, $id)
  {
    $authedUser = $request->user();

    // Allow admin or users with delete/manage permission
    if (
      !$authedUser->isAdmin() &&
      !$authedUser->hasPermissionTo('delete-users') &&
      !$authedUser->hasPermissionTo('delete users') &&
      !$authedUser->hasPermissionTo('manage-users') &&
      !$authedUser->hasPermissionTo('manage users')
    ) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Can't delete self
    if ($authedUser->id == $id) {
      return response()->json(['message' => 'Cannot delete your own account'], 400);
    }

    $user = User::findOrFail($id);

    DB::beginTransaction();

    try {
      // Unlink any assistant
      Assistant::where('user_account_id', $user->id)
        ->update(['user_account_id' => null]);

      // Delete the user
      $user->delete();

      DB::commit();
      return response()->json(['message' => 'User deleted successfully']);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Error deleting user: ' . $e->getMessage()], 500);
    }
  }

  /**
   * Get all available assistants for linking with users.
   */
  public function getAvailableAssistants(Request $request)
  {
    $authedUser = $request->user();

    // Get assistants belonging to authenticated user
    $assistants = Assistant::where('user_id', $authedUser->id)
      ->with('gym:id,name')
      ->get();

    return response()->json($assistants);
  }

  /**
   * Get all available roles and permissions.
   */
  public function getRolesAndPermissions(Request $request)
  {
    $roles = Role::all();
    $permissions = Permission::all();

    return response()->json([
      'roles' => $roles,
      'permissions' => $permissions
    ]);
  }

  /**
   * Update permissions for a user.
   */
  public function updatePermissions(Request $request, $id)
  {
    $authedUser = $request->user();

    // Only admin can update permissions
    if (!$authedUser->isAdmin()) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $user = User::findOrFail($id);

    // Validate the incoming request
    $validator = Validator::make($request->all(), [
      'permissions' => 'required|array',
      'permissions.*' => 'exists:permissions,id',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
      // Update permissions
      $user->syncPermissions($request->permissions);

      return response()->json([
        'message' => 'Permissions updated successfully',
        'permissions' => $user->permissions
      ]);
    } catch (\Exception $e) {
      return response()->json(['message' => 'Error updating permissions: ' . $e->getMessage()], 500);
    }
  }
}
