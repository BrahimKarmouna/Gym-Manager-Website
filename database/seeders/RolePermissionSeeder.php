<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
  public function run(): void
  {
    // Create roles
    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
    $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
    $trainerRole = Role::firstOrCreate(['name' => 'trainer', 'guard_name' => 'web']);
    $receptionistRole = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);

    // Define permission groups
    $permissionGroups = [
      'dashboard' => ['view-dashboard'],
      'users' => ['view-users', 'manage-users'],
      'assistants' => ['view-assistants', 'manage-assistants'],
      'gyms' => ['view-gyms', 'manage-gyms'],
      'members' => ['view-members', 'manage-members'],
      'clients' => ['view-clients', 'manage-clients'],
      'plans' => ['view-plans', 'manage-plans'],
      'insurance' => ['view-insurance', 'manage-insurance'],
      'payments' => ['view-payments', 'manage-payments'],
      'expenses' => ['view-expenses', 'manage-expenses', 'create-expenses'],
      'products' => ['view-products', 'manage-products'],
      'reports' => ['view-reports', 'generate-reports'],
      'settings' => ['view-settings', 'manage-settings'],

    ];

    // Create permissions and assign to admin
    foreach ($permissionGroups as $group => $permissions) {
      foreach ($permissions as $permissionName) {
        $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);

        // Assign all permissions to admin role
        $adminRole->givePermissionTo($permission);

        // Assign view-dashboard to user role
        if ($permissionName === 'view-dashboard') {
          $userRole->givePermissionTo($permission);
        }
      }
    }

    // Assign permissions to manager role
    $managerPermissions = [
      'view-dashboard',
      'view-gyms',
      'view-members',
      'manage-members',
      'view-clients',
      'manage-clients',
      'view-plans',
      'view-insurance',
      'view-payments',
      'manage-payments',
      'view-expenses',
      'view-products',
      'manage-products',
      'view-reports',
      'view-settings'
    ];

    foreach ($managerPermissions as $permissionName) {
      $managerRole->givePermissionTo($permissionName);
    }

    // Assign permissions to trainer role
    $trainerPermissions = [
      'view-dashboard',
      'view-members',
      'view-clients',
      'manage-clients',
      'view-plans',
      'view-payments'
    ];

    foreach ($trainerPermissions as $permissionName) {
      $trainerRole->givePermissionTo($permissionName);
    }

    // Assign permissions to receptionist role
    $receptionistPermissions = [
      'view-dashboard',
      'view-members',
      'view-clients',
      'view-plans',
      'view-payments',
      'manage-payments',
      'view-products',
      'manage-products'
    ];

    foreach ($receptionistPermissions as $permissionName) {
      $receptionistRole->givePermissionTo($permissionName);
    }
  }
}
