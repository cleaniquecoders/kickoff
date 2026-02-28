# Sandbox Testing

Local testing skill for developing and improving Kickoff stubs. All new features,
bug fixes, and improvements MUST be tested in the sandbox before copying back to stubs.

## Quick Reference

| Command | Description |
|---|---|
| `bin/sandbox run` | Create fresh Laravel app + apply kickoff into `test-output/sandbox/` |
| `bin/sandbox reset` | Delete `test-output/sandbox/` for a clean start |

## Development Workflow

### 1. Setup Sandbox

```bash
bin/sandbox run
```

This runs the full kickoff pipeline:
1. Creates fresh Laravel app with `laravel new sandbox --git --livewire --pest --npm --livewire-class-components --no-interaction`
2. Copies all `stubs/` into the Laravel project
3. Updates `composer.json` (autoload, scripts, plugins)
4. Replaces `${PROJECT_NAME}` and `${OWNER}` placeholders
5. Installs 15+ Composer packages and npm packages
6. Publishes vendor configs and migrations
7. Runs `bin/install`, `npm run build`, migrations, seeders
8. Seeds dummy notifications for superadmin

### 2. Develop and Test in Sandbox

Working directory: `test-output/sandbox/`

Test changes directly in the sandbox project:

```bash
# Run the app
cd test-output/sandbox && composer dev

# Run artisan commands
cd test-output/sandbox && php artisan <command>

# Run tests
cd test-output/sandbox && composer test

# Run code quality
cd test-output/sandbox && composer format
cd test-output/sandbox && composer analyse
```

Common things to test:
- **Artisan commands**: `reload:all`, `reload:cache`, `reload:db`, `reload:media`, `seed:dev`, `seed:demo`, `test:generate`
- **UI features**: Login, dashboard, admin panel, roles/permissions, settings, notifications, audit trail, impersonation
- **Livewire components**: Alerts, confirms, notifications bell, role management, user roles
- **Config changes**: Telescope, Horizon, filesystems (MinIO), seeder defaults
- **Routes**: Web routes, admin routes, security routes

### 3. Copy Back to Stubs

Once changes are confirmed working in sandbox, copy them back to `stubs/`:

```bash
# Copy a specific file
cp test-output/sandbox/app/path/to/File.php stubs/app/path/to/File.php

# Copy a directory
cp -r test-output/sandbox/app/Livewire/NewFeature/ stubs/app/Livewire/NewFeature/
```

**Important**: Only copy application code back. Do NOT copy:
- `vendor/`, `node_modules/`, `.env`, `composer.lock`
- Published vendor configs (unless intentionally customized)
- `storage/`, `bootstrap/cache/`
- Any auto-generated files

### 4. Reset and Verify

```bash
# Clean up sandbox
bin/sandbox reset

# Re-run to verify stubs work from scratch
bin/sandbox run
```

## Key Paths

| Path | Description |
|---|---|
| `stubs/` | Source templates — all changes go here after testing |
| `test-output/sandbox/` | Live Laravel project for testing (gitignored) |
| `bin/sandbox` | Sandbox management script |
| `bin/kickoff` | Main kickoff CLI entry point |
| `src/StartCommand.php` | Kickoff pipeline logic |

## Rules

1. **Always test in sandbox first** — never modify stubs blindly
2. **Sandbox is disposable** — `bin/sandbox reset` wipes everything, that's fine
3. **test-output/ is gitignored** — changes there won't be committed
4. **Skip-worktree flag** is auto-set on `test-output` to prevent accidental git tracking
5. **Sandbox owner/name** defaults to `sandbox/sandbox`
6. **Database name** is `sandbox` (snake_case of project name)

## Use Cases

### Adding a New Feature
1. `bin/sandbox run` (if no sandbox exists)
2. Develop the feature in `test-output/sandbox/`
3. Test it works (UI, artisan, tests)
4. Copy working files back to `stubs/`
5. `bin/sandbox reset` then `bin/sandbox run` to verify from scratch

### Fixing a Bug in Stubs
1. Fix the bug directly in `stubs/`
2. `bin/sandbox reset` then `bin/sandbox run`
3. Verify the fix works in the sandbox

### Updating Existing Features
1. `bin/sandbox run` (if no sandbox exists)
2. Make changes in `test-output/sandbox/`
3. Test thoroughly
4. Copy changes back to `stubs/`
5. `bin/sandbox reset` then `bin/sandbox run` to verify

### Testing Config/Environment Changes
1. `bin/sandbox run`
2. Modify configs in `test-output/sandbox/config/`
3. Test with `php artisan config:clear && composer dev`
4. If working, copy config back to `stubs/config/`
