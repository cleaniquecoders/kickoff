# Helper Functions

The package provides CLI utility functions in `support/helpers.php` for
command execution and file operations.

## Overview

All helpers are designed for CLI operations and are used exclusively by the
`StartCommand` class during the bootstrap process.

**Important**: These are NOT Laravel application helpers. For Laravel app helpers,
see `stubs/support/helpers.php`.

## Available Functions

### step()

Wraps operations with loading indicators and error handling.

**Signature**:

```php
function step(
    string $message,
    callable $callback,
    OutputInterface $output,
    bool $verbose = false
): void
```

**Purpose**:

- Shows progress indicator (⏳) while operation runs
- Displays success (✅) or failure (❌) indicator
- Handles exceptions with error messages
- Optionally shows verbose output

**Example**:

```php
step('Copy application stubs', function () use ($verbose, $output) {
    copyRecursively(__DIR__.'/../stubs/', $this->getProjectPath(), $verbose, $output);
}, $output, $verbose);
```

**Output**:

```text
⏳ Copy application stubs... ✅
```

**On Error**:

```text
⏳ Copy application stubs... ❌
Error: Directory not found: /invalid/path
```

### runCommand()

Executes shell commands with optional output suppression.

**Signature**:

```php
function runCommand(string $cmd, bool $verbose = false): void
```

**Purpose**:

- Run shell commands during bootstrap
- Suppress output by default
- Show full output in verbose mode

**Example**:

```php
runCommand('composer dump-autoload', $verbose);
runCommand('php artisan vendor:publish --tag=telescope-migrations', $verbose);
```

**Behavior**:

- **Verbose mode** (`$verbose = true`): Uses `passthru()`, shows real-time output
- **Silent mode** (`$verbose = false`): Uses `shell_exec()`, suppresses output

### installPackages()

Installs Composer packages with dependency management.

**Signature**:

```php
function installPackages(
    array $require,
    array $requireDev,
    string $path,
    bool $verbose = false
): void
```

**Purpose**:

- Install production dependencies
- Install development dependencies
- Clean composer.lock before each installation

**Example**:

```php
$require = ['spatie/laravel-permission', 'livewire/flux'];
$requireDev = ['barryvdh/laravel-debugbar', 'larastan/larastan'];

installPackages($require, $requireDev, $this->getProjectPath(), $verbose);
```

**Behavior**:

1. Removes `composer.lock` before installing production packages
2. Runs `composer require` with all production packages
3. Removes `composer.lock` again
4. Runs `composer require --dev` with all dev packages

**Why remove composer.lock?**

Ensures fresh dependency resolution for newly bootstrapped projects.

### copyRecursively()

Recursively copies directory trees with optional verbose logging.

**Signature**:

```php
function copyRecursively(
    string $src,
    string $dst,
    bool $verbose = false,
    ?OutputInterface $output = null
): void
```

**Purpose**:

- Copy entire directory structures
- Preserve file permissions
- Optionally log each file/directory operation

**Example**:

```php
copyRecursively(
    __DIR__.'/../stubs/',
    $this->getProjectPath(),
    $verbose,
    $output
);
```

**Verbose Output**:

```text
Created directory: /path/to/project/app
Copied file: /stubs/app/Models/Base.php to /path/to/project/app/Models/Base.php
Created directory: /path/to/project/config
Copied file: /stubs/config/app.php to /path/to/project/config/app.php
```

**Implementation Details**:

- Uses `RecursiveIteratorIterator` and `RecursiveDirectoryIterator`
- Skips `.` and `..` directories
- Creates directories before copying files
- Preserves relative path structure

### ensureDir()

Creates directory if it doesn't exist.

**Signature**:

```php
function ensureDir(string $path, int $mode = 0755): void
```

**Purpose**:

- Create directories with specific permissions
- Safe to call on existing directories (no error)

**Example**:

```php
ensureDir('/path/to/project/storage/logs');
ensureDir('/path/to/project/bootstrap/cache', 0775);
```

**Behavior**:

- Creates parent directories recursively
- Sets permissions to `0755` by default
- Does nothing if directory already exists

### putFile()

Writes content to a file.

**Signature**:

```php
function putFile(string $path, string $content): void
```

**Purpose**:

- Create or overwrite files
- Simplified file writing wrapper

**Example**:

```php
$composerJson = json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
putFile($composerFile, $composerJson);
```

**Behavior**:

- Overwrites existing files
- Creates new files if they don't exist
- No return value (void)

## Usage Patterns

### Common Workflow

Most helper usage follows this pattern:

```php
step('Description of operation', function () use ($verbose, $output) {
    // Ensure directories exist
    ensureDir('/path/to/target');

    // Copy files
    copyRecursively('/source', '/target', $verbose, $output);

    // Update files
    $content = file_get_contents('/path/to/file');
    $updated = str_replace('placeholder', 'value', $content);
    putFile('/path/to/file', $updated);

    // Run commands
    runCommand('composer install', $verbose);
}, $output, $verbose);
```

### Error Handling

The `step()` function provides automatic error handling:

```php
step('Install packages', function () use ($verbose) {
    // Any exception thrown here is caught by step()
    runCommand('composer require invalid/package', $verbose);
}, $output, $verbose);
// Shows: ⏳ Install packages... ❌
// Error: Package not found
```

### Verbose Mode Integration

All helpers respect the `$verbose` flag:

```bash
# Silent mode (default)
kickoff start owner name

# Verbose mode
kickoff start owner name -v
kickoff start owner name -vv
kickoff start owner name -vvv
```

## Helper vs. Laravel Helpers

**Package Helpers** (`support/helpers.php`):

- Purpose: CLI operations during bootstrap
- Scope: Kickoff package only
- Examples: `step()`, `runCommand()`, `copyRecursively()`

**Stubs Helpers** (`stubs/support/helpers.php`):

- Purpose: Laravel application utilities
- Scope: Generated projects only
- Examples: `current_user()`, `flash()`, `menu()`

**Never confuse these** - they serve different codebases with different purposes.

## See Also

- [Bootstrap Process](./02-bootstrap-process.md)
- [Stubs System](./03-stubs-system.md)
- [Development Guide](../02-development/README.md)
