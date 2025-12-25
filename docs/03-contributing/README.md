# Contributing

Thank you for considering contributing to Kickoff! This guide will help you
understand our contribution process.

## Contents

1. [Getting Started](./01-getting-started.md) - How to start contributing
2. [Pull Request Process](./02-pull-request-process.md) - Submitting changes
3. [Code Review](./03-code-review.md) - Review guidelines and expectations

## Quick Contribution Guide

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Run quality checks
6. Submit pull request

## Before You Start

- Check existing issues and PRs to avoid duplicates
- Discuss major changes in an issue first
- Follow our code style and conventions
- Add tests for new features
- Update documentation as needed

## Code Quality Standards

All contributions must pass:

- ✅ Laravel Pint formatting (`composer lint`)
- ✅ PHPStan analysis (`composer analyse`)
- ✅ Test suite (`composer test`)
- ✅ Sandbox validation (`bin/sandbox run`)

## Types of Contributions

### Bug Fixes

- Report bugs via GitHub Issues
- Include reproduction steps
- Provide fix with tests

### New Features

- Discuss in GitHub Issues first
- Explain use case and benefits
- Provide implementation with tests
- Update documentation

### Documentation

- Fix typos and clarify content
- Add examples and guides
- Update outdated information

### Stubs Improvements

- Add useful packages
- Improve default configuration
- Add helpful utilities

## Related Documentation

- [Development Guide](../02-development/README.md)
- [Code Style](../02-development/03-code-style.md)
- [Testing](../02-development/02-testing.md)
