# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Kickoff** is a Symfony Console application distributed as a global Composer package that bootstraps new Laravel projects with opinionated conventions, packages, and project structure.

- **Package Type**: CLI tool (Composer global package)
- **Purpose**: Automate setup of fresh Laravel projects with pre-configured packages, stubs, and workflows
- **Entry Point**: `bin/kickoff` executable
- **Main Command**: `StartCommand` in `src/StartCommand.php`

## Common Commands

### Testing

```bash
# Run tests (uses Pest)
composer test

# Run tests with coverage
composer test-coverage

# Run PHPUnit directly
vendor/bin/pest
```

### Code Quality

```bash
# Format code with Laravel Pint
composer lint

# Run static analysis with PHPStan
composer analyse

# Both lint and analyze
composer lint
```

### Development Workflow

```bash
# Test kickoff against a fresh Laravel project
bin/sandbox run

# Clean up sandbox after testing
bin/sandbox reset

# Inspect the generated sandbox project
cd test-output/sandbox
```

## Architecture & Key Concepts

### Package Structure vs. Stubs Structure

**CRITICAL DISTINCTION**: This package has two separate codebases:

1. **The Package Itself** (`src/`, `support/`, `bin/kickoff`):
   - Symfony Console application that does the bootstrapping
   - Helper functions for CLI operations (step, runCommand, copyRecursively)
   - Tests use **PHPUnit** (not Pest)

2. **The Stubs** (`stubs/` directory):
   - Complete Laravel project template that gets copied to target projects
   - Contains its own helpers, routes, models, configs, etc.
   - Generated projects use **Pest** for testing

### How Kickoff Works

When users run `kickoff start <owner> <project-name>`, the command:

1. Validates target is a Laravel project (checks for `artisan` file)
2. Copies entire `stubs/` directory to the target project
3. Modifies target project's `composer.json` (adds scripts, autoload rules)
4. Replaces placeholders (`${PROJECT_NAME}`, `${OWNER}`) in files
5. Installs 15+ Laravel packages (Spatie, Livewire Flux, Telescope, etc.)
6. Publishes vendor configs and migrations
7. Installs NPM dependencies (tippy.js)
8. Runs project setup (migrations, asset build)

### Placeholder System

Two placeholders are used throughout stubs and are replaced during setup:

- `${PROJECT_NAME}`: Replaced with the project name argument
- `${OWNER}`: Replaced with the owner argument

These appear in:

- `stubs/README.md`
- `stubs/.env.example`
- All `stubs/bin/*` scripts

**Implementation**: `updatePlaceholder()` method in `StartCommand`

### Helper Functions (`support/helpers.php`)

These are CLI utilities for the kickoff package itself:

- `step()`: Wraps operations with loading indicators (⏳ → ✅/❌)
- `runCommand()`: Executes shell commands with optional output suppression
- `installPackages()`: Installs Composer dependencies
- `copyRecursively()`: Copies directory trees with optional verbose logging
- `ensureDir()`: Creates directory if not exists
- `putFile()`: Writes content to file

**Do NOT confuse with** `stubs/support/helpers.php` which contains Laravel application helpers for generated projects.

### Testing Strategy

Tests are located in `tests/` and use **PHPUnit** (not Pest):

- `tests/StartCommandTest.php`: Tests command configuration and execution
- Uses mocking to test file-system heavy operations
- Focus on command configuration validation and method accessibility

**Important**: The package itself uses PHPUnit; generated projects use Pest.

## Development Conventions

### Code Style

- **PHP Version**: 8.3+
- **Formatting**: Laravel Pint (`composer lint`)
- **Static Analysis**: PHPStan (`composer analyse`)
- **Testing**: PHPUnit (via Pest test runner, but not Pest syntax in this package)

### File Organization

- `src/`: Command classes (currently only `StartCommand`)
- `support/`: Helper functions auto-loaded via Composer
- `stubs/`: Complete project template structure copied to target projects
- `tests/`: PHPUnit tests for command functionality
- `bin/`: Executable entry points (`kickoff`, `sandbox`)

### Sandbox Testing Workflow

The `bin/sandbox` script provides automated testing:

```bash
# Create fresh Laravel app and apply kickoff
bin/sandbox run

# Inspect the generated project
cd test-output/sandbox

# Clean up when done
bin/sandbox reset
```

**Benefits**:

- Automated testing workflow (30 seconds vs 10 minutes manually)
- Git-safe (test-output/ is ignored)
- Repeatable testing cycles
- Isolated test environment

**Requirements**: Laravel installer must be globally installed (`composer global require laravel/installer`)

## Stubs Architecture

The `stubs/` directory contains a **complete Laravel project structure** that gets copied to target projects.

### Key Stub Components

**Configuration Files**:

- `stubs/rector.php`: PHP 8.3, Laravel 11 level set
- `stubs/pint.json`: Relaxed PHPDoc rules
- `stubs/phpunit.xml`: Test environment settings
- `stubs/tailwind.config.js`: TailwindCSS v4 configuration
- `stubs/docker-compose.yml`: MySQL, Redis, Mailpit, Meilisearch, MinIO services

**Project Scripts** (`stubs/bin/`):

- `install`: Creates database, updates .env, runs migrations
- `deploy`: Git-based deployment script
- `backup-app`, `backup-media`: Backup utilities
- `build-fe-assets`, `reinstall-npm`, `update-dependencies`: Build tools

**Custom Stubs** (`stubs/stubs/`):

