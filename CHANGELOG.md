# Changelog

All notable changes to `kickoff` will be documented in this file.

## Livewire Flux Integration & Development Tooling  - 2025-11-08

### 📋 Summary

The **version 1.4.0** introduces Livewire Flux package integration, refactors card components to use a new structured approach, and adds a comprehensive sandbox testing environment with Laravel Workbench. This represents a significant frontend modernization and a major improvement to the package development workflow.

### 📥 Installation

```bash
composer global require cleaniquecoders/kickoff

```
### 🔗 Links

- **Full Changelog:** https://github.com/cleaniquecoders/kickoff/releases/tag/v1.4.0
- **Repository:** https://github.com/cleaniquecoders/kickoff

### 🎯 Type of Change

- ✨ Feature: Livewire Flux package integration
- 🔧 Refactor: Card component restructuring
- 🛠️ **Dev Tools: Sandbox testing environment**
- 📝 Documentation: Icon component additions

### 🔨 Technical Changes

#### 1. **Sandbox Testing Environment** ⭐ NEW

- **New Script**: sandbox - Automated testing utility for package development
- **Purpose**: Test `kickoff start` repeatedly without polluting git history
- **Git Integration**: Automatically manages `skip-worktree` flag for test-output
- **Test Directory**: sandbox - Isolated Laravel project for testing

**Sandbox Commands:**

```bash
bin/sandbox run          # Create fresh Laravel app + run kickoff start
bin/sandbox reset        # Delete sandbox and start clean

```
**Key Features:**

- **Automated Workflow**: Creates Laravel app, applies kickoff, ready for inspection
- **Git-Safe**: Uses `skip-worktree` to prevent accidental commits of test files
- **Repeatable**: Fast iteration cycle for stub testing
- **Smart Cleanup**: Removes old sandbox before creating new one
- **Validation**: Checks for required Laravel installer and kickoff binary

**Developer Benefits:**

- Test package changes instantly without manual Laravel setup
- No more accidental commits of test-output changes
- Fast feedback loop during stub development
- Consistent testing environment for all contributors

#### 2. **Livewire Flux Integration**

- Integrated Livewire Flux package for enhanced UI components
- Provides modern, reactive component library for Laravel projects
- Improves developer experience with pre-built UI components

#### 3. **Card Component Refactoring**

- Introduced new `x-card` component structure
- Separated card into subcomponents:
  - `x-card` (main wrapper)
  - `x-card.header` (header section)
  - `x-card.body` (body content)
  - `x-card.footer` (footer actions)
  
- Updated views to use new component structure:
  - index.blade.php
  - show.blade.php
  - show.blade.php
  - show.blade.php
  

#### 4. **Icon Components Update**

- Added 13 custom Flux icon components in icon:
  - Lucide icon set (arrow-right-left, book-open-text, bug, chevrons-up-down, etc.)
  - System icons (settings, layout-dashboard, shield-check, log-out)
  - Navigation icons (layout-grid, folder-git-2, gauge)
  
- Replaced `@pure` directive with `@blaze` for better Flux compatibility
- All icons follow consistent Lucide design patterns

#### 5. **Development Tooling**

- **Laravel Workbench**: Added testbench.yaml configuration
- **Purpose**: Package development in isolation
- **Integration**: Enables testing kickoff as if it were installed globally
- Improved contributor onboarding with clear testing path

### 📁 Key Files Added/Modified

**New Files:**

- sandbox - **Sandbox testing script (183 lines)**
- testbench.yaml - Laravel Workbench configuration
- `stubs/resources/views/flux/icon/*.blade.php` - 13 new icon components
- `stubs/resources/views/components/card/*.blade.php` - 3 new subcomponents

**Modified Files:**

- card.blade.php - Main card component
- Multiple admin/livewire views updated with new card structure

### ✅ Benefits

1. **Massive DX Improvement**: Sandbox script reduces testing time from minutes to seconds
2. **Better Component Organization**: Structured card components improve maintainability
3. **Modern UI Framework**: Flux provides enterprise-grade components
4. **Git Hygiene**: Skip-worktree prevents test pollution in git history
5. **Contributor-Friendly**: New developers can test changes immediately with `bin/sandbox run`
6. **Consistency**: Standardized component patterns across the application
7. **Professional Workflow**: Matches industry best practices for package development

### 🧪 Testing Workflow

**Before (Manual):**

```bash
# 5-10 minutes per test cycle
cd ~/Projects
laravel new test-project
cd test-project
# manually edit composer.json to add local kickoff
composer install
kickoff start owner project
# inspect changes
# delete entire project
# repeat

```
**After (Automated):**

```bash
# 30 seconds per test cycle
bin/sandbox run          # Creates Laravel + applies kickoff
# inspect test-output/sandbox
bin/sandbox reset        # Clean slate
# repeat instantly

```
**Testing the Sandbox:**

```bash
# Create fresh Laravel app and apply kickoff
bin/sandbox run

# Inspect the generated project
cd test-output/sandbox
# create a database in mysql named `sandbox`

```
Then create tables & seed data:

```bash
php artisan reload:db

```
Run the sandbox app:

```bash
npm run build
php artisan serve

```
To clean up sandbox, run:

```bash
bin/sandbox reset

```
### 📦 Dependencies

**For Generated Projects:**

- Added: Livewire Flux package
- Updated: Related frontend dependencies

**For Package Development:**

- Laravel Workbench (dev)
- Laravel installer (global requirement documented)

### 🔄 Migration Path

**For Package Contributors:**

- Use `bin/sandbox run` instead of manual Laravel project creation
- Test-output directory automatically managed with skip-worktree

**For Generated Projects:**

1. Update card usage from single component to structured format
2. Replace `@pure` with `@blaze` in custom icon components
3. Leverage new Flux components for enhanced UI features

### 📚 Documentation

**Sandbox Documentation:**

- Inline comments in sandbox explain each command
- Usage instructions at script header
- Error messages guide missing dependencies

**Component Documentation:**

- Icon components follow Lucide design system
- Component structure documented in Blade files
- Copilot instructions remain up-to-date

### ⚠️ Breaking Changes

**For Package Users:**

- Card component structure changed (requires template updates)
- Directive change from `@pure` to `@blaze` (may affect custom implementations)

**For Contributors:**

- New testing workflow via sandbox script (old manual method still works)
- Test-output directory now ignored with skip-worktree

### 💡 Developer Experience Highlights

**Sandbox Script Innovation:**

- Solves the long-standing problem of testing package output
- Prevents "oops, committed test files" incidents
- Makes TDD possible for stub development
- Reduces barrier to contribution
- Professional package development workflow

**Impact on Development Speed:**

- **Before**: ~10 min per test iteration (manual setup/teardown)
- **After**: ~30 sec per test iteration (automated)
- **Time Saved**: ~95% reduction in testing overhead
- **Result**: More iterations = better quality stubs

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
