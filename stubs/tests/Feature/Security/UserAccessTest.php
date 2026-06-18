<?php

use App\Livewire\Security\Users\ManageAccess;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('administrator');
});

test('administrator can toggle a role on a user', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($this->admin)
        ->test(ManageAccess::class)
        ->dispatch('open-user-access', uuid: $user->uuid)
        ->call('toggleRole', 'user');

    expect($user->refresh()->hasRole('user'))->toBeTrue();

    $component->call('toggleRole', 'user');

    expect($user->refresh()->hasRole('user'))->toBeFalse();
});

test('administrator can grant and revoke a direct permission', function () {
    $user = User::factory()->create();
    $permission = Permission::where('name', 'media.upload.files')->first();

    $component = Livewire::actingAs($this->admin)
        ->test(ManageAccess::class)
        ->dispatch('open-user-access', uuid: $user->uuid)
        ->call('togglePermission', $permission->id);

    expect($user->refresh()->hasDirectPermission('media.upload.files'))->toBeTrue();

    $component->call('togglePermission', $permission->id);

    expect($user->refresh()->hasDirectPermission('media.upload.files'))->toBeFalse();
});

test('role-inherited permissions cannot be toggled directly', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    // profile.view.own is granted via the user role
    $permission = Permission::where('name', 'profile.view.own')->first();

    Livewire::actingAs($this->admin)
        ->test(ManageAccess::class)
        ->dispatch('open-user-access', uuid: $user->uuid)
        ->call('togglePermission', $permission->id);

    expect($user->refresh()->hasDirectPermission('profile.view.own'))->toBeFalse()
        ->and($user->can('profile.view.own'))->toBeTrue();
});

test('users without permission cannot open manage access', function () {
    $actor = User::factory()->create();
    $actor->assignRole('user');

    $target = User::factory()->create();

    Livewire::actingAs($actor)
        ->test(ManageAccess::class)
        ->dispatch('open-user-access', uuid: $target->uuid)
        ->assertForbidden();
});
