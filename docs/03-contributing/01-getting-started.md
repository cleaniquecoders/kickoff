# Getting Started with Contributing

This guide walks you through setting up your environment for contributing to Kickoff.

## Fork and Clone

### 1. Fork Repository

Visit [github.com/cleaniquecoders/kickoff](https://github.com/cleaniquecoders/kickoff)
and click "Fork".

### 2. Clone Your Fork

```bash
git clone https://github.com/YOUR-USERNAME/kickoff.git
cd kickoff
```

### 3. Add Upstream Remote

```bash
git remote add upstream https://github.com/cleaniquecoders/kickoff.git
```

## Setup Development Environment

### 1. Install Dependencies

```bash
composer install
```

### 2. Install Laravel Installer

Required for sandbox testing:

```bash
composer global require laravel/installer
```

### 3. Verify Setup

```bash
# Run tests
composer test

# Run sandbox
bin/sandbox run

# Check generated project
cd test-output/sandbox
```

## Create Feature Branch

### 1. Update Main Branch

```bash
git checkout main
git pull upstream main
```

### 2. Create Branch

Use descriptive branch names:

```bash
# For features
git checkout -b feature/add-package-support

# For bug fixes
git checkout -b fix/placeholder-replacement

# For documentation
git checkout -b docs/improve-architecture-guide
```

## Make Changes

### 1. Code Changes

Make your changes following our [code style](../02-development/03-code-style.md).

### 2. Run Quality Checks

```bash
# Format code
composer lint

# Run static analysis
composer analyse

# Run tests
composer test
```

### 3. Test with Sandbox

```bash
# Test end-to-end
bin/sandbox run

# Inspect results
cd test-output/sandbox
# Verify your changes work

# Clean up
cd ../..
bin/sandbox reset
```

## Commit Changes

### Commit Message Format

Follow conventional commits:

```text
type(scope): description

[optional body]

[optional footer]
```

**Types**:

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style (formatting, no logic change)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

**Examples**:

```bash
git commit -m "feat: add support for custom environment variables"
git commit -m "fix: resolve placeholder replacement in nested files"
git commit -m "docs: improve architecture documentation"
```

### Commit Best Practices

- Keep commits focused and atomic
- Write clear, descriptive messages
- Reference issues when applicable: `fixes #123`
- Test each commit individually if possible

## Push Changes

```bash
git push origin your-branch-name
```

## Common Workflows

### Adding a New Package

```bash
# 1. Create branch
git checkout -b feature/add-package-name

# 2. Edit src/StartCommand.php
# Add package to $require or $requireDev array

# 3. Test with sandbox
bin/sandbox run
cd test-output/sandbox
composer show | grep package-name

# 4. Commit
git add src/StartCommand.php
git commit -m "feat: add package-name to default packages"

# 5. Push
git push origin feature/add-package-name
```

### Fixing a Bug

```bash
# 1. Create branch
git checkout -b fix/bug-description

# 2. Add test that reproduces bug
# Edit tests/StartCommandTest.php

# 3. Fix the bug
# Edit relevant files

# 4. Verify fix
composer test

# 5. Test with sandbox
bin/sandbox run

# 6. Commit
git add .
git commit -m "fix: resolve bug-description"

# 7. Push
git push origin fix/bug-description
```

### Updating Documentation

```bash
# 1. Create branch
git checkout -b docs/improve-section

# 2. Edit documentation files
# docs/**/*.md

# 3. Verify changes
# Check formatting and links

# 4. Commit
git add docs/
git commit -m "docs: improve section-name documentation"

# 5. Push
git push origin docs/improve-section
```

## Next Steps

After pushing your changes:

1. [Create a Pull Request](./02-pull-request-process.md)
2. Wait for [Code Review](./03-code-review.md)
3. Address feedback if needed
4. Wait for merge

## Getting Help

- **Questions**: Open a GitHub Discussion
- **Bugs**: Create an issue with reproduction steps
- **Feature Ideas**: Open an issue for discussion

## See Also

- [Pull Request Process](./02-pull-request-process.md)
- [Code Review Guidelines](./03-code-review.md)
- [Development Guide](../02-development/README.md)
