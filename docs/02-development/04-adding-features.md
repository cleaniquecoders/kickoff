# Adding Features

This guide explains how to extend Kickoff with new functionality.

## Common Extension Points

### Adding New Packages to Install

**Location**: `src/StartCommand.php` → `installPackages()` method

**Steps**:

1. Add package to `$require` or `$requireDev` array:

```php
private function installPackages(OutputInterface $output, bool $verbose)
{
    step('Installing required packages', function () use ($verbose) {
        $require = [
            'spatie/laravel-permission',
            'livewire/flux',
            'your/new-package', // Add here
        ];

        $requireDev = [
            'barryvdh/laravel-debugbar',
            'your/new-dev-package', // Or here for dev dependencies
        ];

        installPackages($require, $requireDev, $this->getProjectPath(), $verbose);
    }, $output, $verbose);
}
```

1. If package needs publishing, add to `publishVendorAssets()`:

```php
$tags = [
    '--tag=your-package-config',
    '--tag=your-package-migrations',
];
```

1. Test with sandbox:

```bash
bin/sandbox run
cd test-output/sandbox
composer show | grep your-package
```

### Adding New Stubs

**Location**: `stubs/` directory

**Steps**:

1. Create file in appropriate `stubs/` subdirectory:

```bash
# Example: Add new helper file
touch stubs/support/notification.php
```

1. Add content with placeholders if needed:

```php
<?php

if (! function_exists('notify')) {
    function notify(string $message): void
    {
        // Implementation for ${PROJECT_NAME}
    }
}
```

1. No code changes needed - `copyRecursively()` handles it automatically

2. Test with sandbox:

```bash
bin/sandbox run
cd test-output/sandbox
cat support/notification.php
```

### Adding New Placeholder Types

**Location**: `src/StartCommand.php`

**Steps**:

1. Define constant:

```php
class StartCommand extends Command
{
    const PLACEHOLDER_PROJECT_NAME = '${PROJECT_NAME}';
    const PLACEHOLDER_OWNER = '${OWNER}';
    const PLACEHOLDER_CUSTOM = '${CUSTOM}'; // New placeholder
}
```

1. Add property if needed:

```php
protected string $customValue;
```

1. Capture in `execute()`:

```php
$this->customValue = $input->getOption('custom') ?? 'default-value';
```

1. Add replacement logic:

```php
private function setupProjectName(OutputInterface $output, bool $verbose)
{
    // Existing replacements...

    step('Update custom placeholder', function () {
        $files = glob($this->getProjectPath() . '/config/*');
        foreach ($files as $file) {
            $this->updatePlaceholder(self::PLACEHOLDER_CUSTOM, $file);
        }
    }, $output, $verbose);
}
```

1. Update `updatePlaceholder()` if needed:

```php
private function updatePlaceholder($placeholder, $file)
{
    $replacement = match($placeholder) {
        self::PLACEHOLDER_PROJECT_NAME => $this->getProjectName(),
        self::PLACEHOLDER_OWNER => $this->getProjectOwner(),
        self::PLACEHOLDER_CUSTOM => $this->customValue,
    };

    // Replace in file...
}
```

### Adding New Setup Steps

**Location**: `src/StartCommand.php` → `execute()` method

**Steps**:

1. Create new private method:

```php
private function setupCustomFeature(OutputInterface $output, bool $verbose)
{
    step('Setup custom feature', function () use ($verbose) {
        // Your setup logic here
        runCommand('php artisan your:command', $verbose);

        // Or file operations
        $content = file_get_contents($this->getProjectPath() . '/config/app.php');
        $updated = str_replace('old', 'new', $content);
        putFile($this->getProjectPath() . '/config/app.php', $updated);
    }, $output, $verbose);
}
```

1. Call in `execute()` workflow:

```php
protected function execute(InputInterface $input, OutputInterface $output): int
{
    // ... existing setup ...

    $this->copyStubs($output, $verbose);
    $this->setupComposer($output, $verbose);
    $this->setupProjectName($output, $verbose);
    $this->setupCustomFeature($output, $verbose); // Add here
    $this->installPackages($output, $verbose);

    return Command::SUCCESS;
}
```

1. Test:

```bash
bin/sandbox run -vv  # Verbose to see your step
```

### Adding Composer Scripts to Generated Projects

**Location**: `src/StartCommand.php` → `setupComposer()` method

**Steps**:

1. Add script to `$composer['scripts']` array:

```php
private function setupComposer(OutputInterface $output, bool $verbose)
{
    step('Update composer.json...', function () use ($verbose) {
        // ... existing code ...

        $composer['scripts'] = [
            'dev' => [...],
            'test' => [...],
            'your-script' => 'your-command here', // Add script
            'complex-script' => [  // Or multi-command
                'command one',
                'command two',
            ],
        ];

        putFile($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }, $output, $verbose);
}
```

1. Test:

```bash
bin/sandbox run
cd test-output/sandbox
composer your-script
```

### Adding Helper Functions

**Location**: `support/helpers.php`

**Steps**:

1. Add function to `support/helpers.php`:

```php
/**
 * Your helper description.
 */
function yourHelper(string $param, bool $verbose = false): void
{
    if ($verbose) {
        echo "Running your helper with: {$param}\n";
    }

    // Implementation
}
```

1. Use in `StartCommand`:

```php
step('Using your helper', function () use ($verbose) {
    yourHelper('value', $verbose);
}, $output, $verbose);
```

1. Test:

```bash
bin/sandbox run -v
```

## Best Practices

### Follow Existing Patterns

Match the style of existing code:

- Use `step()` for operations with progress indicators
- Use `runCommand()` for shell commands
- Respect `$verbose` flag for output control
- Add descriptive step messages

### Test End-to-End

Always test with sandbox:

```bash
# Test your changes
bin/sandbox run -vv

# Inspect results
cd test-output/sandbox
# Check files, run commands, verify behavior

# Clean up
cd ../..
bin/sandbox reset
```

### Update Documentation

When adding features:

1. Update relevant docs in `docs/`
2. Update `CLAUDE.md` if architecture changes
3. Update `CHANGELOG.md`
4. Add tests if applicable

### Maintain Backward Compatibility

- Don't remove existing placeholders
- Don't change stub file paths
- Don't modify helper function signatures
- Add new features, don't break old ones

## Common Patterns

### Pattern: Add Package + Config

```php
// 1. Add to packages
$require = ['vendor/package'];

// 2. Publish config
$tags = ['--tag=package-config'];

// 3. Update config file
step('Configure package', function () {
    $config = $this->getProjectPath() . '/config/package.php';
    $content = file_get_contents($config);
    $updated = str_replace('default', 'custom', $content);
    putFile($config, $updated);
}, $output, $verbose);
```

### Pattern: Add Stub + Process

```php
// 1. Create stub file
// stubs/config/custom.php with placeholders

// 2. Add replacement step
step('Process custom config', function () {
    $file = $this->getProjectPath() . '/config/custom.php';
    $this->updatePlaceholder(self::PLACEHOLDER_PROJECT_NAME, $file);
}, $output, $verbose);
```

### Pattern: Conditional Setup

```php
step('Setup optional feature', function () use ($verbose) {
    $envFile = $this->getProjectPath() . '/.env';
    $content = file_get_contents($envFile);

    if (str_contains($content, 'ENABLE_FEATURE=true')) {
        runCommand('php artisan feature:install', $verbose);
    }
}, $output, $verbose);
```

## See Also

- [Architecture Overview](../01-architecture/README.md)
- [Testing](./02-testing.md)
- [Code Style](./03-code-style.md)
- [Contributing Guidelines](../03-contributing/README.md)
