# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel application** bootstrapped with
[CleaniqueCoders Kickoff](https://github.com/cleaniquecoders/kickoff), providing a standardized
structure with pre-configured packages and conventions.

- **Framework**: Laravel 12+ with PHP 8.4+
- **Frontend**: Livewire 4 + TailwindCSS v4 + Alpine.js
- **Testing**: Pest PHP (not PHPUnit syntax)
- **Database**: MySQL with UUID primary keys

## Common Commands

```bash
# Development
composer dev              # Start server, queue, logs, and Vite concurrently
npm run dev               # Vite dev server with HMR
npm run build             # Build production assets

# Testing
composer test             # Run all tests
composer test-arch        # Run architecture tests only
composer test-coverage    # Run tests with coverage

# Code Quality
composer format           # Format code with Laravel Pint
composer analyse          # Run PHPStan static analysis
composer rector           # Run Rector for automated refactoring
composer lint             # Check PHP syntax

# Database
php artisan migrate       # Run migrations
php artisan reload:db     # Drop, migrate, and seed (fresh start)

# Single test file
./vendor/bin/pest tests/Feature/ExampleTest.php
./vendor/bin/pest --filter="test name"
```

## Architecture & Key Concepts

### Models - CRITICAL

**ALL models MUST extend `App\Models\Base`** instead of `Illuminate\Database\Eloquent\Model`.

```php
namespace App\Models;

use App\Models\Base as Model;

class Product extends Model
{
    // UUID primary keys - automatic
    // Auditing - automatic
    // Media support - automatic
}
```

The Base model provides:

- UUID primary keys (`InteractsWithUuid`)
- Auditing via owen-it/laravel-auditing
- Media attachments via Spatie Media Library
- User tracking (created_by, updated_by)
- Resource route helpers

### Database Conventions

- **Primary keys**: Always UUID (`$table->uuid('id')->primary()`)
- **Soft deletes**: Use for all user-facing models
- **Column naming**: snake_case
- **Credentials columns**: Always cast with `encrypted:array` (not manual `encrypt()`)

> **Gotcha:** Using `encrypt()` manually when the model already has `encrypted:array` cast
> causes double-encryption. The cast handles encryption transparently — just assign the plain array.

### Enums

Use enums for all status/type fields. Place in `app/Enums/`.
Custom stub at `stubs/enum.stub` generates the correct template via `php artisan make:enum`.

```php
namespace App\Enums;

use CleaniqueCoders\Traitify\Contracts\Enum as Contract;
use CleaniqueCoders\Traitify\Concerns\InteractsWithEnum;

enum Status: string implements Contract
{
    use InteractsWithEnum;

    case DRAFT = 'draft';
    case ACTIVE = 'active';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DRAFT => 'Item is in draft state.',
            self::ACTIVE => 'Item is active.',
        };
    }
}
```

All enums must implement `CleaniqueCoders\Traitify\Contracts\Enum` and use the
`InteractsWithEnum` trait. This provides `values()`, `labels()`, and `options()` methods.

### Authorization

Use **Spatie Laravel Permission** with policies:

```php
// Permission naming: module.action.target
$user->can('users.view.list');
$user->can('products.create.item');

// In controllers
$this->authorize('update', $product);
```

Default roles: `superadmin`, `administrator`, `user`

### Application Settings (Spatie Laravel Settings)

Application-level settings are stored in the **database** via `spatie/laravel-settings` — NOT in `.env`.

**Settings classes** in `app/Settings/`:
- `GeneralSettings` — `site_name`
- `MailSettings` — `from_address`, `from_name`
- `NotificationSettings` — `enabled`, `channels`

**How it works**: `AppServiceProvider::boot()` reads from DB and overrides `config()` values, so all existing `config('app.name')`, `config('mail.from.*')`, `config('notification.*')` calls automatically use DB values.

```php
// Reading (via config — already overridden at runtime)
config('app.name');

// Reading (via Settings class directly)
app(GeneralSettings::class)->site_name;

// Writing
$settings = app(GeneralSettings::class);
$settings->site_name = 'New Name';
$settings->save();
```

**Admin UI**: Managed at Admin > Settings (site name, mail from, notifications).

**What stays in .env**: Infrastructure settings (`APP_ENV`, `APP_DEBUG`, SMTP credentials, DB, Redis).

> **Gotcha:** Never write to `.env` at runtime. Use Spatie Settings for any value that admins should be able to change from the UI.

### Helper Functions

Located in `support/` directory, auto-loaded via Composer:

```php
user();                           // Get authenticated user
flash('success', 'Message');      // Flash messages
money_format(1234.56);            // Format: "1,234.56"
```

### Directory Conventions

When the project grows, follow these directory conventions for organizing business logic:

| Directory | Purpose | When to Create |
|---|---|---|
| `app/Services/` | Business logic services (e.g., `PaymentService`, `ReportService`) | When logic doesn't belong in a model, controller, or action |
| `app/DataTransferObjects/` | Typed DTOs for passing structured data between layers | When passing 3+ related values between classes |
| `app/Contracts/` | Interfaces for services and abstractions | When you need swappable implementations or test doubles |
| `app/Actions/` | Single-purpose action classes | When an operation is reusable across controllers/commands |

These directories are not scaffolded by default — create them as needed. The architecture tests
already enforce `app/Contracts/` contains only interfaces and `app/Concerns/` contains only traits.

## File Organization

```text
app/
├── Actions/        # Single-purpose action classes (Builder/Menu already included)
├── Concerns/       # Traits (InteractsWithLivewireAlert, InteractsWithLivewireConfirm, etc.)
├── Console/        # Artisan commands (24+ included: seeders, cache, code generation)
├── Contracts/      # Interfaces (HeadingMenuBuilder, AuthorizedMenuBuilder)
├── Enums/          # Status/type enums
├── Exceptions/     # Custom exceptions (ActionException, ThrowException)
├── Livewire/       # Livewire components
├── Models/         # Eloquent models (extend Base)
├── Notifications/  # Notification classes
├── Mail/           # Mailable classes
├── Policies/       # Authorization policies
├── Settings/       # Spatie Settings classes
support/            # Helper functions by domain (16 files)
routes/web/         # Modular web routes (auth, admin, security, pages, etc.)
stubs/              # Custom Artisan stubs (model, migration, enum, pest, policy)
bin/                # Deployment and utility scripts (7 scripts)
docs/               # Project documentation (getting started, development guides)
config/             # Custom configs (access-control, admin, audit, horizon, etc.)
```

## Testing with Pest

Use Pest syntax (not PHPUnit):

```php
it('can create a product', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/products', ['name' => 'Test'])
        ->assertRedirect('/products');

    expect(Product::count())->toBe(1);
});
```

### Livewire Testing

```php
use Livewire\Livewire;

Livewire::test(ProductForm::class)
    ->set('name', 'Test Product')
    ->call('save')
    ->assertHasNoErrors()
    ->assertDispatched('toast');
```

### Architecture Tests

Located in `tests/Feature/ArchitectureTest.php`. Enforces:

- No `dd()`, `dump()`, `ray()` in application code
- No `url()` helper usage — use `route()` instead
- `env()` only used in `config/` files
- No raw DB queries (`DB::raw`, `DB::select`, etc.)
- Controllers have `Controller` suffix
- Policies are classes with `Policy` suffix
- Mailables extend `Illuminate\Mail\Mailable`
- Concerns are traits, Enums are enums, Contracts are interfaces

## Livewire Patterns

### Toast Notifications (Primary)

Use the `<x-toast />` Alpine.js component (already mounted in sidebar layout) for all
user-facing notifications. Dispatch from any Livewire component:

```php
// Success notification
$this->dispatch('toast', type: 'success', message: 'Item saved successfully.');

// Error notification
$this->dispatch('toast', type: 'error', message: 'Something went wrong.');

// Warning / Info
$this->dispatch('toast', type: 'warning', message: 'Check your input.');
$this->dispatch('toast', type: 'info', message: 'Processing started.');
```

After redirect — flash to session, pick up on target page:

```php
session()->flash('toast', ['message' => 'Done!', 'type' => 'success']);
return $this->redirect('/products');
```

From Alpine.js directly:

```html
<button @click="$dispatch('toast', { type: 'info', message: 'Hello from Alpine!' })">
```

> **Gotcha:** `InteractsWithLivewireAlert` dispatches `displayAlert` to a `<livewire:alert>`
> modal component. For simple feedback messages, prefer `$this->dispatch('toast', ...)` which
> uses the `<x-toast />` component already in the sidebar layout. Reserve `InteractsWithLivewireAlert`
> only for modal-style alerts that require user acknowledgement.

### Confirmations

```php
use App\Concerns\InteractsWithLivewireConfirm;

class MyComponent extends Component
{
    use InteractsWithLivewireConfirm;

    public function delete($id)
    {
        $this->confirm(
            'Delete Item',
            'Are you sure?',
            'my-component',
            'performDelete',
            $id
        );
    }
}
```

### Page Header Pattern

All pages should follow this consistent header structure:

```blade
<flux:breadcrumbs class="mb-6">
    <flux:breadcrumbs.item href="{{ route('dashboard') }}">Dashboard</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Products</flux:breadcrumbs.item>
</flux:breadcrumbs>
<div class="flex items-end justify-between">
    <div>
        <flux:heading size="xl" level="1">Products</flux:heading>
        <flux:text class="mt-2">Manage your products.</flux:text>
    </div>
    <div class="flex items-center gap-2">
        {{-- Action buttons here --}}
    </div>
</div>
<flux:separator variant="subtle" class="my-6" />
```

### Mail — Always Queued

In Livewire components, **always use queued Mailables** instead of synchronous `Mail::send()`:

```php
// DO — queued, non-blocking
Mail::to($user)->queue(new OrderConfirmation($order));

// DON'T — synchronous, blocks Livewire's HTTP response
Mail::send('emails.order', $data, function ($message) { ... });
```

> **Gotcha:** `Mail::send()` is synchronous — it blocks until the SMTP server responds. In
> Livewire components this prevents redirects and UI updates from firing. Always use queued
> Mailables: `Mail::to(...)->queue(new MyMailable(...))`.

> **Gotcha:** `Mail::send()` cannot render `<x-mail::message>` Markdown mail components —
> it renders templates as regular Blade views, causing "No hint path defined for [mail]"
> error. Use proper Mailable classes with `->markdown()`, or use plain HTML Blade views.

## Important Conventions

### DO

- Extend `App\Models\Base` for all models
- Use UUID primary keys
- Use enums for status/type fields with `Enum` contract and `InteractsWithEnum` trait
- Use Pest syntax for tests
- Use policies for authorization
- Use Form Requests for validation
- Use `route()` helper for URLs
- Use queued Mailables (`Mail::to()->queue()`) in Livewire components
- Use `$this->dispatch('toast', ...)` for user feedback notifications
- Add `cursor-pointer` class to clickable buttons (TailwindCSS v4 default)
- Register new queue names in `config/horizon.php` supervisor queue list

### DON'T

- Extend `Illuminate\Database\Eloquent\Model` directly
- Use auto-increment IDs
- Use `url()` helper — use `route()` instead
- Use `dd()`, `dump()` in production code
- Use raw SQL queries — use Eloquent
- Use PHPUnit syntax — use Pest
- Write to `.env` at runtime — use Spatie Settings for admin-configurable values
- Expose `APP_ENV`, `APP_DEBUG`, or SMTP credentials in admin UI
- Use `Mail::send()` in Livewire — use queued Mailables instead
- Use `encrypt()` manually when model has `encrypted:array` cast (double-encryption)

## Release Workflow

When asked to commit, push, tag, and release:

1. **Commit** the changes (do NOT update CHANGELOG.md — it is auto-generated by GitHub Actions)
2. **Push** to the remote branch
3. **Tag** with the next version. Determine next version by checking the latest tag with `git tag --sort=-v:refname | head -1`
4. **Push the tag** with `git push origin <tag>`
5. **Create a GitHub release** using `gh release create <tag> --title "<tag>" --notes "<release notes>"` with a concise summary of changes. Always include a **Full Changelog** compare link at the bottom of the release notes: `**Full Changelog**: https://github.com/<owner>/<repo>/compare/<previous-tag>...<new-tag>`

## Code Quality Checklist

Before committing:

- [ ] Models extend `App\Models\Base`
- [ ] Status fields use enums
- [ ] Tests use Pest syntax
- [ ] `composer format` passes
- [ ] `composer analyse` passes
- [ ] `composer test` passes

## Packages

### Core

- **spatie/laravel-permission**: Roles and permissions
- **spatie/laravel-settings**: Application settings stored in database
- **spatie/laravel-medialibrary**: File/media management
- **owen-it/laravel-auditing**: Audit trail
- **cleaniquecoders/traitify**: Common traits and contracts

### Development

- **laravel/telescope**: Debugging (access via /telescope)
- **laravel/horizon**: Queue monitoring (access via /horizon)
- **barryvdh/laravel-debugbar**: Debug toolbar

### Frontend

- **livewire/livewire**: Reactive components
- **livewire/flux**: UI components
- **mallardduck/blade-lucide-icons**: Icons via `@svg('lucide-icon-name')`

## Docker Services

The project includes a `docker-compose.yml` with the following services:

| Service     | Port(s)        | Description              |
| ----------- | -------------- | ------------------------ |
| MySQL       | 3306           | Database server          |
| Redis       | 6379           | Cache & session store    |
| Mailpit     | 1025, 8025     | Mail testing (SMTP + UI) |
| Meilisearch | 7700           | Full-text search engine  |
| MinIO       | 9000, 9001     | S3-compatible storage    |

```bash
# Start all services
docker compose up -d

# Stop all services
docker compose down

# View logs
docker compose logs -f
```

Access points:
- **Mailpit UI**: http://localhost:8025
- **MinIO Console**: http://localhost:9001 (minioadmin/minioadmin)
- **Meilisearch**: http://localhost:7700

## Environment Variables

Key variables in `.env`:

```env
# Superadmin (seeded on fresh install)
SUPERADMIN_NAME="Admin"
SUPERADMIN_EMAIL="admin@example.com"
SUPERADMIN_PASSWORD=password

# Features
ACCESS_CONTROL_ENABLED=true
TELESCOPE_ENABLED=true

# Docker Services
DB_ROOT_PASSWORD=root
MAILPIT_UI_PORT=8025
MEILI_MASTER_KEY=masterKey
MINIO_ROOT_USER=minioadmin
MINIO_ROOT_PASSWORD=minioadmin
```

## Quick Reference

```bash
# Generate with proper stubs
php artisan make:model Product           # Extends Base automatically
php artisan make:policy ProductPolicy --model=Product
php artisan make:test ProductTest --pest

# Deployment
bin/deploy -b main                       # Deploy specific branch
bin/backup-app                           # Backup application
```

## Gotchas

### Livewire 4

> **Gotcha:** Livewire 4 does not support the `rules()` method for dynamic validation.
> Calling `$this->validate()` without rules throws `MissingRulesException`.
> Always pass rules inline: `$this->validate($rules)`.

> **Gotcha:** `<script>` tags inside Livewire components don't re-execute on `wire:navigate`.
> For Alpine.js components, use inline `x-data="{...}"` objects instead of `Alpine.data()`
> registered via `document.addEventListener('alpine:init', ...)` in a `<script>` block.

> **Gotcha:** Livewire 4's `addNamespace()` takes precedence over `component()` for namespaced
> components (those with `::`). The `Finder::resolveClassComponentClassName()` checks
> `classNamespaces` first and never falls through to `classComponents`.

### Flux UI

> **Gotcha:** Flux UI `description` prop on `<flux:input>` renders the text **above** the
> input field, not below. For consistent below-input help text, use a manual
> `<p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">` after the component instead.

> **Gotcha:** `flux:tab.group` / `flux:tabs` is **Flux Pro only** — not available in the free
> version. Use Alpine.js `x-data`/`x-show` for tabs instead, with `cursor-pointer`, hover/active
> states, and URL deep linking via `window.history.replaceState`.

> **Gotcha:** `@json()` Blade directive inside HTML attributes causes parse errors due to
> bracket conflicts. Use `{!! json_encode(...) !!}` instead when outputting JSON in attributes.

### TailwindCSS v4

> **Gotcha:** TailwindCSS v4 does not add `cursor: pointer` to `<button>` elements by
> default. Always add `cursor-pointer` class to clickable buttons explicitly.

### Forms & Grid Layout

> **Gotcha:** When adding fields to a 2-column `sm:grid-cols-2` form, always maintain proper
> left-right pairing. An odd field inserted in the middle shifts all subsequent fields and
> breaks visual alignment. Place new fields to preserve existing pairs.

### Horizon & Queues

> **Gotcha:** When adding new queue names (e.g., `backups`, `webhooks`), they must be
> registered in `config/horizon.php` supervisor queue list — otherwise Horizon won't pick
> up jobs dispatched to those queues. Also ensure supervisor `timeout` >= job `$timeout`.

### BackedEnum

> **Gotcha:** `BackedEnum` objects cannot be cast to string with `(string)`. Use
> `$value instanceof \BackedEnum ? $value->value : $value` when normalizing model
> attributes for comparison (e.g., snapshot diffs, array comparisons).

---

## Claude Self-Update Practice — CRITICAL

This file is a **living document**. Claude must update `CLAUDE.md` whenever:

1. **User corrects a mistake** — e.g., "jangan guna MySQL, kita pakai PostgreSQL"
2. **User expresses a preference** — e.g., "aku tak suka pattern ni, guna cara lain"
3. **A better pattern is discovered** during implementation
4. **A gotcha or edge case is found** that could cause future mistakes

### How to Update

When a correction or preference is identified:

1. Apply the fix to the current task
2. Immediately update the relevant section in `CLAUDE.md` to reflect the new rule
3. If it's a DO/DON'T, add it to the **DO / DON'T** section
4. If it's architectural, update the relevant architecture section
5. If it's a new gotcha, add it under the relevant section with a `> **Gotcha:**` callout

### Format for Gotchas

```markdown
> **Gotcha:** PostgreSQL `uuid-ossp` extension must be enabled before using
> `DB::raw('uuid_generate_v4()')`. Prefer letting Laravel handle UUID generation
> from PHP side via `InteractsWithUuid` trait instead.
```

### What NOT to Record

- One-off task-specific decisions that don't affect future work
- Things already covered by Laravel or package documentation
- Preferences that are already obvious from existing conventions

> **Rule**: When in doubt — record it. A slightly redundant note is better than repeating a mistake.
