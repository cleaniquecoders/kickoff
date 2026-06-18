<?php

use App\Enums\UserStatus;
use App\Livewire\Security\Users\Index;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('administrator');
});

test('administrator can suspend and activate a user', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('performSuspend', [$user->uuid]);

    expect($user->refresh()->isSuspended())->toBeTrue();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('activate', $user->uuid);

    expect($user->refresh()->isSuspended())->toBeFalse();
});

test('administrators cannot suspend themselves', function () {
    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('suspend', $this->admin->uuid)
        ->assertForbidden();
});

test('suspended users are logged out on their next request', function () {
    $user = User::factory()->create();
    $user->suspend();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('suspended users cannot be impersonated', function () {
    $user = User::factory()->create();
    $user->suspend();

    expect($user->canBeImpersonated())->toBeFalse();
});

test('administrator can send a password reset link', function () {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('sendPasswordResetLink', $user->uuid);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('administrator can resend the verification email to unverified users', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('resendVerification', $user->uuid);

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('verification email is not resent to verified users', function () {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('resendVerification', $user->uuid);

    Notification::assertNothingSent();
});

test('user status is derived correctly', function () {
    $user = User::factory()->create();
    expect($user->status())->toBe(UserStatus::ACTIVE);

    $unverified = User::factory()->unverified()->create();
    expect($unverified->status())->toBe(UserStatus::UNVERIFIED);

    $user->suspend();
    expect($user->refresh()->status())->toBe(UserStatus::SUSPENDED);

    $user->delete();
    expect($user->refresh()->status())->toBe(UserStatus::DELETED);
});
