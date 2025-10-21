# Artisan Commands

You may found the following commands are useful during development:

| Command | Description |
|---------|-------------|
| `php artisan reload:db` | Drop all tables and remigrate and seed database |
| `php artisan reload:cache` | Clear all caches and cache back all caches available |
| `php artisan reload:all` | Run `reload:cache` and `reload:db` |
| `php artisan seed:prepare` | By default this command run `PrepareSeeder` which seed all necessary data for first time deployment. This command run by default in `reload:db` command |
| `php artisan seed:dev` | Seed any data for development purpose. You may configure `DevSeeder` to meet your requirements. |

You may run other make commands which available under `app/Console/Commands/Make` namespace.
