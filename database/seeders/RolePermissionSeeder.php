<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Création des rôles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole  = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Création des permissions
        $permissions = [
            'view-dashboard',
            'manage-users',
            'edit-posts',
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);

            // Assigner toutes les permissions à l'admin
            $adminRole->givePermissionTo($permission);

            // Exemple : assigne une permission limitée à l'utilisateur
            if ($perm === 'view-dashboard') {
                $userRole->givePermissionTo($permission);
            }
        }
    }
}
