# Kickoff - Laravel Project Bootstrap CLI

A Composer package that provides a CLI tool to bootstrap new Laravel projects with opinionated conventions, packages, and project structure.

## Architecture Overview

**Package Type**: Symfony Console application distributed as global Composer package
**Purpose**: Automate setup of new Laravel projects with pre-configured packages, stubs, and workflows
**Entry Point**: `bin/kickoff` executable
**Main Command**: `StartCommand` in `src/StartCommand.php`

### Key Components

1. **CLI Binary** (`bin/kickoff`): Symfony Console application entry point
2. **StartCommand** (`src/StartCommand.php`): Orchestrates the entire setup process
3. **Helper Functions** (`support/helpers.php`): Utilities for file operations and command execution
4. **Stubs Directory** (`stubs/`): Complete template structure copied to target Laravel projects

### How It Works

```bash
kickoff start <owner> <project-name> [<project-path>]
```

The command executes these steps sequentially:
1. Validates target is a Laravel project (checks for `artisan` file)
2. Copies entire `stubs/` directory to project
3. Modifies `composer.json` with custom scripts and autoload rules
4. Updates placeholders (`${PROJECT_NAME}`, `${OWNER}`) in files
5. Installs required Composer packages (Spatie, Laravel Telescope, etc.)
6. Publishes vendor configs and migrations
7. Installs NPM dependencies (tippy.js)
8. Runs project setup scripts (`bin/install`, migrations, asset build)

## Development Conventions

### Code Style

- **PHP Version**: 8.2+ (package supports Laravel 10-12)
- **Formatting**: Use Laravel Pint (`composer format`)
- **Static Analysis**: PHPStan Level 0 (`composer analyse`)
- **Testing**: PHPUnit (NOT Pest - this is the package itself, not generated projects)

### File Organization

- `src/`: Command classes (currently only `StartCommand`)
- `support/`: Helper functions auto-loaded via Composer
- `stubs/`: Complete project template structure
- `tests/`: PHPUnit tests for command functionality
- `bin/`: Executable entry point

### Helper Functions (`support/helpers.php`)

All helpers are designed for CLI operations:

```php
step(string $message, callable $callback, OutputInterface $output, bool $verbose)
// Wraps operations with loading indicators (⏳ → ✅/❌)

runCommand(string $cmd, bool $verbose)
// Executes shell commands with optional output suppression

installPackages(array $require, array $requireDev, string $path, bool $verbose)
// Installs Composer dependencies

copyRecursively(string $src, string $dst, bool $verbose, ?OutputInterface $output)
// Copies directory trees with optional verbose logging

ensureDir(string $path, int $mode = 0755)
// Creates directory if not exists

putFile(string $path, string $content)
// Writes content to file
```

### Placeholder Replacement System

Two placeholders are used throughout stubs:

- `${PROJECT_NAME}`: Replaced with project name argument
- `${OWNER}`: Replaced with owner argument

These appear in:
- `stubs/README.md`
- `stubs/.env.example`
- All `stubs/bin/*` scripts

**Implementation**: `updatePlaceholder()` method in `StartCommand`

## Testing Strategy

### Test File: `tests/StartCommandTest.php`

Uses PHPUnit with mocking to test:
- Command configuration (arguments: owner, name, path)
- Getter methods for project properties
- Mock execution to verify workflow

**Run Tests:**
```bash
composer test          # Runs PHPUnit
composer test-coverage # With coverage report
```

### Testing Approach

Since this is a file-system heavy operation, tests focus on:
1. Command configuration validation
2. Method accessibility and return values
3. Mock-based workflow verification

**Note**: Integration tests that actually create projects are not included (would require Laravel installation)

## Stubs Architecture

The `stubs/` directory contains a **complete Laravel project structure** that gets copied to target projects.

### Critical Stub Files

**Configuration Files:**
- `stubs/rector.php`: PHP 8.3, Laravel 11 level set
- `stubs/pint.json`: Relaxed PHPDoc rules
- `stubs/phpunit.xml`: Test environment settings
- `stubs/tailwind.config.js`: TailwindCSS v4 configuration
- `stubs/docker-compose.yml`: MinIO, Elasticsearch, Redis services

**Project Scripts** (`stubs/bin/`):
- `install`: Creates database, updates .env, runs migrations
- `deploy`: Git-based deployment script
- `backup-app`, `backup-media`: Backup utilities
- `build-fe-assets`, `reinstall-npm`, `update-dependencies`: Build tools

**Custom Stubs** (`stubs/stubs/`):
- `model.stub`: Extends `App\Models\Base` (not Eloquent Model)
- `migration.create.stub`: UUID primary keys
- `pest.stub`: Pest syntax for tests
- `policy.stub`: Standard policy methods

**Helper Functions** (`stubs/support/`):
- Organized by domain: `user.php`, `flash.php`, `media.php`, `menu.php`, etc.
- `helpers.php` uses `require_all_in(__DIR__.'/*.php')` pattern
- All wrapped in `function_exists()` checks

