# Architecture Overview

Kickoff is a Symfony Console application distributed as a global Composer package
that bootstraps new Laravel projects with opinionated conventions.

## Package Type

- **Distribution**: Global Composer package
- **Framework**: Symfony Console
- **Purpose**: Automate Laravel project setup
- **Entry Point**: `bin/kickoff` executable

## Core Components

### 1. CLI Binary (`bin/kickoff`)

Symfony Console application entry point that registers and runs the `StartCommand`.

### 2. StartCommand (`src/StartCommand.php`)

Main command class that orchestrates the entire bootstrap process:

- Validates target Laravel project
- Copies stub files
- Updates configuration files
- Installs dependencies
- Runs setup tasks

### 3. Helper Functions (`support/helpers.php`)

Utility functions for CLI operations:

- `step()`: Progress indicators for operations
- `runCommand()`: Shell command execution
- `installPackages()`: Composer package installation
- `copyRecursively()`: Directory tree copying
- `ensureDir()`: Directory creation
- `putFile()`: File writing

### 4. Stubs Directory (`stubs/`)

Complete Laravel project template that gets copied to target projects.
Contains all files, configurations, and scripts for generated projects.

## Package Structure

```text
kickoff/
├── bin/
│   ├── kickoff          # CLI executable
│   └── sandbox          # Testing helper
├── src/
│   └── StartCommand.php # Main command
├── support/
│   └── helpers.php      # CLI utilities
├── stubs/               # Project template
│   ├── app/
│   ├── routes/
│   ├── resources/
│   ├── bin/
│   ├── support/
│   └── ...
├── tests/               # PHPUnit tests
└── composer.json        # Package config
```

## Two Separate Codebases

**Important**: Kickoff manages two distinct codebases:

### Package Codebase

- Location: `src/`, `support/`, `bin/kickoff`
- Purpose: Bootstrap automation
- Testing: PHPUnit
- Helpers: CLI utilities (step, runCommand, etc.)

### Stubs Codebase

- Location: `stubs/`
- Purpose: Laravel project template
- Testing: Pest (for generated projects)
- Helpers: Laravel app helpers (user, flash, media, etc.)

Do not confuse package helpers with stubs helpers - they serve different purposes.

## Dependencies

### Required

- `illuminate/filesystem`: Laravel file operations
- `illuminate/support`: Laravel support utilities
- `symfony/console`: CLI framework
- `symfony/process`: Process execution
- `symfony/polyfill-mbstring`: String handling

### Development

- `larastan/larastan`: PHPStan with Laravel support
- `driftingly/rector-laravel`: Rector with Laravel rules
- `laravel/pint`: Code formatting
- `pestphp/pest`: Test runner

## Next Steps

- [Bootstrap Process](02-bootstrap-process.md) - Learn how the kickoff command executes
- [Stubs System](03-stubs-system.md) - Understand the template system
- [Helper Functions](04-helper-functions.md) - Explore CLI utility functions
