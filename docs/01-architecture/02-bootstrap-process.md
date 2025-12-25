# Bootstrap Process

This document explains how the `kickoff start` command works internally.

## Command Syntax

```bash
kickoff start <owner> <project-name> [<project-path>]
```

- `owner`: Project owner name (required)
- `project-name`: Name of the project (required)
- `project-path`: Target directory (optional, defaults to current directory)

## Execution Flow

### 1. Argument Validation

The command validates and sets up arguments:

```php
$this->projectOwner = $input->getArgument('owner');
$this->projectName = $input->getArgument('name');
$this->projectPath = $input->getArgument('path') ?? getcwd();
```

### 2. Laravel Project Validation

Checks if target is a valid Laravel project:

- Verifies directory exists
- Checks for `composer.json`
- Validates `artisan` file exists

If validation fails, the command exits with an error.

### 3. Copy Stubs

Copies entire `stubs/` directory to target project:

```php
copyRecursively(__DIR__.'/../stubs/', $this->getProjectPath(), $verbose, $output);
```

All files and directories are recursively copied, preserving structure.

### 4. Setup Composer Configuration

Modifies target project's `composer.json`:

- Adds `support/helpers.php` to autoload files
- Enables Pest plugin in allow-plugins
- Injects custom Composer scripts:
  - `dev`: Concurrent server/queue/logs/vite
  - `analyse`: PHPStan analysis
  - `test`: Pest tests
  - `format`: Laravel Pint formatting
  - `rector`: Code refactoring

### 5. Replace Placeholders

Updates placeholder values in specific files:

**In `bin/` scripts**:

- Replaces `${PROJECT_NAME}` with actual project name

**In `README.md`**:

- Replaces `${PROJECT_NAME}` with project name
- Replaces `${OWNER}` with owner name

**In `.env.example`**:

- Replaces `${PROJECT_NAME}` with project name

### 6. Setup Environment File

Creates `.env` from `.env.example`:

- Copies `.env.example` to `.env`
- Updates `DB_DATABASE` with snake_case version of project name

Database name conversion example:

- `my-project` → `my_project`
- `MyAwesomeApp` → `my_awesome_app`

### 7. Install Packages

Installs required Composer packages:

**Production dependencies**:

- `laravel/sanctum`
- `blade-ui-kit/blade-icons`
- `cleaniquecoders/laravel-media-secure`
- `cleaniquecoders/traitify`
- `diglactic/laravel-breadcrumbs`
- `lab404/laravel-impersonate`
- `laravel/horizon`
- `laravel/telescope`
- `livewire/flux`
- `mallardduck/blade-lucide-icons`
- `owen-it/laravel-auditing`
- `predis/predis`
- `rappasoft/laravel-livewire-tables`
- `spatie/laravel-activitylog`
- `spatie/laravel-medialibrary`
- `spatie/laravel-permission`
- `spatie/laravel-settings`
- `yadahan/laravel-authentication-log`

**Development dependencies**:

- `barryvdh/laravel-debugbar`
- `cleaniquecoders/laravel-db-doc`
- `driftingly/rector-laravel`
- `larastan/larastan`
- `pestphp/pest-plugin-arch`

### 8. Publish Vendor Assets

Publishes package configurations and migrations using multiple vendor tags:

- Auditing config and migrations
- Livewire tables assets
- Activity log
- Laravel settings
- Authentication log
- Blade icons
- Media library
- Permissions
- Sanctum
- Telescope

Also installs NPM dependencies:

- `tippy.js`

### 9. Run Setup Tasks

Executes final setup commands:

```bash
bin/install              # Database setup, migrations
npm run build            # Build frontend assets
php artisan key:generate # Generate app key
php artisan reload:db    # Reload database with seeds
```

## Progress Indicators

Each step shows progress using the `step()` helper:

```text
⏳ Copy application stubs... ✅
⏳ Update composer.json... ✅
⏳ Installing required packages... ✅
```

Failed steps show ❌ with error message.

## Verbose Mode

Use `-v`, `-vv`, or `-vvv` flags for detailed output:

```bash
kickoff start owner name -vv
```

Verbose mode shows:

- Full command output
- File copy operations
- Error traces
- Detailed progress messages

## Error Handling

The command validates at each step:

- Invalid path → Command fails before starting
- Missing Laravel files → Early exit with error
- Package installation failure → Error displayed with details
- Any step failure → ❌ indicator with message

## See Also

- [Overview](./01-overview.md)
- [Stubs System](./03-stubs-system.md)
- [Helper Functions](./04-helper-functions.md)
