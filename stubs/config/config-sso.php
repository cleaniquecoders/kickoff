<?php

use CleaniqueCoders\ConfigSso\Http\Controllers\SsoController;
use CleaniqueCoders\ConfigSso\Livewire\SsoProviders;
use SocialiteProviders\Azure\AzureExtendSocialite;
use SocialiteProviders\Keycloak\KeycloakExtendSocialite;

return [

    /*
    |--------------------------------------------------------------------------
    | Feature Toggle
    |--------------------------------------------------------------------------
    | Master switch for the SSO feature. When false, ConfigSso::providers()
    | returns an empty collection so login buttons disappear.
    */
    'feature' => env('CONFIG_SSO_FEATURE', true),

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    | The application's authenticatable model. Used to find an existing user by
    | email and to create one on first SSO login. Keep this app-agnostic — the
    | package never references App\Models\User directly.
    */
    'user_model' => env('CONFIG_SSO_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    */
    'tables' => [
        'providers' => 'sso_providers',
        'user_providers' => 'sso_user_providers',
    ],

    /*
    |--------------------------------------------------------------------------
    | Login Buttons Component
    |--------------------------------------------------------------------------
    | The Blade tag for the login-buttons component. Defaults to <x-sso />.
    | Usage: <x-sso />, <x-sso provider="github" />, <x-sso only="github,keycloak" />.
    | Set to null to skip registering a global tag (e.g. to avoid a name clash).
    */
    'component' => 'sso',

    /*
    |--------------------------------------------------------------------------
    | Authorization Gate
    |--------------------------------------------------------------------------
    | Ability string (or callable) checked before the admin UI loads. Set to
    | null to disable the check (NOT recommended in production).
    */
    'gate' => env('CONFIG_SSO_GATE', 'admin.manage.sso'),

    /*
    |--------------------------------------------------------------------------
    | Drivers
    |--------------------------------------------------------------------------
    | The providers offered in the admin UI. "fields" lists the extra,
    | driver-specific config keys stored in the JSON "config" column.
    */
    'drivers' => [
        'google' => ['label' => 'Google'],
        'github' => ['label' => 'GitHub'],
        'gitlab' => ['label' => 'GitLab', 'fields' => ['instance_url']],
        'bitbucket' => ['label' => 'Bitbucket'],
        'keycloak' => ['label' => 'Keycloak', 'fields' => ['base_url', 'realms']],
        'azure' => ['label' => 'Azure AD', 'fields' => ['tenant']],
    ],

    /*
    |--------------------------------------------------------------------------
    | SocialiteProviders Extensions
    |--------------------------------------------------------------------------
    | Non-core Socialite drivers are registered via socialiteproviders/* listeners.
    | Each is wired only when its listener class is installed (class_exists).
    | Install e.g. `composer require socialiteproviders/keycloak`.
    */
    'socialite_providers' => [
        'keycloak' => KeycloakExtendSocialite::class,
        'azure' => AzureExtendSocialite::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | User Provisioning
    |--------------------------------------------------------------------------
    */
    'registration' => [
        // Create a local user when no account matches the SSO email.
        'enabled' => true,
        // Role assigned to newly created users via spatie/permission (if installed).
        // Set to null to skip role assignment.
        'default_role' => 'user',
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirects
    |--------------------------------------------------------------------------
    | Route names used after a successful login and on failure.
    */
    'redirect' => [
        'home' => 'dashboard',
        'login' => 'login',
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth Routes
    |--------------------------------------------------------------------------
    | Registers sso.redirect ({provider}/redirect) and sso.callback
    | ({provider}/callback).
    */
    'routes' => [
        'enabled' => true,
        'prefix' => 'auth/sso',
        'middleware' => ['web'],
        'controller' => SsoController::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Management UI (Livewire + Flux)
    |--------------------------------------------------------------------------
    | Registered as the Livewire component "config-sso.admin". When `route` is
    | true a full-page GET route is registered; otherwise embed the component
    | yourself with <livewire:config-sso.admin />.
    */
    'admin' => [
        'enabled' => true,
        'route' => true,
        'prefix' => 'admin/settings/sso',
        'name' => 'config-sso.admin',
        'middleware' => ['web', 'auth'],
        'component' => SsoProviders::class,
        // Render the SSO admin UI inside the application layout.
        'layout' => env('CONFIG_SSO_ADMIN_LAYOUT', 'components.layouts.app'),
    ],

];
