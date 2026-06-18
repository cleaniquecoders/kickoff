<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Discovery Mode
    |--------------------------------------------------------------------------
    |
    | Controls how available commands are determined:
    |   'manual'    - Only commands in allowed_commands (default, current behavior)
    |   'auto'      - Auto-discover all Artisan commands minus excluded ones
    |   'selection' - Auto-discover only commands listed in included_commands
    |
    */

    'discovery_mode' => 'manual',

    /*
    |--------------------------------------------------------------------------
    | Excluded Commands
    |--------------------------------------------------------------------------
    |
    | Commands to exclude during auto-discovery. Applies to 'auto' and
    | 'selection' modes.
    |
    */

    'excluded_commands' => [
        'down', 'up', 'serve', 'tinker', 'env', 'inspire',
        'db:wipe', 'migrate:fresh', 'migrate:reset',
        'vendor:publish', 'package:discover',
        'artisan-runner:discover',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Namespaces
    |--------------------------------------------------------------------------
    |
    | Entire command namespaces to skip during discovery.
    |
    */

    'excluded_namespaces' => [
        'make',
        'schedule',
        'queue',
        'stub',
    ],

    /*
    |--------------------------------------------------------------------------
    | Included Commands
    |--------------------------------------------------------------------------
    |
    | When discovery_mode is 'selection', only these commands will be
    | discovered. Their parameter schemas are auto-generated from the
    | command's InputDefinition.
    |
    */

    'included_commands' => [],

    /*
    |--------------------------------------------------------------------------
    | Discovery Cache TTL
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) to cache discovered commands. Set to null to
    | disable caching. Only applies to 'auto' and 'selection' modes.
    |
    */

    'discovery_cache_ttl' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Allowed Commands
    |--------------------------------------------------------------------------
    |
    | Only commands listed here can be executed from the UI. Each entry maps
    | an Artisan command signature to its label, description, group, and
    | parameter schema. In 'auto' or 'selection' modes, these entries take
    | precedence over auto-discovered ones (useful for custom labels or
    | parameter overrides).
    |
    */

    'allowed_commands' => [
        'cache:clear' => [
            'label' => 'Clear Cache',
            'description' => 'Flush the application cache.',
            'group' => 'Cache',
            'parameters' => [],
        ],
        'config:clear' => [
            'label' => 'Clear Config',
            'description' => 'Remove the configuration cache file.',
            'group' => 'Cache',
            'parameters' => [],
        ],
        'route:clear' => [
            'label' => 'Clear Routes',
            'description' => 'Remove the route cache file.',
            'group' => 'Cache',
            'parameters' => [],
        ],
        'view:clear' => [
            'label' => 'Clear Views',
            'description' => 'Clear all compiled view files.',
            'group' => 'Cache',
            'parameters' => [],
        ],
        'migrate' => [
            'label' => 'Run Migrations',
            'description' => 'Run the database migrations.',
            'group' => 'Database',
            'parameters' => [
                ['name' => '--force', 'type' => 'boolean', 'label' => 'Force (production)', 'default' => false],
                ['name' => '--seed', 'type' => 'boolean', 'label' => 'Run seeders', 'default' => false],
                ['name' => '--step', 'type' => 'boolean', 'label' => 'Run individually (rollback one by one)', 'default' => false],
                ['name' => '--pretend', 'type' => 'boolean', 'label' => 'Pretend (show SQL only)', 'default' => false],
            ],
        ],
        'migrate:status' => [
            'label' => 'Migration Status',
            'description' => 'Show the status of each migration.',
            'group' => 'Database',
            'parameters' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    |
    | Configure who gets notified when a command completes or fails.
    | Set enabled to false to disable notifications entirely.
    |
    */

    'notification' => [
        'enabled' => true,
        'channels' => ['database', 'mail'],
        'notifiable' => [
            'model' => 'App\\Models\\User',
            'identifier' => 'email',
            'value' => env('ARTISAN_RUNNER_NOTIFY_EMAIL', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Retention
    |--------------------------------------------------------------------------
    |
    | Number of days to keep command logs. Set to null to keep forever.
    |
    */

    'log_retention_days' => 30,

    /*
    |--------------------------------------------------------------------------
    | Route
    |--------------------------------------------------------------------------
    |
    | Customize the route prefix, middleware, and name for the package routes.
    |
    */

    'route' => [
        'prefix' => 'artisan-runner',
        'middleware' => ['web', 'auth', 'can:access.artisan-runner'],
        'name' => 'artisan-runner.',
    ],
];
