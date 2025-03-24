<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Assistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller
{
  /**
   * Display a listing of users.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    // Check if user has permission to view users
    if (!$request->user()->hasRole(1) && !$request->user()->hasPermissionTo('view-users')) {
      return response()->json(['message' => 'Unauthorized to view users'], 403);
    }

    // Get all users with their assistants
    $users = User::with(['assistant', 'roles', 'permissions'])->get();

    // Clean up the response format
    $usersFormatted = $users->map(function ($user) {
      $userData = $user->toArray();
      $userData['roleId'] = $user->roles->first()->id ?? null;
      $userData['roleName'] = $user->roles->first()->name ?? 'user';
      return $userData;
    });

    return response()->json($usersFormatted);
  }

  /**
   * Store a newly created user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // Check if user has permission to create users
    if (!$request->user()->hasRole(1) && !$request->user()->hasPermissionTo('create users')) {
      return response()->json(['message' => 'Unauthorized to create users'], 403);
    }

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8',
      'role_id' => 'sometimes|exists:roles,id',
      'assistant_id' => 'nullable|exists:assistants,id',
      'permissions' => 'sometimes|array',
      'permissions.*' => 'exists:permissions,id',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Create user
    $user = User::create([
      'name' => $request->input('name'),
      'email' => $request->input('email'),
      'password' => Hash::make($request->input('password')),
    ]);

    // Assign role if provided (by ID)
    if ($request->filled('role_id')) {
      $user->roles()->sync([$request->input('role_id')]);
    } else {
      // Default to admin role (ID 1) for directly created users
      $user->roles()->sync([1]);
    }

    // Assign permissions if provided
    if ($request->filled('permissions')) {
      $permissions = Permission::whereIn('id', $request->input('permissions'))->get();
      $user->syncPermissions($permissions);
    }

    // Link assistant if provided
    if ($request->filled('assistant_id')) {
      $assistant = Assistant::find($request->input('assistant_id'));
      if ($assistant) {
        $assistant->user_account_id = $user->id;
        $assistant->save();
      }
    }

    // Reload user with relations
    $userWithRelations = User::with(['assistant', 'roles', 'permissions'])->find($user->id);
    $responseData = $userWithRelations->toArray();
    $responseData['roleId'] = $userWithRelations->roles->first()->id ?? null;
    $responseData['roleName'] = $userWithRelations->roles->first()->name ?? 'user';

    return response()->json([
      'message' => 'User created successfully',
      'user' => $responseData
    ], 201);
  }

  /**
   * Update the specified user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    // Check if user has permission to update users
    if (!$request->user()->hasRole(1) && !$request->user()->hasPermissionTo('update users')) {
      return response()->json(['message' => 'Unauthorized to update users'], 403);
    }

    $user = User::findOrFail($id);

    $validator = Validator::make($request->all(), [
      'name' => 'sometimes|string|max:255',
      'email' => [
        'sometimes',
        'string',
        'email',
        'max:255',
        Rule::unique('users')->ignore($user->id)
      ],
      'password' => 'sometimes|nullable|string|min:8',
      'role_id' => 'sometimes|exists:roles,id',
      'assistant_id' => 'nullable|exists:assistants,id',
      'permissions' => 'sometimes|array',
      'permissions.*' => 'exists:permissions,id',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Update name and email if provided
    if ($request->filled('name')) {
      $user->name = $request->input('name');
    }

    if ($request->filled('email')) {
      $user->email = $request->input('email');
    }

    // Update password if provided
    if ($request->filled('password')) {
      $user->password = Hash::make($request->input('password'));
    }

    $user->save();

    // Handle role assignment by ID
    if ($request->filled('role_id')) {
      // Set role using role ID
      $user->roles()->sync([$request->input('role_id')]);
    }

    // Handle permissions
    if ($request->filled('permissions')) {
      $permissions = Permission::whereIn('id', $request->input('permissions'))->get();
      $user->syncPermissions($permissions);
    }

    // Handle assistant assignment
    if ($request->has('assistant_id')) {
      // First, unlink any assistant that might be linked to this user
      Assistant::where('user_account_id', $user->id)->update(['user_account_id' => null]);

      // Then link the new assistant if one is provided
      if ($request->input('assistant_id')) {
        $assistant = Assistant::find($request->input('assistant_id'));
        if ($assistant) {
          $assistant->user_account_id = $user->id;
          $assistant->save();
        }
      }
    }

    // Reload user with relations
    $updatedUser = User::with(['assistant', 'roles', 'permissions'])->find($user->id);
    $responseData = $updatedUser->toArray();
    $responseData['roleId'] = $updatedUser->roles->first()->id ?? null;
    $responseData['roleName'] = $updatedUser->roles->first()->name ?? 'user';

    return response()->json([
      'message' => 'User updated successfully',
      'user' => $responseData
    ]);
  }

  /**
   * Remove the specified user.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    // Check if user has permission to delete users
    if (!$request->user()->hasRole(1) && !$request->user()->hasPermissionTo('delete users')) {
      return response()->json(['message' => 'Unauthorized to delete users'], 403);
    }

    $user = User::findOrFail($id);

    // Unlink any assistant linked to this user
    Assistant::where('user_account_id', $user->id)->update(['user_account_id' => null]);

    // Delete the user
    $user->delete();

    return response()->json(['message' => 'User deleted successfully']);
  }

  /**
   * Get available assistants that can be linked to users.
   *
   * @return \Illuminate\Http\Response
   */
  public function getAvailableAssistants(Request $request)
  {
    // Check if user has permission to view assistants
    if (!$request->user()->hasRole(1) && !$request->user()->hasPermissionTo('view assistants')) {
      return response()->json(['message' => 'Unauthorized to view assistants'], 403);
    }

    // Get all assistants with their gyms
    $assistants = Assistant::with('gym')->get();

    return response()->json($assistants);
  }

  /**
   * Get roles and permissions for user management.
   *
   * @return \Illuminate\Http\Response
   */
  public function getRolesAndPermissions(Request $request)
  {
    // Check if user has permission to view roles and permissions
    if (!$request->user()->hasRole(1) && !$request->user()->hasPermissionTo('view roles')) {
      return response()->json(['message' => 'Unauthorized to view roles and permissions'], 403);
    }

    $roles = Role::all();
    $permissions = Permission::all();

    return response()->json([
      'roles' => $roles,
      'permissions' => $permissions
    ]);
  }
}
