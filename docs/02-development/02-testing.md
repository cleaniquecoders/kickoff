# Testing

This guide covers testing strategies for the Kickoff package.

## Testing Framework

Kickoff uses **PHPUnit** (via Pest test runner, but NOT Pest syntax).

**Important**: Do not confuse with generated projects, which use Pest syntax.

## Test Structure

```text
tests/
├── Feature/           # Feature tests (currently unused)
├── Unit/             # Unit tests (currently unused)
├── Pest.php          # Pest configuration
├── StartCommandTest.php  # Main command tests
└── TestCase.php      # Base test case
```

## Running Tests

### All Tests

```bash
composer test
```

### With Coverage

```bash
composer test-coverage
```

### Specific Test File

```bash
vendor/bin/pest tests/StartCommandTest.php
```

### With Verbose Output

```bash
vendor/bin/pest --verbose
```

## Test Configuration

Configuration in `phpunit.xml`:

```xml
<phpunit bootstrap="vendor/autoload.php" colors="true">
    <testsuites>
        <testsuite name="Test Suite">
            <directory suffix="Test.php">./tests</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
```

## Writing Tests

### Test File Structure

```php
<?php

namespace CleaniqueCoders\Kickoff\Console\Tests;

use CleaniqueCoders\Kickoff\Console\StartCommand;
use PHPUnit\Framework\TestCase;

class StartCommandTest extends TestCase
{
    public function test_command_has_correct_name()
    {
        $command = new StartCommand();
        $this->assertEquals('start', $command->getName());
    }
}
```

### Testing Private Properties

Use reflection to test private properties:

```php
public function test_get_project_name_returns_correct_value()
{
    $command = new StartCommand();
    $reflection = new \ReflectionClass($command);

    $property = $reflection->getProperty('projectName');
    $property->setAccessible(true);
    $property->setValue($command, 'test-project');

    $this->assertEquals('test-project', $command->getProjectName());
}
```

### Mocking External Dependencies

Mock file system operations:

```php
public function test_execute_calls_setup_methods()
{
    $command = $this->getMockBuilder(StartCommand::class)
        ->onlyMethods(['execute'])
        ->getMock();

    $command->expects($this->once())
        ->method('execute');

    // Test logic here
}
```

## Sandbox Testing

The sandbox provides integration testing for the full bootstrap process.

### Sandbox Workflow

```bash
# 1. Create fresh Laravel app and apply kickoff
bin/sandbox run

# 2. Inspect generated project
cd test-output/sandbox

# 3. Verify setup
php artisan list           # Check custom commands
composer test             # Run project tests
npm run build            # Build assets

# 4. Clean up
cd ../..
bin/sandbox reset
```

### What Sandbox Tests

The sandbox validates:

- ✅ Stub files copied correctly
- ✅ Placeholders replaced
- ✅ Packages installed
- ✅ Composer scripts added
- ✅ Environment configured
- ✅ Database migrations work
- ✅ Frontend builds successfully

### Sandbox Script Details

Located at `bin/sandbox`:

**Commands**:

- `bin/sandbox run`: Create and bootstrap Laravel project
- `bin/sandbox reset`: Delete sandbox directory

**Behavior**:

1. Creates Laravel app with recommended flags
2. Runs `kickoff start sandbox sandbox`
3. Leaves project in `test-output/sandbox/`

**Benefits**:

- ✅ Automated testing (30s vs 10min manually)
- ✅ Git-safe (`test-output/` is ignored)
- ✅ Repeatable testing cycles
- ✅ Isolated environment

### Manual Sandbox Testing

You can also test against any Laravel project:

```bash
# Create Laravel project
laravel new my-test --git --livewire --pest --npm --livewire-class-components

# Apply kickoff from source
cd my-test
php /path/to/kickoff/bin/kickoff start testowner testproject

# Inspect results
git status
composer show
cat composer.json
```

## Testing Guidelines

### What to Test

**DO test**:

- Command configuration (name, description, arguments)
- Getter methods
- Input validation
- Error handling

**DON'T test**:

- File system operations (use sandbox instead)
- External package behavior
- Laravel framework features

### Test Coverage

Current test coverage focuses on:

- Command setup and configuration
- Property accessors
- Input argument handling

Integration testing handled by sandbox workflow.

### Adding New Tests

When adding features, add corresponding tests:

1. Create test method in `StartCommandTest.php`
2. Use descriptive test names: `test_feature_does_expected_behavior`
3. Follow AAA pattern: Arrange, Act, Assert
4. Test edge cases and error conditions

Example:

```php
public function test_get_database_name_converts_to_snake_case()
{
    $command = new StartCommand();
    $reflection = new \ReflectionClass($command);

    $property = $reflection->getProperty('projectName');
    $property->setAccessible(true);
    $property->setValue($command, 'MyAwesomeApp');

    $method = $reflection->getMethod('getDatabaseName');
    $method->setAccessible(true);

    $result = $method->invoke($command);

    $this->assertEquals('my_awesome_app', $result);
}
```

## Continuous Integration

Tests run automatically via GitHub Actions on every push.

See `.github/workflows/run-tests.yml`:

```yaml
- name: Run tests
  run: composer test
```

## See Also

- [Getting Started](./01-getting-started.md)
- [Code Style](./03-code-style.md)
- [Adding Features](./04-adding-features.md)
