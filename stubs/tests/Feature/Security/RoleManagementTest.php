<?php

use App\Livewire\Admin\Roles\Index as RolesIndex;
use App\Livewire\Admin\Roles\RoleForm;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('administrator');
});

test('administrator can create a role', function () {
    Livewire::actingAs($this->admin)
        ->test(RoleForm::class)
        ->dispatch('open-role-form')
        ->set('displayName', 'Content Editor')
        ->set('description', 'Manages site content.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('role-saved');

    $role = Role::where('name', 'content-editor')->first();

    expect($role)->not->toBeNull()
        ->and($role->display_name)->toBe('Content Editor')
        ->and((bool) $role->is_enabled)->toBeTrue();
});

test('duplicate role names are rejected', function () {
    Livewire::actingAs($this->admin)
        ->test(RoleForm::class)
        ->dispatch('open-role-form')
        ->set('displayName', 'User')
        ->call('save')
        ->assertHasErrors(['displayName']);
});

test('administrator can edit a role without changing its name', function () {
    $role = Role::create(['name' => 'editor', 'display_name' => 'Editor', 'guard_name' => 'web']);

    Livewire::actingAs($this->admin)
        ->test(RoleForm::class)
        ->dispatch('open-role-form', uuid: $role->uuid)
        ->set('displayName', 'Senior Editor')
        ->call('save')
        ->assertHasNoErrors();

    $role->refresh();

    expect($role->name)->toBe('editor')
        ->and($role->display_name)->toBe('Senior Editor');
});

test('protected roles cannot be deleted', function () {
    $role = Role::where('name', 'user')->first();

    Livewire::actingAs($this->admin)
        ->test(RolesIndex::class)
        ->call('performDelete', [$role->uuid]);

    expect(Role::where('name', 'user')->exists())->toBeTrue();
});

test('roles assigned to users cannot be deleted', function () {
    $role = Role::create(['name' => 'editor', 'display_name' => 'Editor', 'guard_name' => 'web']);
    User::factory()->create()->assignRole('editor');

    Livewire::actingAs($this->admin)
        ->test(RolesIndex::class)
        ->call('performDelete', [$role->uuid]);

    expect(Role::where('name', 'editor')->exists())->toBeTrue();
});

test('unassigned custom roles can be deleted', function () {
    $role = Role::create(['name' => 'editor', 'display_name' => 'Editor', 'guard_name' => 'web']);

    Livewire::actingAs($this->admin)
        ->test(RolesIndex::class)
        ->call('performDelete', [$role->uuid]);

    expect(Role::where('name', 'editor')->exists())->toBeFalse();
});

test('roles can be enabled and disabled', function () {
    $role = Role::create(['name' => 'editor', 'display_name' => 'Editor', 'guard_name' => 'web', 'is_enabled' => true]);

    Livewire::actingAs($this->admin)
        ->test(RolesIndex::class)
        ->call('toggleEnabled', $role->uuid);

    expect((bool) $role->refresh()->is_enabled)->toBeFalse();
});

test('users without permission cannot create roles', function () {
    $actor = User::factory()->create();
    $actor->assignRole('user');

    Livewire::actingAs($actor)
        ->test(RoleForm::class)
        ->dispatch('open-role-form')
        ->assertForbidden();
});