**Documentation Placeholders:**
- `CHANGELOG.md`, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md`
- `docs/` with deployment, development, and standards subdirectories

### Stubs vs Package Structure

**IMPORTANT**: Don't confuse package structure with stubs:
- Package `support/helpers.php`: CLI utilities (step, runCommand, copyRecursively)
- Stubs `stubs/support/helpers.php`: Laravel application helpers (user, flash, media)

## Composer Configuration

### Package Distribution

```json
{
  "bin": ["bin/kickoff"],
  "autoload": {
    "psr-4": { "CleaniqueCoders\\Kickoff\\Console\\": "src/" },
    "files": ["support/helpers.php"]
  }
}
```

**Installation**: `composer global require cleaniquecoders/kickoff`
**Usage**: `kickoff start owner project-name [path]`

### Composer Scripts Injected into Target Projects

The command modifies target project's `composer.json` to add:

```json
{
  "scripts": {
    "dev": "concurrently server, queue, logs, vite",
    "analyse": "phpstan analyse",
    "test": "pest",
    "format": "pint",
    "rector": "rector process"
  }
}
```

## Package Dependencies

### Required
- `illuminate/filesystem`, `illuminate/support`: Laravel components for file operations
- `symfony/console`, `symfony/process`: CLI framework
- `symfony/polyfill-mbstring`: String handling

### Dev Dependencies
- `larastan/larastan`: PHPStan + Laravel
- `driftingly/rector-laravel`: Rector + Laravel rules
- `laravel/pint`: Code formatting
- `phpunit/phpunit`: Testing (NOT Pest for this package)

## Command Execution Flow

### StartCommand::execute() Workflow

```php
1. Validate arguments (owner, name, optional path)
2. Default path to getcwd() if not provided
3. Validate target is Laravel project (artisan exists)
4. copyStubs() → Copy stubs/ to target
5. setupComposer() → Modify composer.json, add scripts
6. setupProjectName() → Replace ${PROJECT_NAME}, ${OWNER} in files
7. setupEnvironmentFile() → Copy .env.example to .env, update DB name
8. installPackages() → Install 15+ Composer packages
9. runTasks() → bin/install, npm build, migrate
```

### Error Handling

- Validates Laravel project before starting
- Each step wrapped in `step()` helper with try-catch
- Outputs ❌ on failure with error message
- Verbose mode shows full errors and traces

## GitHub Actions Workflows

### Automated QA

- **lint.yml**: Runs Laravel Pint on every push
- **phpstan.yml**: Static analysis
- **rector.yml**: Checks for refactoring opportunities
- **run-tests.yml**: PHPUnit on PHP 8.4, Ubuntu
- **update-changelog.yml**: Auto-updates CHANGELOG.md

## Common Development Tasks

### Adding New Packages to Install

Edit `StartCommand::installPackages()`:

```php
$require = [
    'blade-ui-kit/blade-icons',
    'cleaniquecoders/laravel-media-secure',
    // Add here for composer require
];

$requireDev = [
    'barryvdh/laravel-debugbar',
    // Add here for composer require --dev
];
```

### Adding New Stubs

1. Create file in `stubs/` directory
2. Use placeholders: `${PROJECT_NAME}`, `${OWNER}`
3. No code changes needed - `copyRecursively()` handles it

### Modifying Injected Composer Scripts

Edit `StartCommand::setupComposer()` method:

```php
$composer['scripts'] = [
    'dev' => [...],
    'new-script' => 'command here',
];
```

### Adding New Setup Steps

Add method and call it in `execute()`:

```php
private function myNewStep(OutputInterface $output, bool $verbose)
{
    step('Doing something new', function () use ($verbose) {
        // Your setup logic
        runCommand('some-command', $verbose);
    }, $output, $verbose);
}
```

## Testing Guidelines

### Writing New Tests

Use PHPUnit (not Pest) with reflection for testing private properties:

```php
public function test_new_feature()
{
    $command = new StartCommand();
    $reflection = new \ReflectionClass($command);

    $property = $reflection->getProperty('projectName');
    $property->setAccessible(true);
    $property->setValue($command, 'test-value');

    $this->assertEquals('test-value', $command->getProjectName());
}
```

### Mock External Dependencies

Mock file system operations and Symfony I/O when possible:

```php
$command = $this->getMockBuilder(StartCommand::class)
    ->onlyMethods(['execute'])
    ->getMock();
```

## Important Gotchas

1. ❌ **Don't use Pest syntax** - This package uses PHPUnit (Pest is for generated projects)
2. ❌ **Don't modify stubs/vendor/** - These are generated Laravel projects' dependencies
3. ⚠️ **Path handling**: Use absolute paths; `getcwd()` is default for missing path argument
4. ⚠️ **Database naming**: Converts project name to snake_case for DB_DATABASE
5. ⚠️ **Placeholder format**: Must be exact: `${PROJECT_NAME}` and `${OWNER}`
6. ⚠️ **Composer lock**: Gets deleted/regenerated during package installation
7. ⚠️ **Verbose mode**: Use `-v`, `-vv`, or `-vvv` for debugging setup issues

## Project Structure Generated by Kickoff

When users run `kickoff start`, their Laravel project receives:

- **15+ Spatie/Laravel packages** (permission, media, auditing, etc.)
- **Custom Base model** with UUIDs, auditing, media support
- **Helper functions** organized by domain in `support/`
- **GitHub Actions** for lint, test, PHPStan, Rector
- **Docker Compose** for MinIO, Redis, Elasticsearch
- **Deployment scripts** in `bin/`
- **Architecture tests** using Pest
- **TailwindCSS + Livewire** frontend setup
- **Comprehensive documentation** templates

See `stubs/.github/copilot-instructions.md` for full generated project conventions.

---

**Package Repository**: https://github.com/cleaniquecoders/kickoff
**Based On**: [Project Template](https://github.com/nasrulhazim/project-template)
**Maintained By**: CleaniqueCoders (Nasrul Hazim)
