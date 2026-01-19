# Getting Started

This guide will help you set up your development environment for working on Kickoff.

## Prerequisites

- PHP 8.3 or higher
- Composer
- Git
- Laravel installer (for sandbox testing)

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/cleaniquecoders/kickoff.git
cd kickoff
```

### 2. Install Dependencies

```bash
composer install
```

This installs:

- Production dependencies (Symfony, Laravel components)
- Development dependencies (PHPStan, Rector, Pint, Pest)

### 3. Install Laravel Installer (for testing)

```bash
composer global require laravel/installer
```

Required for sandbox testing workflow.

## Project Structure

```text
kickoff/
├── bin/
│   ├── kickoff          # Main CLI executable
│   └── sandbox          # Testing helper script
├── src/
│   └── StartCommand.php # Bootstrap command
├── support/
│   └── helpers.php      # CLI utilities
├── stubs/               # Laravel project template
├── tests/               # PHPUnit tests
├── test-output/         # Sandbox output (gitignored)
├── composer.json        # Package configuration
├── phpunit.xml          # Test configuration
├── phpstan.neon.dist    # Static analysis config
└── CLAUDE.md            # AI assistant guidance
```

## Development Commands

### Testing

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test
vendor/bin/pest tests/StartCommandTest.php
```

### Code Quality

```bash
# Format code with Laravel Pint
composer lint

# Run static analysis with PHPStan
composer analyse

# Both formatting and analysis
composer lint
```

### Sandbox Testing

```bash
# Create fresh Laravel app and apply kickoff
bin/sandbox run

# Inspect generated project
cd test-output/sandbox
php artisan serve

# Clean up sandbox
bin/sandbox reset
```

## Verifying Installation

### 1. Run Tests

```bash
composer test
```

Expected output:

```text
PASS  Tests\StartCommandTest
✓ command has correct name and description
✓ command has correct arguments configured
✓ get project name returns correct value

Tests:  3 passed
```

### 2. Run Sandbox

```bash
bin/sandbox run
```

Expected output:

```text
Creating Laravel sandbox ...
Sandbox scaffolded: /path/to/kickoff/test-output/sandbox

Running kickoff start for sandbox/sandbox ...

🎉 Let's kickoff your sandbox/sandbox now!

⏳ Copy application stubs... ✅
⏳ Update composer.json... ✅
⏳ Update project name... ✅
⏳ Installing required packages... ✅

🎉 Project setup completed successfully!
```

### 3. Check Generated Project

```bash
cd test-output/sandbox
php artisan list
```

Should show custom commands and packages installed by kickoff.

## Common Issues

### Laravel Installer Not Found

**Error**: `Missing 'laravel' installer`

**Solution**:

```bash
composer global require laravel/installer
```

### Permission Errors

**Error**: `Permission denied: bin/sandbox`

**Solution**:

```bash
chmod +x bin/sandbox bin/kickoff
```

### Composer Lock Conflicts

**Error**: `composer.lock is out of sync`

**Solution**:

```bash
composer update
```

## Next Steps

- [Testing](02-testing.md) - Testing strategies and sandbox workflow
- [Code Style](03-code-style.md) - Formatting and quality guidelines
- [Adding Features](04-adding-features.md) - Learn to extend Kickoff
- [Architecture Overview](../01-architecture/01-overview.md) - Understand package structure
