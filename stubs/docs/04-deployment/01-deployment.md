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

Copy the output then add key in [Deploy Keys](https://github.com/nasrulhazim/project-template/settings/keys)

References:

1. <https://docs.github.com/en/authentication/connecting-to-github-with-ssh/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent>
2. <https://docs.github.com/en/authentication/connecting-to-github-with-ssh/adding-a-new-ssh-key-to-your-github-account>

## Running the Script

Deploy it anywhere in your server, then run:

```bash
sudo su
. ./deploy
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

The `bin/deploy` script runs `php artisan operations --force` automatically after
`php artisan migrate --force`. To run manually:

```bash
# Run all pending operations
php artisan operations

# Run in production (skip confirmation)
php artisan operations --force

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
