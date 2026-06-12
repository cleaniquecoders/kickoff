# Artisan Runner

Run allowlisted Artisan commands from the browser via
[cleaniquecoders/laravel-artisan-runner](https://github.com/cleaniquecoders/laravel-artisan-runner):
queued execution, database-logged output, and completion/failure notifications.

## Access — Superadmin Only by Default

The package has **no built-in gate** — access control is entirely via the route middleware.
Kickoff pre-configures it the same way as Horizon/Telescope, but stricter:

- Route middleware (`config/artisan-runner.php`): `['web', 'auth', 'can:access.artisan-runner']`
- Gate `access.artisan-runner` (`AdminServiceProvider`) checks the
  `admin.access.artisan-runner` permission
- That permission is **deliberately not granted to any role** in
  `config/access-control.php` — only `superadmin` (wildcard `*`) has it

To grant another role access, assign the `admin.access.artisan-runner` permission explicitly
(Admin → Roles → Manage Permissions) — a conscious decision, never a default.

The UI lives at `/artisan-runner` (sidebar: Audit & Monitoring → Artisan Runner).

## Command Allowlist

Only commands in `config/artisan-runner.php` → `allowed_commands` can run (default
`discovery_mode` is `manual`). The shipped defaults are safe operations: cache/config/route/view
clears, `migrate` (with explicit flags), `migrate:status`.

Add commands with their parameter schemas:

```php
'allowed_commands' => [
    'app:sync-report' => [
        'label' => 'Sync Reports',
        'description' => 'Rebuild the reporting tables.',
        'group' => 'Reports',
        'parameters' => [
            ['name' => '--month', 'type' => 'string', 'label' => 'Month (YYYY-MM)', 'default' => ''],
        ],
    ],
],
```

`auto` / `selection` discovery modes exist but think carefully before enabling them —
destructive commands (`db:wipe`, `migrate:fresh`, ...) are excluded by default, but an explicit
allowlist remains the safest posture.

## Execution & Logs

Commands run as **queued jobs** (processed by Horizon) and are logged to the `command_logs`
table with output, status, and runtime. Logs are pruned after 30 days
(`log_retention_days`).

## Notifications

On completion/failure, notify via database + mail. Set the recipient:

```env
ARTISAN_RUNNER_NOTIFY_EMAIL=admin@example.com
```
