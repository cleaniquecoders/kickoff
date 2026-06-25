<?php

return [
    'enabled' => env('ADMIN_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Public Registration
    |--------------------------------------------------------------------------
    |
    | Fresh-install default for whether the public self-service registration
    | page is available. At runtime this is overridden by the DB-stored
    | AuthenticationSettings (Admin > Settings > Authentication). When disabled,
    | the registration routes are not registered and only administrators can
    | create accounts.
    |
    */
    'public_registration' => env('REGISTRATION_ENABLED', true),
];
