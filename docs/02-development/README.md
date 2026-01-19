# Development

This section contains information for developers working on the Kickoff package.

## Overview

This guide covers everything you need to know to develop and extend the Kickoff package, including
setting up your environment, testing strategies, and code quality standards.

## Table of Contents

### [1. Getting Started](01-getting-started.md)

Setup your development environment, install dependencies, and verify installation.

### [2. Testing](02-testing.md)

Testing strategies, PHPUnit usage, and sandbox workflow for integration testing.

### [3. Code Style](03-code-style.md)

Code formatting with Laravel Pint, static analysis with PHPStan, and style guidelines.

### [4. Adding Features](04-adding-features.md)

How to extend Kickoff: adding packages, stubs, placeholders, and setup steps.

## Quick Start

```bash
# Clone repository
git clone https://github.com/cleaniquecoders/kickoff.git
cd kickoff

# Install dependencies
composer install

# Run tests
composer test

# Run linting
composer lint

# Test with sandbox
bin/sandbox run
```

## Development Workflow

1. Make changes to package code or stubs
2. Test changes with `bin/sandbox run`
3. Inspect generated project in `test-output/sandbox/`
4. Run tests with `composer test`
5. Format code with `composer lint`
6. Clean up with `bin/sandbox reset`

## Key Differences from Generated Projects

**This package uses**:

- PHPUnit for testing (not Pest)
- Symfony Console (not Laravel)
- CLI helpers in `support/helpers.php`

**Generated projects use**:

- Pest for testing
- Laravel framework
- Application helpers in `stubs/support/helpers.php`

## Related Documentation

- [Architecture](../01-architecture/README.md)
- [Contributing](../03-contributing/README.md)
- [CLAUDE.md](../../CLAUDE.md)
