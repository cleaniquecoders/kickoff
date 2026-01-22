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

### Enums

Use enums for all status/type fields. Place in `app/Enums/`:

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
}
```

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

### Helper Functions

Located in `support/` directory, auto-loaded via Composer:

```php
user();                           // Get authenticated user
flash('success', 'Message');      // Flash messages
money_format(1234.56);            // Format: "1,234.56"
```

## File Organization

```text
app/
├── Concerns/       # Traits (InteractsWithLivewireAlert, etc.)
├── Enums/          # Status/type enums
├── Livewire/       # Livewire components
├── Models/         # Eloquent models (extend Base)
├── Policies/       # Authorization policies
support/            # Helper functions by domain
routes/web/         # Modular web routes
stubs/              # Custom Artisan stubs
bin/                # Deployment and utility scripts
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
    ->assertHasNoErrors();
```

## Livewire Patterns

### Alerts and Confirmations

```php
use App\Concerns\InteractsWithLivewireAlert;
use App\Concerns\InteractsWithLivewireConfirm;

class MyComponent extends Component
{
    use InteractsWithLivewireAlert;
    use InteractsWithLivewireConfirm;

    public function save()
    {
        // ... save logic
        $this->alert('Success', 'Item saved!');
    }

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

## Important Conventions

### DO

- Extend `App\Models\Base` for all models
- Use UUID primary keys
- Use enums for status/type fields
- Use Pest syntax for tests
- Use policies for authorization
- Use Form Requests for validation
- Use `route()` helper for URLs

### DON'T

- Extend `Illuminate\Database\Eloquent\Model` directly
- Use auto-increment IDs
- Use `url()` helper - use `route()` instead
- Use `dd()`, `dump()` in production code
- Use raw SQL queries - use Eloquent
- Use PHPUnit syntax - use Pest

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
