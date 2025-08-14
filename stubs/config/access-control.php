<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Access Control Toggle
    |--------------------------------------------------------------------------
    */
    'enabled' => env('ACCESS_CONTROL_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'superadmin' => 'Full system access (Dictator)',
        'administrator' => 'Handles administration and security related works.',
        'user' => 'Default user role, can create and participate in events.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    | Using verb-first convention. CRUD-heavy functions are grouped under "manage".
    */
    'permissions' => [
        [
            'module' => 'General',
            'functions' => [
                'telescope' => ['manage'],
                'queue' => ['manage'],
                'administration' => ['manage'],
                'security' => ['manage'],
                'settings' => ['manage'],
                'impersonation' => ['enter', 'leave'],
            ],
        ],
        [
            'module' => 'Security',
            'functions' => [
                'access-control' => ['manage'],
                'role' => ['manage'],
                'user' => ['manage'],
                'issues' => ['view', 'update'],
                'queues' => ['view', 'update'],
                'audit' => ['view'],
            ],
        ],
        [
            'module' => 'Dashboard',
            'functions' => [
                'user' => ['view'],
                'administrator' => ['view'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Scopes
    |--------------------------------------------------------------------------
    | Define what each role can access.
    | Supports wildcard (*) or prefix matching for simplicity.
    */
    'role_scope' => [
        'superadmin' => '*', // semua permissions

        'administrator' => [
            'manage-administration',
            'manage-security',
            'manage-settings',
            'view-impersonate',
            'manage-user',
            'manage-role',
            'view-dashboard',
        ],

        'user' => [
            'view-dashboard',
            'view-user-dashboard',
        ],
    ],
];