- `model.stub`: Extends `App\Models\Base` (not Eloquent Model)
- `migration.create.stub`: Dual-key pattern (auto-increment `id` + `uuid` column)
- `pest.stub`: Pest syntax for tests
- `policy.stub`: Standard policy methods
- `enum.stub`: Enum with `InteractsWithEnum` trait and `Enum` contract
- `helper.stub`: Helper function template

**Helper Functions** (`stubs/support/`):

- Organized by domain: `user.php`, `flash.php`, `media.php`, `menu.php`, `toast.php`, etc.
- 16 domain-specific files auto-loaded via `support/helpers.php`

**AI Coding Instructions**:

- `stubs/CLAUDE.md`: Primary conventions guide for generated projects — models, enums, Livewire patterns, gotchas, DO/DON'T rules. This is the most critical file for AI-assisted development in generated projects.
- `stubs/.claude/settings.local.json`: Claude Code local settings for generated projects
- `stubs/.github/copilot-instructions.md`: GitHub Copilot guide (models, Livewire, routes, testing)

**GitHub Resources** (`stubs/.github/`):

- `workflows/`: CI pipelines (run-tests, lint, rector, update-changelog)
- `instructions/`: 7 coding instruction files (performance, backend, frontend, code-review, database, security, testing)
- `prompts/`: 8 Claude prompts (uat, setup-component, gen-docs, debug-issue, refactor-code, write-tests, code-review)
- `chatmodes/`: Claude chat modes (architect, reviewer, debugger)
- `ISSUE_TEMPLATE/`: Bug report and feature request templates
- `pull_request_template.md`: PR template

**Documentation** (`stubs/docs/`):

- `01-getting-started/`: Installation, setup, Docker
- `02-development/`: 13 development guides (database, Livewire, API, commands, datatable, access control, upload, secure files, sidebar, toast notifications)
- `03-architecture/`: Code quality guide
- `04-deployment/`: Deployment guide

## Modifying Kickoff Behavior

### Adding New Packages to Install

Edit `StartCommand::installPackages()`:

```php
$require = [
    'spatie/laravel-permission',
    // Add new packages here for composer require
];

$requireDev = [
    'barryvdh/laravel-debugbar',
    // Add dev packages here for composer require --dev
];
```

### Adding New Stubs

1. Create file in `stubs/` directory
2. Use placeholders: `${PROJECT_NAME}`, `${OWNER}` where needed
3. No code changes required - `copyRecursively()` handles it automatically

### Modifying Composer Scripts Injected into Projects

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

## Failure Behavior & Debugging

### Error Handling

- `step()` wraps each phase with loading indicators. **Critical steps** (default) re-throw exceptions and halt execution. Non-critical steps log the error and continue.
- `runCommand()` checks exit codes — any non-zero exit throws `RuntimeException` with the failed command and output.
- `validateProject()` returns `false` if target is not a valid Laravel project (no `artisan` file), causing `Command::FAILURE`.

### Debugging Failed Runs

Use verbose flags to see full output from each step:

```bash
# Show command output
kickoff start owner project-name -v

# Show detailed debug info
kickoff start owner project-name -vvv
```

Common failure points:
- **Package installation**: Composer version conflicts — check output with `-v`
- **Laravel creation**: Missing `laravel` installer — `composer global require laravel/installer`
- **NPM install**: Node version mismatch or network issues
- **Vendor publish**: Package not installed or provider class changed

### Recovery

If a run fails midway, the project directory still exists with partial setup. You can:
1. Fix the issue (e.g., install missing dependency)
2. Re-run `kickoff start` — it will skip Laravel creation if `artisan` exists
3. Or delete the directory and start fresh

## Important Gotchas

1. **Testing Framework**: The package uses PHPUnit (not Pest) - Pest is for generated projects only
2. **Path Handling**: Use absolute paths; `getcwd()` is default when path argument is omitted
3. **Database Naming**: Project names are converted to snake_case for DB_DATABASE
4. **Placeholder Format**: Must be exact: `${PROJECT_NAME}` and `${OWNER}`
5. **Composer Lock**: Gets deleted/regenerated during package installation
6. **Verbose Mode**: Use `-v`, `-vv`, or `-vvv` for debugging setup issues
7. **Validation**: Always validates target is a Laravel project (checks for `artisan` file)

## Generated Project Stack

Projects created with `kickoff start` receive:

**Backend**:

- Laravel 12+, PHP 8.4+
- 15+ packages: Spatie (permission, media, settings, activity log), Laravel (Sanctum, Horizon, Telescope), Livewire Flux
- Custom Base model with dual-key pattern (id + uuid), auditing, media support
- Helper functions organized by domain

**Frontend**:

- Vite + TailwindCSS v4
- Livewire 4 + Alpine.js
- Blade Lucide Icons
- Tippy.js for tooltips

**Infrastructure**:

- GitHub Actions for lint, test, PHPStan, Rector
- Docker Compose for MySQL, Redis, Mailpit, Meilisearch, MinIO
- Deployment scripts in `bin/`
- Architecture tests using Pest Arch

See `stubs/CLAUDE.md` for the complete generated project conventions (primary source of truth),
and `stubs/.github/copilot-instructions.md` for GitHub Copilot-specific guidance.

## Workflow Conventions

### Issue Tracking

All bugs, features, and improvements **must** have a GitHub issue created before work begins.
Use `gh issue create` to create issues with appropriate labels and descriptions.
Reference issues in commit messages and PRs.

### Labels

- `bug`: Something broken or inconsistent
- `enhancement`: New feature or improvement
- `documentation`: Documentation gaps or corrections
- `stubs`: Issues specific to the generated project template
- `package`: Issues specific to the kickoff CLI tool itself
