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
                    'guard_name'   => 'web',
                    'description'  => $description,
                    'is_enabled'   => true,
                ]
            );
        }
    }

    /**
     * Seed permissions from config without auto expanding manage.
     */
    private function seedPermissions(): void
    {
        collect(config('access-control.permissions'))->each(function ($permission) {
            $module = $permission['module'];
            $functions = $permission['functions'];

            foreach ($functions as $function => $actions) {
                foreach ($actions as $action) {
                    Permission::updateOrCreate(
                        [
                            'name'       => "{$action}-{$function}",
                            'guard_name' => 'web',
                        ],
                        [
                            'module'     => $module,
                            'function'   => str($function)->title()->toString(),
                            'is_enabled' => true,
                        ]
                    );
                }
            }
        });
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
