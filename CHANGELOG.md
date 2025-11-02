# Changelog

All notable changes to `kickoff` will be documented in this file.

## Laravel Sanctum - 2025-11-02

Added Laravel Sanctum

## 1.3.3 - 2025-11-02

### Release Notes - Kickoff v1.3.3

**Release Date:** November 2, 2025
**Package:** cleaniquecoders/kickoff

#### 📝 Overview

Version 1.3.3 is a documentation-focused release that enhances developer experience by providing comprehensive GitHub Copilot integration and improved project understanding.

#### ✨ What's New

##### 🤖 GitHub Copilot Integration

- **NEW:** Added comprehensive GitHub Copilot instructions file (`.github/copilot-instructions.md`)
  - Complete architecture documentation for AI-assisted development
  - Detailed command execution flow documentation
  - Helper function reference guide
  - Testing strategy and guidelines
  - Stub architecture explanation
  - Common development tasks with code examples
  - Important gotchas and best practices
  

##### 📚 Documentation Improvements

- Enhanced developer onboarding with AI-powered code assistance
- Comprehensive package architecture documentation
- Clear separation between package structure and generated project stubs
- Detailed explanation of placeholder replacement system
- Added examples for extending functionality

#### 🎯 Benefits

##### For Contributors

- Faster onboarding with AI-assisted code understanding
- Clear guidelines for adding new features
- Comprehensive testing patterns documented
- Better understanding of stub vs package structure

##### For Users

- Better understanding of what Kickoff generates
- Clear documentation of all helper functions
- Improved troubleshooting with detailed workflow docs

#### 📦 Package Information

- **Supported Laravel Versions:** 10.x, 11.x, 12.x
- **PHP Version:** ^8.2
- **Installation:** `composer global require cleaniquecoders/kickoff`
- **Usage:** `kickoff start <owner> <project-name> [<project-path>]`

#### 🔧 Technical Details

##### File Changes

- Added: `.github/copilot-instructions.md` (10,720 lines of comprehensive documentation)

##### No Breaking Changes

This release is purely additive and does not introduce any breaking changes.

#### 📖 Documentation Coverage

The new Copilot instructions document covers:

1. **Architecture Overview** - Package structure and execution flow
2. **Development Conventions** - Code style, formatting, and testing
3. **Helper Functions** - Complete API reference for CLI utilities
4. **Stubs Architecture** - Explanation of template system
5. **Composer Configuration** - Package distribution and scripts
6. **Command Execution Flow** - Step-by-step workflow breakdown
7. **Testing Guidelines** - PHPUnit patterns and best practices
8. **Common Development Tasks** - How-to guides for extending Kickoff
9. **Important Gotchas** - Common pitfalls and warnings

#### 🔗 Links

- **Repository:** [https://github.com/cleaniquecoders/kickoff](https://github.com/cleaniquecoders/kickoff)
- **Full Changelog:** [https://github.com/cleaniquecoders/kickoff/compare/1.3.2...1.3.3](https://github.com/cleaniquecoders/kickoff/compare/1.3.2...1.3.3)
- **Issues:** [https://github.com/cleaniquecoders/kickoff/issues](https://github.com/cleaniquecoders/kickoff/issues)

#### 🙏 Credits

**Maintained by:** CleaniqueCoders (Nasrul Hazim)
**Based on:** [Project Template](https://github.com/nasrulhazim/project-template)

#### 📦 Installation

##### Global Installation (Recommended)

```bash
composer global require cleaniquecoders/kickoff


```
##### Update from Previous Version

```bash
composer global update cleaniquecoders/kickoff


```
#### 🚀 Quick Start

After installation, create a new Laravel project and run:

```bash
cd your-laravel-project
kickoff start your-owner your-project-name


```
For verbose output:

```bash
kickoff start your-owner your-project-name -vvv


```
#### 🔮 What's Next?

See our [TODO list](https://github.com/cleaniquecoders/kickoff/blob/main/todo.md) for upcoming features:

- Rollback mechanism for failed setups
- Interactive package selection mode
- Custom stub directory support
- Laravel 12 compatibility testing
- Integration test suite

#### 📝 Upgrade Notes

No action required for upgrading from v1.3.2 to v1.3.3. Simply update the package globally.


---

**Previous Release:** [v1.3.2](https://github.com/cleaniquecoders/kickoff/releases/tag/1.3.2) - October 21, 2025

## 1.3.2 - 2025-10-21

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.3.1...1.3.2

## 1.3.1 - 2025-10-21

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.3.0...1.3.1

## Improvement on Menu, Sidebar and Components. - 2025-10-21

**Release Date:** October 21, 2025

### About Kickoff

Kickoff helps configure your new Laravel project with good practices, providing a comprehensive set of stubs, configurations, and utilities to jumpstart Laravel application development.

### What's New in v1.3.0

#### 🔄 Updates

- Updated project stubs and access control configurations
- Update Access Control naming conventions.
- Improve documentations
- Added Default GitHub Copilot Instructions
- Improve Menu builder with header label & icon, and also authorization.

## 1.2.7 - 2025-08-14

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.6...1.2.7

## Update stubs - 2025-08-14

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.5...1.2.6

## 1.2.5 - 2025-08-06

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.4...1.2.5

## 1.2.4 - 2025-08-06

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.3...1.2.4

## 1.2.3 - 2025-08-06

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.2...1.2.3

## 1.2.2 - 2025-08-06

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.1...1.2.2

## 1.2.1 - 2025-08-03

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.0...1.2.1
