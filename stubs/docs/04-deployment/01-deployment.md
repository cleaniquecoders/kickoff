# Deployment

Deploy the `./bin/deploy` to your server, then you need to add the deployment key, as following.

You may want to trigger the script manually or by webhook (require additional setup which not cover in this repo).

This script will deploy based on **latest tagged**. It won't deploy to any non-tagged. Run the follow command as root user.

## Creating Deployment Key

TLDR, create deployment keys:

```bash
$ ssh-keygen -t ed25519 -C "your@email.com"
> Enter a file in which to save the key (/Users/you/.ssh/id_algorithm):
> Enter passphrase (empty for no passphrase): [Type a passphrase]
> Enter same passphrase again: [Type passphrase again]
$ eval "$(ssh-agent -s)"
$ ssh-add -k /Users/you/.ssh/id_algorithm
$ cat /Users/you/.ssh/id_algorithm.pub
```

Copy the output then add key in [Deploy Keys](https://github.com/${OWNER}/${PROJECT_NAME}/settings/keys)

References:

1. <https://docs.github.com/en/authentication/connecting-to-github-with-ssh/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent>
2. <https://docs.github.com/en/authentication/connecting-to-github-with-ssh/adding-a-new-ssh-key-to-your-github-account>

## Running the Script

From the project root on the server:

```bash
sudo bin/deploy -b main
```

Run it as a script, never `source` it (`. ./deploy`): it uses `set -e`, so a failing step would
close the shell you sourced it into. Overrides: `APP_URL` (health check), `WEB_USER` (default
`nginx`), `PHP_FPM_SERVICE` (reloaded when set), `SKIP_BACKUP=1`.

What a failure means:

| Output | State | Next step |
|---|---|---|
| `pre-deploy database backup failed — nothing has been changed` | Site untouched | Fix `bin/backup-db` (or `SKIP_BACKUP=1`) |
| `❌ Deploy failed at line N` | **Maintenance mode on, on purpose** | Fix the cause, then `php artisan up` or re-run |
| `health check failed … Rolling back` | Code back on the previous commit, site up | Migrations that ran are listed and **not** reversed |

## Production Environment

Before the first deploy, set in `.env`:

```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=redis   # .env.example ships `sync`: jobs then run inside the web request
CACHE_STORE=redis
```

and install the scheduler cron — retention, pruning and Horizon metrics in `routes/console.php`
only run through it:

```cron
* * * * * cd /var/www/${PROJECT_NAME} && php artisan schedule:run >> /dev/null 2>&1
```

## Restoring a Backup

`bin/backup-db` proves the archive is readable and non-empty, not that it restores. Rehearse a
restore into a scratch database regularly:

```bash
gunzip -c /home/${PROJECT_NAME}/backup/db/<file>.sql.gz | mysql -u root -p restore_check
```

## Deploy Operations

This project ships with [`dragon-code/laravel-deploy-operations`](https://github.com/TheDragonCode/laravel-deploy-operations)
for one-off post-deploy tasks that don't belong in migrations — backfills, data fixes,
permission seeding, third-party sync, cache warm-up, etc.

The package works like migrations: each operation runs once per environment, tracked in the
`operations` table.

### Creating a Deploy Operation

```bash
php artisan make:operation backfill_user_uuids
```

This generates a file in `operations/` similar to:

```php
<?php

use DragonCode\LaravelDeployOperations\Operation;

return new class extends Operation
{
    public function up(): void
    {
        // One-off logic — backfill data, sync state, fix records, etc.
    }
};
```

### Running Deploy Operations

The `bin/deploy` script runs `php artisan operations --isolated --no-interaction` automatically after
`php artisan migrate --force`. To run manually:

```bash
# Run all pending operations
php artisan operations

# Run in production (skip confirmation)
php artisan operations --isolated --no-interaction

# Check status
php artisan operations:status

# Rollback the last batch
php artisan operations:rollback

# Re-run all operations from scratch
php artisan operations:fresh
```

### When to Use

| Use Case | Tool |
|---|---|
| Schema change (add column, create table) | **Migration** |
| One-off data backfill or fix | **Deploy Operation** |
| Re-runnable seed data (roles, default settings) | Seeder |
| Code change | Just deploy — no operation needed |

> **Gotcha:** Deploy operations are tracked per-environment in the `operations` table.
> The migration for this table is set up during project bootstrap via
> `php artisan operations:install`. If you clone an existing project and the table is
> missing, run `php artisan operations:install` followed by `php artisan migrate`.

> **Gotcha:** Operations should be **idempotent where possible**. Even though each runs once,
> a failed run mid-execution can leave partial state. Wrap multi-step changes in transactions
> or guard with existence checks.
