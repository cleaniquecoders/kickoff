<?php

use CleaniqueCoders\ConfigSso\Models\SsoProvider;

it('renders no SSO block while no provider is active', function () {
    $this->get(route('login'))->assertOk()->assertDontSee('auth/sso/', false);
});

it('offers an active SSO provider on login and register', function (string $route) {
    SsoProvider::create([
        'name' => 'GitHub',
        'driver' => 'github',
        'client_id' => 'id',
        'client_secret' => 'secret',
        'is_active' => true,
    ]);

    $this->get(route($route))->assertOk()->assertSee(route('sso.redirect', 'github'), false);
})->with(['login', 'register']);

it('renders the login page without a sign-up link when registration is off', function () {
    // Registration off removes the `register` route; the view must not call route('register').
    $routes = app('router')->getRoutes();
    $routes->refreshNameLookups();
    $register = $routes->getByName('register');
    expect($register)->not->toBeNull();

    $register->name('register-disabled');
    $routes->refreshNameLookups();

    $this->get(route('login'))->assertOk()->assertDontSee(__('Sign up'));
});
