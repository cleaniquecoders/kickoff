# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel application** bootstrapped with
[CleaniqueCoders Kickoff](https://github.com/cleaniquecoders/kickoff), providing a standardized
structure with pre-configured packages and conventions.

- **Framework**: Laravel 13+ with PHP 8.4+
- **Frontend**: Livewire 4 + TailwindCSS v4 + Alpine.js
- **Testing**: Pest PHP (not PHPUnit syntax)
- **Database**: MySQL with dual-key pattern (auto-increment `id` for internal relations + `uuid` column for public-facing identifiers)

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

Laravel 13 uses **PHP attributes** for model properties instead of `$fillable`, `$guarded`, `$hidden`:

```php
namespace App\Models;

use App\Models\Base as Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

#[Fillable(['name', 'description', 'status'])]
#[Hidden(['id'])]
class Product extends Model
{
    // Auto-increment id (internal) + uuid column (public-facing) - automatic
    // Auditing - automatic
    // Media support - automatic
}
```

The Base model provides:

- Dual-key pattern: auto-increment `id` + auto-generated `uuid` column (`InteractsWithUuid`)
- Auditing via owen-it/laravel-auditing
- Media attachments via Spatie Media Library
- User tracking (created_by, updated_by)
- Resource route helpers

### Database Conventions

- **Primary keys**: Auto-increment `id` for internal DB relations + `uuid` column for public-facing identifiers (`$table->id()` + `$table->uuid('uuid')->index()`)
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
├── Concerns/       # Traits (InteractsWithLivewireConfirm, HasMedia, etc.)
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

> **Gotcha:** For user feedback messages, use `$this->dispatch('toast', type: 'success', message: '...')`
> which renders via the `<x-toast />` component already in the sidebar layout. Toast types:
> `success`, `error`, `warning`, `info`. Do NOT create custom alert modal components for simple
> feedback — toast notifications are the standard pattern.

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

### UI Requirements — MUST HAVE

- **Responsive**: All pages MUST be fully responsive (mobile, tablet, desktop). Use Tailwind responsive prefixes (`sm:`, `md:`, `lg:`) consistently.
- **Dark mode**: All pages MUST support dark mode. Use `dark:` variants for all custom styling. Flux UI components handle this automatically — only add `dark:` for custom HTML/Tailwind elements.
- **Data tables — max 5 visible columns** (excluding actions). If a model has more than 5 displayable columns, combine related columns into a single column (e.g., "Name + Email" in one cell, "Created + Updated" in one cell). Never render wide, horizontally-scrolling tables.
- **Action buttons — always use 3-dot menu** (`...` / ellipsis / kebab menu). Never render inline action buttons (Edit, Delete, View) as separate buttons in table rows. Always group actions behind a `<flux:dropdown>` triggered by a 3-dot icon button.

```blade
{{-- Action column pattern (import icons first: php artisan flux:icon ellipsis eye pencil trash-2) --}}
<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis" />
    <flux:menu>
        <flux:menu.item icon="eye" href="{{ route('products.show', $product) }}">View</flux:menu.item>
        <flux:menu.item icon="pencil" href="{{ route('products.edit', $product) }}">Edit</flux:menu.item>
        <flux:menu.item icon="trash-2" variant="danger" wire:click="delete({{ $product->id }})">Delete</flux:menu.item>
    </flux:menu>
</flux:dropdown>
```

### Icons — Lucide via Flux (NOT Heroicons)

This project uses **Lucide icons** imported via Flux's built-in command, NOT Heroicons. When you need an icon beyond Flux's bundled Heroicons, import from Lucide.

**Before using any icon**, confirm it exists in [Lucide's icon set](https://lucide.dev/icons) and import it:

```bash
# Import Lucide icons (no prefix needed — just the icon name)
php artisan flux:icon pencil trash-2 eye ellipsis plus
```

**Usage in Blade** (no `lucide-` prefix — use the plain icon name after import):

```blade
{{-- Flux components --}}
<flux:icon.crown />
<flux:button icon="pencil">Edit</flux:button>
<flux:button icon="ellipsis" variant="ghost" size="sm" />

{{-- Standalone via blade-lucide-icons package --}}
@svg('lucide-eye', 'w-4 h-4')
```

> **Gotcha:** Flux's `php artisan flux:icon` imports Lucide icons so they work with the **same syntax** as Heroicons — just `icon="pencil"`, NOT `icon="lucide-pencil"`. The `lucide-` prefix is only needed when using the `@svg()` Blade directive from `mallardduck/blade-lucide-icons`. If a Flux component renders a blank/missing icon, you likely forgot to run `php artisan flux:icon <name>`.

### DO

- Extend `App\Models\Base` for all models
- Use PHP attributes for model properties: `#[Fillable([...])]`, `#[Guarded([...])]`, `#[Hidden([...])]`
- Use dual-key pattern: auto-increment `id` (internal) + `uuid` column (public-facing, URLs, APIs)
- Use enums for status/type fields with `Enum` contract and `InteractsWithEnum` trait
- Use Pest syntax for tests
- Use policies for authorization
- Use Form Requests for validation
- Use `route()` helper for URLs
- Use queued Mailables (`Mail::to()->queue()`) in Livewire components
- Use `$this->dispatch('toast', ...)` for user feedback notifications
- Add `cursor-pointer` class to clickable buttons (TailwindCSS v4 default)
- Register new queue names in `config/horizon.php` supervisor queue list
- Import Lucide icons with `php artisan flux:icon <name>` before using them in Flux components
- Confirm icon exists in Lucide's icon set before importing
- Make all pages responsive (mobile-first with `sm:`, `md:`, `lg:` breakpoints)
- Support dark mode on all pages (use `dark:` variants for custom elements)
- Use 3-dot dropdown menu for row actions in data tables
- Combine columns when table has more than 5 data columns (excluding actions)

### DON'T

- Extend `Illuminate\Database\Eloquent\Model` directly
- Use `protected $fillable`, `$guarded`, or `$hidden` properties — use PHP attributes instead
- Expose auto-increment `id` in URLs or APIs — use `uuid` for public-facing identifiers
- Use `url()` helper — use `route()` instead
- Use `dd()`, `dump()` in production code
- Use raw SQL queries — use Eloquent
- Use PHPUnit syntax — use Pest
- Write to `.env` at runtime — use Spatie Settings for admin-configurable values
- Expose `APP_ENV`, `APP_DEBUG`, or SMTP credentials in admin UI
- Use `Mail::send()` in Livewire — use queued Mailables instead
- Use `encrypt()` manually when model has `encrypted:array` cast (double-encryption)
- Render inline action buttons (Edit, Delete, View) in table rows — use 3-dot dropdown menu
- Create tables with more than 5 visible data columns — combine related columns instead
- Skip responsive design or dark mode support on any page
- Use Heroicon-specific names (e.g., `pencil-square`) — use Lucide equivalents (e.g., `pencil`) instead
- Use icons without importing them first via `php artisan flux:icon <name>`

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

### Laravel 13

> **Gotcha:** Laravel 13 uses **PHP attributes** for model properties (`#[Fillable([...])]`,
> `#[Guarded([...])]`, `#[Hidden([...])]`) instead of `protected $fillable`, `$guarded`, `$hidden`.
> Always use the attribute syntax for new models. Import from `Illuminate\Database\Eloquent\Attributes`.

> **Gotcha:** CSRF middleware was renamed from `VerifyCsrfToken` to `PreventRequestForgery` in
> Laravel 13. If you customize CSRF behavior, use the new class name.

> **Gotcha:** Cache serialization defaults to `serializable_classes: false` in Laravel 13.
> If you need to cache PHP objects (not just strings/arrays), explicitly set
> `'serializable_classes' => true` in `config/cache.php`.

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

## Claude Operating Principles

### 1. Plan Mode Default

- Enter plan mode for ANY non-trivial task (3+ steps or architectural decisions)
- If something goes sideways, STOP and re-plan immediately — don't keep pushing
- Use plan mode for verification steps, not just building
- Write detailed specs upfront to reduce ambiguity

### 2. Subagent Strategy

- Use subagents liberally to keep main context window clean
- Offload research, exploration, and parallel analysis to subagents
- For complex problems, throw more compute at it via subagents
- One task per subagent for focused execution
- Only parallelize truly independent queries — avoid redundant searches

### 3. Self-Improvement Loop

- After ANY correction from the user: update `tasks/lessons.md` with the pattern
- Write rules for yourself that prevent the same mistake
- Ruthlessly iterate on these lessons until mistake rate drops
- Review lessons at session start for relevant project

### 4. Verification Before Done

- Never mark a task complete without proving it works
- Diff behavior between main and your changes when relevant
- Ask yourself: "Would a staff engineer approve this?"
- Run tests, check logs, demonstrate correctness

### 5. Demand Elegance (Balanced)

- For non-trivial changes: pause and ask "is there a more elegant way?"
- If a fix feels hacky: "Knowing everything I know now, implement the elegant solution"
- Skip this for simple, obvious fixes — don't over-engineer
- Challenge your own work before presenting it

### 6. Autonomous Bug Fixing

- When given a bug report: just fix it. Don't ask for hand-holding
- Point at logs, errors, failing tests — then resolve them
- Zero context switching required from the user
- Go fix failing CI tests without being told how

### Task Management

1. **Plan First**: Write plan to `tasks/todo.md` with checkable items
2. **Verify Plan**: Check in before starting implementation
3. **Track Progress**: Mark items complete as you go
4. **Explain Changes**: High-level summary at each step
5. **Document Results**: Add review section to `tasks/todo.md`
6. **Capture Lessons**: Update `tasks/lessons.md` after corrections

Scale process to task size — simple fixes skip steps 1–2.

### Core Principles

- **Simplicity First**: Make every change as simple as possible. Impact minimal code.
- **No Laziness**: Find root causes. No temporary fixes. Senior developer standards.
- **Match Effort to Impact**: Don't refactor surrounding code unless asked.
