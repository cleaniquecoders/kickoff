# Changelog

All notable changes to `kickoff` will be documented in this file.

## Remove Volt Related - 2026-01-22

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.5.3...1.5.4

## Simplify Docker Compose Setup - 2026-01-22

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.5.2...1.5.3

## Added Media Manager Integration - 2026-01-20

### Release Notes: v1.5.1

#### What's New

##### Media Manager Integration

This release adds full integration with the [`cleaniquecoders/media-manager`](https://github.com/cleaniquecoders/media-manager) package, providing a comprehensive media library management system with custom Flux UI components.

<img width="2048" height="646" alt="Media Manager Browser" src="https://github.com/user-attachments/assets/5f7d6a50-2bb5-43fc-b347-c32fbfd7c14e" />
#### Features
##### Media Management Module
- **Sidebar Navigation:** New "Media" section in sidebar with "Media Library" menu item
- **Custom Routes:** Dedicated `/media-manager` route with proper authentication and authorization
- **Access Control:** New permissions for media management (`media.access.management`, `media.upload.files`, `media.delete.files`)

##### Custom Flux-Styled Views

- **Media Browser:** Full-featured media browsing interface with grid and list views
- **Sidebar Filters:** Search, collection filter, type filter, and date range filters
- **Bulk Actions:** Select multiple items for bulk deletion
- **Preview Panel:** Flyout modal for previewing media details (images, videos, audio, PDFs, documents)
- **Empty States:** Polished empty state designs for both initial state and filtered results

##### UI Components Used

- Flux: `heading`, `button`, `button.group`, `input`, `select`, `modal`, `checkbox`, `badge`, `icon`, `callout`
- Tailwind CSS for card-like containers (replacing non-existent `flux:card`)
- Standard HTML tables for list view

#### Files Added

| File | Description |
|------|-------------|
| `stubs/app/Actions/Builder/Menu/MediaManagement.php` | Media menu builder |
| `stubs/routes/web/media.php` | Media manager routes |
| `stubs/resources/views/vendor/media-manager/browser.blade.php` | Main wrapper view |
| `stubs/resources/views/vendor/media-manager/livewire/media-browser.blade.php` | Livewire component view |
| `stubs/resources/views/vendor/media-manager/partials/grid-item.blade.php` | Grid item partial |
| `stubs/resources/views/vendor/media-manager/partials/list-item.blade.php` | List item partial |
| `stubs/resources/views/vendor/media-manager/partials/preview-panel.blade.php` | Preview modal partial |

#### Files Modified

| File | Changes |
|------|---------|
| `stubs/app/Actions/Builder/Menu.php` | Added MediaManagement import and match case |
| `stubs/resources/views/components/layouts/app/sidebar.blade.php` | Added media-management menu |
| `stubs/config/access-control.php` | Added media permissions and administrator role_scope |
| `stubs/app/Providers/AdminServiceProvider.php` | Added media management gates |

#### Dependencies

- Requires `cleaniquecoders/media-manager` ^1.0.1 (Livewire 4 compatible)

#### Upgrade Notes

After updating to v1.5.1, run:

```bash
php artisan reload:db



```
This will seed the new media permissions for your roles.

#### Verification

1. Log in as superadmin
2. Verify "Media" section appears in sidebar
3. Click "Media Library" to access `/media-manager`
4. Test grid/list view toggle, filters, and preview functionality

## 1.5.0 - 2026-01-19

### Release Notes - v1.5.0

**Release Date:** January 20, 2026

#### Overview

Version 1.5.0 is a major release that introduces a complete UI/UX overhaul with new dashboard, welcome page, security management features, notifications system, and improved navigation. This release also includes Livewire 4 upgrade and comprehensive documentation improvements.


---

#### New Features

##### Dashboard Redesign

- **Stats Overview Cards**: Display Total Users, Active Roles, Permissions, and Unread Notifications with visual icons
- **Quick Actions Panel**: One-click access to Edit Profile, Change Password, View Notifications, and Settings
- **Recent Activity Section**: Shows recent audit logs for quick monitoring
- **Personalized Welcome Message**: Greets user by name with contextual information

##### Landing Page (Welcome)

- **Modern Hero Section**: Clean design with "Powered by Kickoff" badge
- **Responsive Navigation**: Fixed navbar with authentication-aware links
- **Features Showcase**: Highlights Livewire 4, TailwindCSS v4, Laravel 12 stack
- **Call-to-Action**: "Get Started" button for new user registration

##### Notifications System

- **Bell Component** (`App\Livewire\Notifications\Bell`):
  
  - Header notification icon with unread count badge (99+ for large counts)
  - Dropdown showing 5 most recent unread notifications
  - Quick mark-as-read from dropdown
  - "View All Notifications" link
  
- **Notifications Management Page** (`App\Livewire\Notifications\Index`):
  
  - Full-page notification center at `/notifications`
  - Filter by: All, Unread, Read
  - Sortable by date (asc/desc)
  - Pagination (15 per page)
  - Bulk "Mark All as Read" action
  - Individual actions: Mark read/unread, Delete with confirmation
  - Visual highlighting for unread notifications
  - URL state persistence for filters and sorting
  
- **NotificationSeeder**: Seeds 5 sample notifications for superadmin user
  

##### Security & User Management

- **User Management** (`/security/users`):
  
  - User listing with roles display
  - Stats: Total Users, Active Today, With Roles, New This Month
  - User detail view with role assignment via `UserRoles` Livewire component
  
- **Role & Permission Management**:
  
  - `RolePermissions` Livewire component for toggling permissions per role
  - Module-based permission grouping with bulk toggle
  - Toast notifications for all actions
  - Authorization checks on all operations
  
- **Audit Trail** (`/security/audit-trail`):
  
  - Activity log viewer with pagination
  - Stats: Total Logs, Created, Updated, Deleted counts
  - Detailed audit view showing old/new values
  - User attribution for each action
  

##### Menu System Refactoring

- **New Structure**:
  
  - `Sidebar.php` - Dashboard and Notifications menu items
  - `UserManagement.php` - User management menu (renamed from Administration)
  - `AuditMonitoring.php` - Audit trail menu (renamed from Support)
  - `Settings.php` - Settings menu items
  
- **Removed**:
  
  - `Security.php` (AccessControl removed)
  - `SidebarFooter.php` (consolidated)
  

##### Layout Improvements

- **Sidebar**: Improved structure with dynamic user authentication elements
- **Header**: New desktop user menu component
- **Auth Layouts**: Added card, simple, and split layout variants
- **Partials**: New `head.blade.php` and `settings-heading.blade.php`

##### Logo & Branding

- **Kickoff Logo Component** (`x-kickoff-logo`): Rocket icon with gradient
- **App Logo Updates**: Uses Kickoff branding with project name
- **App Logo Icon**: Compact icon variant


---

#### Improvements

##### Livewire 4 Upgrade

- Updated all Livewire components to version 4 syntax
- Enhanced settings routes for Livewire 4 compatibility
- Updated profile settings component

##### Documentation

- **CLAUDE.md Added**: Comprehensive project guidance for Claude Code AI
  
  - Model conventions (extend `App\Models\Base`)
  - Database conventions (UUID primary keys)
  - Enum patterns with `InteractsWithEnum`
  - Authorization patterns with Spatie Permission
  - Helper functions reference
  - Testing patterns with Pest
  - Livewire patterns (alerts, confirmations)
  - DO's and DON'Ts checklist
  
- **Documentation Structure Updates**:
  
  - Added "Next Steps" navigation sections
  - Improved architecture overview
  - Enhanced development guides
  - Better contributing guidelines
  

##### Installation Process

- Added Livewire configuration during installation
- Added notifications table migration setup

##### Code Quality

- PHP Linting with Pint applied across codebase
- Improved code readability and maintainability


---

#### Bug Fixes

- Fixed URL for profile completion reminder in NotificationSeeder
- Fixed settings routes for profile and password in dashboard


---

#### Refactoring

- Renamed notification data key from `title` to `subject` for consistency
- Removed obsolete `VoltServiceProvider`
- Removed `AccessControlController` (replaced with Livewire components)
- Consolidated gate definitions for user management and auditing
- Refactored admin views structure (`administration` → `admin`)


---

#### Files Changed

##### New Files (Key)

- `stubs/CLAUDE.md`
- `stubs/app/Livewire/Actions/Logout.php`
- `stubs/app/Livewire/Admin/Roles/Index.php`
- `stubs/app/Livewire/Admin/Roles/Show.php`
- `stubs/app/Livewire/Admin/Settings/Show.php`
- `stubs/app/Livewire/Notifications/Bell.php`
- `stubs/app/Livewire/Notifications/Index.php`
- `stubs/app/Livewire/Security/RolePermissions.php`
- `stubs/app/Livewire/Security/UserRoles.php`
- `stubs/app/Actions/Builder/Menu/Settings.php`
- `stubs/app/Actions/Builder/Menu/Sidebar.php`
- `stubs/database/seeders/NotificationSeeder.php`
- `stubs/resources/views/welcome.blade.php`
- `stubs/resources/views/dashboard.blade.php`
- `stubs/resources/views/notifications/index.blade.php`
- `stubs/resources/views/security/users/index.blade.php`
- `stubs/resources/views/security/users/show.blade.php`
- `stubs/resources/views/security/audit-trail/index.blade.php`
- `stubs/resources/views/security/audit-trail/show.blade.php`
- `stubs/resources/views/components/kickoff-logo.blade.php`
- `stubs/resources/views/components/user-menu.blade.php`
- `stubs/resources/views/components/desktop-user-menu.blade.php`
- `stubs/routes/web/notifications.php`

##### Removed Files

- `stubs/app/Actions/Builder/Menu/Security.php`
- `stubs/app/Actions/Builder/Menu/SidebarFooter.php`
- `stubs/app/Http/Controllers/Security/AccessControlController.php`
- `stubs/app/Providers/VoltServiceProvider.php`
- `stubs/routes/web/security.php`

##### Assets Added

- `assets/dashboard.png`
- `assets/landing-features.png`
- `assets/landing-hero.png`
- `assets/role-permissions.png`
- `assets/settings-email.png`


---

#### Statistics

- **97 files changed**
- **+3,281 lines added**
- **-769 lines removed**


---

#### Upgrade Notes

Projects generated with Kickoff v1.5.0 will automatically include all new features. For existing projects upgrading from v1.4.x:

1. **Menu System**: Update menu builders to new structure
2. **Notifications**: Run `php artisan notifications:table && php artisan migrate`
3. **Livewire**: Update components to Livewire 4 syntax
4. **Routes**: Update security routes to new structure


---

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.4.2...1.5.0

## 1.4.2 - 2025-12-25

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.4.1...1.4.2

## Release Notes - Kickoff v1.4.1 - 2025-12-25

**Release Date:** December 25, 2024

### 🎉 What's New

#### Toast Notification System

- ✨ Added fully functional toast notification component with Alpine.js
- 🎨 Support for 4 notification types: success, error, warning, info
- 🌙 Dark mode support with proper color contrast
- ⚡ Auto-dismiss with configurable duration (default 3000ms)
- 🔄 Smooth animations and transitions

#### Settings Management

- 💾 Settings now persist to `.env` file (environment-based configuration)
  
- ✅ Full validation for all settings sections
  
- 🔐 Authorization with `manage.settings` gate
  
- 📧 **Enhanced Email Settings** with complete SMTP configuration:
  
  - Mail Driver (SMTP, Sendmail, Mailgun, SES, Log)
  - SMTP Host, Port, Username, Password
  - Encryption (TLS, SSL, None)
  - Sender information (From Address, From Name)
  - Organized in 2-column grid layout with helper text showing ENV keys
  
- 📋 Settings sections: General, Email, Notifications
  
- 🎯 Toast notifications for save/update feedback
  

#### Application Branding

- 🚀 New app logo component with Kickoff rocket icon
- 🎨 Dynamic project name display from `APP_NAME` env variable
- 🌓 Proper light/dark mode support with contrasting colors
- 💎 Clean design with white border and subtle shadow
- 📱 Responsive layout

#### Installation Improvements

- 🏷️ `.env.example` now uses project name placeholders
- ⚙️ Automatic replacement of `${PROJECT_NAME}` and `${OWNER}` during installation
- 🗄️ Database name automatically set to snake_case project name
- 📧 Superadmin email uses owner domain
- 🪣 MinIO bucket uses project name

### 🐛 Bug Fixes

#### Toast Notifications

- Fixed Livewire dispatch syntax (must use named parameters, not array)
- Fixed Alpine.js SVG icon rendering (`x-show` instead of `<template x-if>`)
- Added proper fallback classes for all toast states
- Fixed text visibility in both light and dark modes

#### Settings

- Replaced database-based settings with `.env` file approach
- Added proper `.env` file update helper functions
- Fixed settings not persisting after save

#### UI/UX

- Fixed app logo visibility in light mode
- Improved color contrast for better accessibility
- Changed from `brand-*` to `blue-*` colors for easier customization

### 🔧 Technical Changes

#### New Files

- `stubs/resources/views/components/toast.blade.php` - Toast notification component
- `stubs/resources/views/components/app-logo.blade.php` - Application logo component
- `stubs/support/env.php` - Environment file update helpers
- `stubs/docs/toast-notifications.md` - Toast documentation

#### Modified Files

- `stubs/.env.example` - Added project name placeholders
- `stubs/resources/views/livewire/admin/settings/show.blade.php` - Enhanced settings management
- `stubs/routes/web/administration.php` - Added settings authorization middleware
- `src/StartCommand.php` - Improved environment file setup
- `stubs/app/Providers/AdminServiceProvider.php` - Added administration gates

#### New Helper Functions

- `update_env(string $key, mixed $value)` - Update single env variable
- `update_env_multiple(array $data)` - Update multiple env variables

### 📝 Documentation

- 📝 Comprehensive documentation - reorganise the `docs/` based on context and priority.

#### Code Examples

All examples updated to use correct Livewire 3 named parameter syntax:

  ```php
  // ✅ Correct
$this->dispatch('toast',
type: 'success',
message: 'Success!',
duration: 3000
);

// ❌ Old (incorrect)
$this->dispatch('toast', [
'type' => 'success',
'message' => 'Success!'
]);






  ```
### 💡 Migration Guide

From Previous Version

1. Toast Notifications: Update to use the new toast component: `$this->dispatch('toast', type: 'success', message: 'Saved!');`
2. Settings: Settings now persist to .env file automatically. No database table needed.
3. Branding: The app logo now uses `config('app.name')` automatically.

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
