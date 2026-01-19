# Code Style

This guide covers code formatting and quality standards for Kickoff development.

## Code Style Tools

Kickoff uses standard Laravel/PHP tooling:

- **Laravel Pint**: Code formatting
- **PHPStan**: Static analysis
- **Rector**: Code refactoring

## Running Style Checks

### Format Code

```bash
# Auto-fix code style issues
composer lint
```

Runs Laravel Pint with default Laravel preset.

### Static Analysis

```bash
# Run PHPStan analysis
composer analyse
```

Analyzes code for type errors and potential bugs.

### Both

```bash
# Run both formatting and analysis
composer lint
composer analyse
```

## Laravel Pint

### Configuration

No custom configuration - uses Laravel preset defaults.

### What Pint Fixes

- Indentation (4 spaces)
- Line length
- Import organization
- Trailing whitespace
- Method spacing
- Blank lines

### Running Pint

```bash
# Fix all files
vendor/bin/pint

# Check without fixing
vendor/bin/pint --test

# Specific files
vendor/bin/pint src/StartCommand.php
```

## PHPStan

### PHPStan Configuration

Located in `phpstan.neon.dist`:

```neon
includes:
    - vendor/larastan/larastan/extension.neon
parameters:
    level: 0
    paths:
        - src
```

**Level 0**: Basic type checking (more lenient).

### What PHPStan Checks

- Type errors
- Undefined variables
- Invalid method calls
- Missing return types
- Incorrect argument types

### Running PHPStan

```bash
# Analyze with default config
vendor/bin/phpstan analyse

# Verbose output
vendor/bin/phpstan analyse --verbose

# Higher strictness level
vendor/bin/phpstan analyse --level=5
```

## Rector

### Rector Configuration

Located in `rector.php` (if present):

```php
use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src'])
    ->withPhpSets()
    ->withSets([LaravelSetList::LARAVEL_110]);
```

### Running Rector

```bash
# Dry run (preview changes)
vendor/bin/rector process --dry-run

# Apply changes
vendor/bin/rector process
```

## Code Style Guidelines

### PHP Version

- **Minimum**: PHP 8.3
- Use modern PHP features (typed properties, constructor promotion, etc.)

### Naming Conventions

**Classes**:

```php
// PascalCase
class StartCommand extends Command
```

**Methods**:

```php
// camelCase
public function getProjectName(): string
```

**Variables**:

```php
// camelCase
$projectPath = '/path/to/project';
```

**Constants**:

```php
// SCREAMING_SNAKE_CASE
const PLACEHOLDER_PROJECT_NAME = '${PROJECT_NAME}';
```

### Type Hints

Always use type hints for parameters and return types:

```php
// Good
private function updatePlaceholder(string $placeholder, string $file): void
{
    // ...
}

// Bad
private function updatePlaceholder($placeholder, $file)
{
    // ...
}
```

### Documentation

Add docblocks for complex methods:

```php
/**
 * Install Composer packages with dependency management.
 *
 * @param array $require Production packages
 * @param array $requireDev Development packages
 * @param string $path Project path
 * @param bool $verbose Show output
 */
function installPackages(
    array $require,
    array $requireDev,
    string $path,
    bool $verbose = false
): void {
    // ...
}
```

### Imports

- Group imports by type (vendor, then project)
- Remove unused imports
- Use full class names, not aliases (unless necessary)

```php
// Good
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// Bad
use Symfony\Component\Console\{Command\Command, Input\InputArgument};
```

### Array Syntax

Use short array syntax:

```php
// Good
$packages = ['spatie/laravel-permission', 'livewire/flux'];

// Bad
$packages = array('spatie/laravel-permission', 'livewire/flux');
```

### String Concatenation

Prefer string interpolation for simple cases:

```php
// Good
$message = "Project: {$this->projectName}";

// Also good for complex concatenation
$path = $this->getProjectPath() . '/composer.json';

// Bad
$message = 'Project: ' . $this->projectName;
```

## File Organization

### Method Order

Within classes, order methods logically:

1. Constructor
2. Public methods
3. Protected methods
4. Private methods

```php
class StartCommand extends Command
{
    // Properties
    protected string $projectName;

    // Configuration
    protected function configure(): void { }

    // Public execution
    protected function execute(...): int { }

    // Private setup methods
    private function validateProject(...): void { }
    private function copyStubs(...): void { }
    private function installPackages(...): void { }
}
```

### File Structure

Each PHP file should have:

1. Opening `<?php` tag (no closing tag)
2. Blank line
3. Namespace declaration
4. Blank line
5. Use statements
6. Blank line
7. Class definition

## CI/CD Integration

Style checks run automatically via GitHub Actions:

**`.github/workflows/lint.yml`**:

```yaml
- name: Run Pint
  run: composer lint
```

**`.github/workflows/phpstan.yml`**:

```yaml
- name: Run PHPStan
  run: composer analyse
```

## Pre-Commit Checklist

Before committing:

- [ ] Run `composer lint` - ensure code is formatted
- [ ] Run `composer analyse` - check for type errors
- [ ] Run `composer test` - verify tests pass
- [ ] Test with `bin/sandbox run` - validate end-to-end

## Next Steps

- [Adding Features](04-adding-features.md) - Learn to extend Kickoff
- [Testing](02-testing.md) - Test your changes
- [Contributing Guidelines](../03-contributing/README.md) - Submit your contributions
