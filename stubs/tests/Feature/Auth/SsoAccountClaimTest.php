<?php

use App\Http\Controllers\Auth\SsoController;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Two\User as SocialiteUser;

beforeEach(fn () => $this->seed(AccessControlSeeder::class));

function ssoFindOrCreate(string $email): ?User
{
    $identity = (new SocialiteUser)->map(['id' => 'idp-1', 'name' => 'Owner', 'email' => $email]);

    return (fn () => $this->findOrCreateUser($identity))->call(new SsoController);
}

it('revokes the registrant credentials before handing an unverified account to SSO', function () {
    $squatter = User::factory()->unverified()->create(['email' => 'owner@example.com', 'password' => 'squatter-pass']);
    $squatter->createToken('squatter');
    $squatter->forceFill(['two_factor_secret' => 'secret'])->save();

    $user = ssoFindOrCreate('owner@example.com');

    expect($user->is($squatter))->toBeTrue();

    $squatter->refresh();
    expect(Hash::check('squatter-pass', $squatter->password))->toBeFalse()
        ->and($squatter->two_factor_secret)->toBeNull()
        ->and($squatter->tokens()->count())->toBe(0)
        ->and($squatter->hasVerifiedEmail())->toBeTrue();
});

it('leaves a verified account credentials alone', function () {
    $owner = User::factory()->create(['email' => 'owner@example.com', 'password' => 'owner-pass']);

    ssoFindOrCreate('owner@example.com');

    expect(Hash::check('owner-pass', $owner->fresh()->password))->toBeTrue();
});

it('marks a user created by SSO as verified', function () {
    $user = ssoFindOrCreate('new@example.com');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});
