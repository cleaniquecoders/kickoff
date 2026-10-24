<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Additive by design: safe to re-run on a live app (e.g. from a deploy operation after adding a
 * permission to config). It creates missing roles/permissions and grants missing role_scope
 * permissions, but never overwrites what an admin changed in the UI — no description/enabled
 * reset, no syncPermissions() stripping grants an admin added.
 */
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
            Role::firstOrCreate(
                ['name' => $role, 'guard_name' => 'web'],
                [
                    'display_name' => str($role)->headline()->toString(),
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
                Permission::firstOrCreate(
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
     * Grant role_scope permissions to roles. Additive: grants an admin added are kept, and a
     * permission removed from role_scope is not revoked from live roles — revoke it explicitly.
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
                $role->givePermissionTo(Permission::all());

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

            $role->givePermissionTo($permissions);
        }
    }
}
