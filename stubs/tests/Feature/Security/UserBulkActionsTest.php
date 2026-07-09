<?php

use App\Livewire\Security\Users\Index;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('administrator');
});

test('bulk delete removes selected users but skips self and superadmins', function () {
    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');

    $userA = User::factory()->create();
    $userB = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('selected', [$userA->uuid, $userB->uuid, $superadmin->uuid, $this->admin->uuid])
        ->call('bulkDelete');

    expect($userA->refresh()->trashed())->toBeTrue()
        ->and($userB->refresh()->trashed())->toBeTrue()
        ->and($superadmin->refresh()->trashed())->toBeFalse()
        ->and($this->admin->refresh()->trashed())->toBeFalse();
});

test('bulk role assignment assigns the role to selected users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('selected', [$userA->uuid, $userB->uuid])
        ->set('bulkRole', 'user')
        ->call('bulkAssignRole');

    expect($userA->refresh()->hasRole('user'))->toBeTrue()
        ->and($userB->refresh()->hasRole('user'))->toBeTrue();
});

test('bulk role assignment requires a role', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('selected', [$user->uuid])
        ->call('bulkAssignRole')
        ->assertHasErrors(['bulkRole' => 'required']);
});

test('non-superadmins cannot bulk assign the superadmin role', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('selected', [$user->uuid])
        ->set('bulkRole', 'superadmin')
        ->call('bulkAssignRole');

    expect($user->refresh()->hasRole('superadmin'))->toBeFalse();
});

test('select page selects all visible users', function () {
    User::factory()->count(3)->create();

    $component = Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('selectPage', true);

    expect(count($component->get('selected')))->toBeGreaterThanOrEqual(4);

    $component->call('clearSelection');

    expect($component->get('selected'))->toBeEmpty();
});
