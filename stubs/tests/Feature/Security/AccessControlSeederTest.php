<?php

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\AccessControlSeeder;

it('can be re-run without undoing admin changes', function () {
    $this->seed(AccessControlSeeder::class);

    $user = Role::findByName('user', 'web');
    $extra = Permission::where('name', 'not like', 'profile%')->first();
    $user->givePermissionTo($extra);
    $user->forceFill(['is_enabled' => false])->save();

    $this->seed(AccessControlSeeder::class);

    $user->refresh();
    expect($user->hasPermissionTo($extra))->toBeTrue()
        ->and((bool) $user->is_enabled)->toBeFalse();
});

it('grants permissions added to config on a re-run', function () {
    $this->seed(AccessControlSeeder::class);

    config(['access-control.permissions.reports' => ['view.list' => 'View reports']]);
    config(['access-control.role_scope.administrator' => [...(array) config('access-control.role_scope.administrator'), 'reports.*']]);

    $this->seed(AccessControlSeeder::class);

    expect(Role::findByName('administrator', 'web')->hasPermissionTo('reports.view.list'))->toBeTrue()
        ->and(Role::findByName('superadmin', 'web')->hasPermissionTo('reports.view.list'))->toBeTrue();
});
