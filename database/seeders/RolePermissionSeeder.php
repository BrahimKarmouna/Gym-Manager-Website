<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Création des rôles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        // Création des permissions
        $permissions = [
            'view-dashboard',
            'manage-users',
            'edit-posts',
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::firstOrCreate(['name' => $perm]);

            // Assigner toutes les permissions à l'admin
            $adminRole->permissions()->syncWithoutDetaching($permission->id);

            // Exemple : assigne une permission limitée à l'utilisateur
            if ($perm === 'view-dashboard') {
                $userRole->permissions()->syncWithoutDetaching($permission->id);
            }
        }
    }
}
