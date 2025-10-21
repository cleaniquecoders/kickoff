<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRoles();
        $this->seedPermissions();
        $this->mapPermissionsToRoles();
    }

    /**
     * Seed roles from config.
     */
    private function seedRoles(): void
    {
        foreach (config('access-control.roles') as $role => $description) {
            Role::updateOrCreate(
                ['name' => $role],
                [
                    'display_name' => str($role)->headline()->toString(),
                    'guard_name' => 'web',
                    'description' => $description,
                    'is_enabled' => true,
                ]
            );
        }
    }

    /**
     * Seed permissions from the new grouped permission structure.
     * Creates permissions with format: module.action.target (e.g., users.view.list)
     */
    private function seedPermissions(): void
    {
        $permissions = config('access-control.permissions');

        foreach ($permissions as $module => $modulePermissions) {
            foreach ($modulePermissions as $permissionKey => $description) {
                Permission::updateOrCreate(
                    [
                        'name' => "{$module}.{$permissionKey}",
                        'guard_name' => 'web',
                    ],
                    [
                        'module' => str($module)->title()->toString(),
                        'function' => $description,
                        'is_enabled' => true,
                    ]
                );
            }
        }
    }

    /**
     * Map permissions to roles based on role_scope.
     */
    private function mapPermissionsToRoles(): void
    {
        $roleScopes = config('access-control.role_scope');

        foreach ($roleScopes as $roleName => $scopes) {
            $role = Role::where('name', $roleName)->first();

            if (! $role) {
                continue;
            }

            // Superadmin (wildcard *)
            if ($scopes === '*') {
                $role->syncPermissions(Permission::all());

                continue;
            }

            $permissions = collect();

            foreach ($scopes as $scope) {
                if (str($scope)->contains('*')) {
                    // prefix search
                    $prefix = rtrim($scope, '*');
                    $permissions = $permissions->merge(
                        Permission::where('name', 'like', "{$prefix}%")->get()
                    );
                } else {
                    $permissions = $permissions->merge(
                        Permission::where('name', $scope)->get()
                    );
                }
            }

            $role->syncPermissions($permissions);
        }
    }
}
