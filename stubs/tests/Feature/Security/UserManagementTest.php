<?php

use App\Livewire\Security\Users\Index;
use App\Livewire\Security\Users\UserForm;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('administrator');
});

test('users index page can be rendered by administrator', function () {
    $this->actingAs($this->admin)
        ->get(route('security.users.index'))
        ->assertOk();
});

test('guests are redirected to login', function () {
    $this->get(route('security.users.index'))
        ->assertRedirect(route('login'));
});

test('users without permission cannot access the index', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)
        ->get(route('security.users.index'))
        ->assertForbidden();
});

test('users can be searched by name and email', function () {
    User::factory()->create(['name' => 'Alpha Tester', 'email' => 'alpha@example.com']);
    User::factory()->create(['name' => 'Beta Tester', 'email' => 'beta@example.com']);

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('search', 'alpha')
        ->assertSee('Alpha Tester')
        ->assertDontSee('Beta Tester');
});

test('users can be filtered by status', function () {
    $suspended = User::factory()->create(['name' => 'Suspended Person']);
    $suspended->suspend();
    User::factory()->create(['name' => 'Active Person']);

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('statusFilter', 'suspended')
        ->assertSee('Suspended Person')
        ->assertDontSee('Active Person');
});

test('users can be filtered by role', function () {
    $other = User::factory()->create(['name' => 'Plain Person']);
    $other->assignRole('user');

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('roleFilter', 'administrator')
        ->assertSee($this->admin->name)
        ->assertDontSee('Plain Person');
});

test('administrator can create a user with a password setup link', function () {
    Notification::fake();

    Livewire::actingAs($this->admin)
        ->test(UserForm::class)
        ->dispatch('open-user-form')
        ->set('name', 'New Person')
        ->set('email', 'new@example.com')
        ->set('roles', ['user'])
        ->set('sendPasswordSetupLink', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('user-saved');

    $user = User::where('email', 'new@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole('user'))->toBeTrue();

    Notification::assertSentTo($user, ResetPassword::class);
});

test('user creation validates required fields and unique email', function () {
    Livewire::actingAs($this->admin)
        ->test(UserForm::class)
        ->dispatch('open-user-form')
        ->set('name', '')
        ->set('email', $this->admin->email)
        ->call('save')
        ->assertHasErrors(['name' => 'required', 'email' => 'unique']);
});

test('administrator can edit a user and email change resets verification', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(UserForm::class)
        ->dispatch('open-user-form', uuid: $user->uuid)
        ->set('name', 'Renamed Person')
        ->set('email', 'renamed@example.com')
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Renamed Person')
        ->and($user->email)->toBe('renamed@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

test('administrator can delete and restore a user', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('delete', $user->uuid);

    expect($user->refresh()->trashed())->toBeTrue();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('restore', $user->uuid);

    expect($user->refresh()->trashed())->toBeFalse();
});

test('administrators cannot delete themselves', function () {
    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('delete', $this->admin->uuid)
        ->assertForbidden();
});
