<?php

use App\Models\User;
use CleaniqueCoders\ConfigSso\Models\SsoProvider;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);

    SsoProvider::create([
        'name' => 'GitHub',
        'driver' => 'github',
        'client_id' => 'id',
        'client_secret' => 'secret',
        'is_active' => true,
    ]);
});

function ssoSignIn(string $email): void
{
    $identity = (new SocialiteUser)->map(['id' => 'idp-1', 'name' => 'Owner', 'email' => $email]);
    $driver = Mockery::mock(Provider::class);
    $driver->shouldReceive('user')->andReturn($identity);
    Socialite::shouldReceive('driver')->with('github')->andReturn($driver);

    test()->get(route('sso.callback', 'github'))->assertRedirect();
}

it('revokes every registrant credential before handing an unverified account to SSO', function () {
    $squatter = User::factory()->unverified()->create(['email' => 'owner@example.com', 'password' => 'squatter-pass']);
    $squatter->createToken('squatter');
    $squatter->forceFill(['two_factor_secret' => 'secret'])->save();

    ssoSignIn('owner@example.com');

    $squatter->refresh();
    expect(Hash::check('squatter-pass', $squatter->password))->toBeFalse()
        ->and($squatter->two_factor_secret)->toBeNull()
        ->and($squatter->tokens()->count())->toBe(0)
        ->and($squatter->hasVerifiedEmail())->toBeTrue();
    $this->assertAuthenticatedAs($squatter);
});

it('leaves a verified account credentials alone', function () {
    $owner = User::factory()->create(['email' => 'owner@example.com', 'password' => 'owner-pass']);

    ssoSignIn('owner@example.com');

    expect(Hash::check('owner-pass', $owner->fresh()->password))->toBeTrue();
});

it('creates SSO users verified, with the default role', function () {
    ssoSignIn('new@example.com');

    $user = User::where('email', 'new@example.com')->first();
    expect($user->hasVerifiedEmail())->toBeTrue()
        ->and($user->hasRole('user'))->toBeTrue();
});
