<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Assistant;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for users
        $userPermissions = [
            'view users',
            'create users',
            'update users',
            'delete users',
        ];

        // Create permissions for assistants
        $assistantPermissions = [
            'view assistants',
            'create assistants',
            'update assistants',
            'delete assistants',
        ];

        // Create permissions for roles and permissions
        $rolePermissions = [
            'view roles',
            'create roles',
            'update roles',
            'delete roles',
        ];

        // Create permissions for clients
        $clientPermissions = [
            'view clients',
            'create clients',
            'update clients',
            'delete clients',
        ];

        // Create permissions for products
        $productPermissions = [
            'view products',
            'create products',
            'update products',
            'delete products',
            'view orders',
            'update orders',
            'view sells',
        ];

        // Create permissions for plans
        $planPermissions = [
            'view plans',
            'create plans',
            'update plans',
            'delete plans',
            'view insurance plans',
            'create insurance plans',
            'update insurance plans',
            'delete insurance plans',
        ];

        // Create permissions for expenses
        $expensePermissions = [
            'view expenses',
            'create expenses',
            'update expenses',
            'delete expenses',
            'view invoices',
            'create invoices',
            'update invoices',
            'delete invoices',
        ];

        // Create permissions for payments
        $paymentPermissions = [
            'view payments',
            'create payments',
            'update payments',
            'delete payments',
        ];

        // Create all permissions
        $allPermissions = array_merge(
            $userPermissions,
            $assistantPermissions,
            $rolePermissions,
            $clientPermissions,
            $productPermissions,
            $planPermissions,
            $expensePermissions,
            $paymentPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles and assign permissions
        // Make sure admin is created first with ID 1
        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::findOrCreate('manager');
        $managerRole->givePermissionTo([
            'view users', 'view assistants', 'view roles',
            'view clients', 'create clients', 'update clients',
            'view products', 'view orders', 'view sells',
            'view plans', 'view insurance plans',
            'view expenses', 'view invoices',
            'view payments', 'create payments', 'update payments',
        ]);

        $trainerRole = Role::findOrCreate('trainer');
        $trainerRole->givePermissionTo([
            'view clients', 'update clients',
            'view plans',
            'view payments',
        ]);

        // Get admin role ID
        $adminRoleId = $adminRole->id; // Should be 1

        // Assign admin role to all direct user accounts (users that are not linked to assistants)
        $users = User::all();
        
        foreach ($users as $user) {
            // Check if this user is not linked to an assistant
            $isAssistantAccount = Assistant::where('user_account_id', $user->id)->exists();
            
            if (!$isAssistantAccount) {
                // This is a direct user account, give it admin privileges by role ID
                $user->roles()->sync([$adminRoleId]);
            } else {
                // This is an assistant account, it should have limited permissions
                // Assign trainer role by default for assistants
                if (!$user->hasAnyRole()) {
                    $user->roles()->sync([3]); // Trainer role ID should be 3
                }
            }
        }
    }
}
