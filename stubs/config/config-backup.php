<?php

use Spatie\LaravelSettings\Models\SettingsProperty;

// config for CleaniqueCoders\ConfigBackup
return [

    /*
    |--------------------------------------------------------------------------
    | Feature Toggle
    |--------------------------------------------------------------------------
    | Master switch for the Config Backup feature (UI route + scheduler).
    */
    'feature' => env('CONFIG_BACKUP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    | Where encrypted archives are stored. Use a PRIVATE disk — the archives
    | contain every secret. The archive itself is AES-256 password-encrypted.
    */
    'disk' => env('CONFIG_BACKUP_DISK', 'local'),
    'directory' => env('CONFIG_BACKUP_DIRECTORY', 'config-backups'),

    /*
    |--------------------------------------------------------------------------
    | Retention
    |--------------------------------------------------------------------------
    | Number of backups to keep. Older archives + rows are pruned after each
    | successful backup. Set to 0 to disable pruning.
    */
    'retention' => (int) env('CONFIG_BACKUP_RETENTION', 10),

    /*
    |--------------------------------------------------------------------------
    | Eloquent
    |--------------------------------------------------------------------------
    | The table the ConfigBackup model is stored in, and the host's user model
    | (falls back to the framework's configured auth user model).
    */
    'table' => 'config_backups',
    'user_model' => env('CONFIG_BACKUP_USER_MODEL'),

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    | Gate ability checked before any backup/restore action via the UI/routes.
    | Maps to the admin.manage.config-backup permission seeded from
    | config/access-control.php.
    */
    'gate' => env('CONFIG_BACKUP_GATE', 'admin.manage.config-backup'),

    /*
    |--------------------------------------------------------------------------
    | Database Settings Allowlist
    |--------------------------------------------------------------------------
    | DB-stored settings included in the "database" section. Rows are exported
    | DECRYPTED (via each model's casts) and re-imported through the model so
    | encrypted columns are RE-ENCRYPTED with the destination server's APP_KEY —
    | making a backup portable across servers. Each entry is matched on import
    | via `match` (updateOrCreate).
    */
    'database' => [
        // Application settings managed via spatie/laravel-settings (Admin > Settings).
        'settings' => [
            'model' => SettingsProperty::class,
            'match' => ['group', 'name'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Columns Never Written Back On Import
    |--------------------------------------------------------------------------
    | Auto-managed columns that must not be carried across (re-generated locally).
    */
    'exclude_columns' => ['id', 'created_at', 'updated_at'],

    /*
    |--------------------------------------------------------------------------
    | User Reference Columns
    |--------------------------------------------------------------------------
    | FK columns pointing at users. On import, if the referenced user does not
    | exist on the destination server, the value is remapped to the importing
    | admin to avoid foreign-key violations.
    */
    'user_columns' => ['created_by', 'updated_by', 'user_id'],

    /*
    |--------------------------------------------------------------------------
    | UI Route
    |--------------------------------------------------------------------------
    | The optional Livewire + Flux management screen. Requires livewire/livewire
    | and livewire/flux in the host application.
    */
    'route' => [
        'enabled' => env('CONFIG_BACKUP_ROUTE_ENABLED', true),
        'prefix' => env('CONFIG_BACKUP_ROUTE_PREFIX', 'admin/config-backup'),
        'name' => 'config-backup.index',
        'middleware' => ['web', 'auth'],
        'layout' => env('CONFIG_BACKUP_LAYOUT', 'components.layouts.app'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    | Notify recipients when a backup completes or fails. `mail` is a list of
    | email addresses (comma-separated in the env var).
    */
    'notifications' => [
        'enabled' => env('CONFIG_BACKUP_NOTIFICATIONS', false),
        'mail' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('CONFIG_BACKUP_NOTIFICATION_MAIL', ''))
        ))),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduled Backups
    |--------------------------------------------------------------------------
    | Automatically create a backup on a cron schedule. Requires a password so
    | the archive can be encrypted unattended — store it securely.
    */
    'schedule' => [
        'enabled' => env('CONFIG_BACKUP_SCHEDULE', false),
        'cron' => env('CONFIG_BACKUP_SCHEDULE_CRON', '0 2 * * *'),
        'sections' => ['env', 'database'],
        'password' => env('CONFIG_BACKUP_SCHEDULE_PASSWORD'),
    ],
];
