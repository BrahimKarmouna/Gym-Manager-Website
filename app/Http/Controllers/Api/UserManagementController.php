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
    if (!$request->user()->hasRole(['admin', 'super-admin']) && !$request->user()->hasPermissionTo('view-users')) {
      return response()->json(['message' => 'Unauthorized to view users'], 403);
    }

    // Get users based on permissions
    $query = User::with(['assistant', 'roles', 'permissions', 'creator']);

    // If not admin/super-admin, only show users created by the authenticated user
    if (!$request->user()->hasRole(['admin', 'super-admin'])) {
      $query->where('created_by', $request->user()->id);
    }

    $users = $query->get();

    // Clean up the response format
    $usersFormatted = $users->map(function ($user) {
      $userData = $user->toArray();
      $role = $user->roles->first();
      $userData['role_id'] = $role ? $role->id : null;
      $userData['role'] = $role ? $role->name : 'user';
      $userData['creator_name'] = $user->creator ? $user->creator->name : null;
      return $userData;
    });

    return response()->json(['data' => $usersFormatted]);
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
    if (!$request->user()->hasRole(['admin', 'super-admin']) && !$request->user()->hasPermissionTo('create users')) {
      return response()->json(['message' => 'Unauthorized to create users'], 403);
    }

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8',
      'assistant_id' => 'nullable|exists:assistants,id',
      // 'permissions' => 'sometimes|array',
      // 'permissions.*' => 'exists:permissions,id',
    ]);

    // Create user
    $user = User::create([
      'name' => $request->input('name'),
      'email' => $request->input('email'),
      'password' => Hash::make($request->input('password')),
      'created_by' => $request->user()->id, // Set the creator
    ]);

    // // Assign role if provided (by ID)
    // if ($request->filled('role_id')) {
    //   // If not admin/super-admin, ensure they can't assign higher roles
    //   if (!$request->user()->hasRole(['admin', 'super-admin'])) {
    //     $role = Role::find($request->input('role_id'));
    //     if ($role && in_array($role->name, ['admin', 'super-admin'])) {
    //       return response()->json(['message' => 'Unauthorized to assign admin roles'], 403);
    //     }
    //   }
    //   $user->roles()->sync([$request->input('role_id')]);
    // } else {
    //   // Default to user role for directly created users
    //   $defaultRole = Role::where('name', 'user')->first();
    //   if ($defaultRole) {
    //     $user->roles()->sync([$defaultRole->id]);
    //   }
    // }

    // // Assign permissions if provided
    // if ($request->filled('permissions')) {
    //   $permissions = Permission::whereIn('id', $request->input('permissions'))->get();
    //   $user->syncPermissions($permissions);
    // }

    // Link assistant if provided
    if ($request->filled('assistant_id')) {
      $assistant = Assistant::find($request->input('assistant_id'));
      if ($assistant) {
        $assistant->user_account_id = $user->id;
        $assistant->save();
      }
    }

    return response()->json([
      'message' => 'User created successfully',
      'user' => $user
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
    $user = User::findOrFail($id);

    // Check if user has permission to update users
    if (!$request->user()->hasRole(['admin', 'super-admin'])) {
      // Regular users can only update users they created
      if ($user->created_by !== $request->user()->id) {
        return response()->json(['message' => 'Unauthorized to update this user'], 403);
      }
    }

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
      // If not admin/super-admin, ensure they can't assign higher roles
      if (!$request->user()->hasRole(['admin', 'super-admin'])) {
        $role = Role::find($request->input('role_id'));
        if ($role && in_array($role->name, ['admin', 'super-admin'])) {
          return response()->json(['message' => 'Unauthorized to assign admin roles'], 403);
        }
      }
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
    $updatedUser = User::with(['assistant', 'roles', 'permissions', 'creator'])->find($user->id);
    $responseData = $updatedUser->toArray();
    $role = $updatedUser->roles->first();
    $responseData['role_id'] = $role ? $role->id : null;
    $responseData['role'] = $role ? $role->name : 'user';
    $responseData['creator_name'] = $updatedUser->creator ? $updatedUser->creator->name : null;

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
    $user = User::findOrFail($id);

    // Check if user has permission to delete users
    if (!$request->user()->hasRole(['admin', 'super-admin'])) {
      // Regular users can only delete users they created
      if ($user->created_by !== $request->user()->id) {
        return response()->json(['message' => 'Unauthorized to delete this user'], 403);
      }
    }

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
    if (!$request->user()->hasRole(['admin', 'super-admin']) && !$request->user()->hasPermissionTo('view roles')) {
      return response()->json(['message' => 'Unauthorized to view roles and permissions'], 403);
    }

    $roles = Role::all()->map(function ($role) {
      return [
        'id' => $role->id,
        'name' => $role->name,
        'label' => ucwords(str_replace('-', ' ', $role->name))
      ];
    });

    $permissions = Permission::all()->map(function ($permission) {
      return [
        'id' => $permission->id,
        'name' => $permission->name,
        'label' => ucwords(str_replace('-', ' ', $permission->name))
      ];
    });

    return response()->json([
      'roles' => $roles,
      'permissions' => $permissions
    ]);
  }
}
