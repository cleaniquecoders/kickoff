# GitHub Copilot Instructions for Laravel Projects

This project is built using the **CleaniqueCoders Kickoff** template, which provides a standardized Laravel application structure with pre-configured packages, conventions, and best practices.

## 🏗️ Project Architecture

### Tech Stack

- **Laravel**: 12+
- **PHP**: 8.4+ (using modern PHP features)
- **Frontend**: Vite + TailwindCSS v4 + Livewire 4 + Alpine.js
- **Database**: MySQL (SQLite for testing)
- **Cache/Session**: Redis
- **Queue**: Configurable (sync default, Redis/database for production)

### Directory Structure

- `app/`: Application code (Models, Livewire, Policies, etc.)
- `support/`: Helper functions organized by domain
- `routes/web/`: Modular web routes
- `stubs/`: Custom Artisan stubs
- `bin/`: Deployment and utility scripts
- `resources/js/`: Frontend JavaScript
- `resources/views/`: Blade templates

---

## 🎨 Frontend Development

### TailwindCSS

- Use **TailwindCSS v3** utility classes
- Typography plugin available (`@tailwindcss/typography`)
- Forms plugin available (`@tailwindcss/forms`)
- Custom configuration in `tailwind.config.js`
- Content paths include: views, support directory, vendor SVGs

### Livewire Components

- Use **Livewire 4** syntax with built-in single-file component support
- Component communication via `$this->dispatch()->to('component-name')`
- Single-file components use anonymous class extending `Livewire\Component`
- Available custom Livewire components:
  - `Alert`: Modal alerts with title and message
  - `Confirm`: Confirmation dialogs with callbacks
- Wire directives: `wire:model.live`, `wire:model.blur`, `wire:loading`, `wire:navigate`

**Example Livewire Alert:**

```php
// In a Livewire component, use the InteractsWithLivewireAlert trait
use App\Concerns\InteractsWithLivewireAlert;

public function someMethod()
{
    $this->alert('Success', 'Operation completed successfully!');
}
```

**Example Livewire Confirm:**

```php
// In a Livewire component, use the InteractsWithLivewireConfirm trait
use App\Concerns\InteractsWithLivewireConfirm;

public function deleteItem($id)
{
    $this->confirm(
        'Delete Item',
        'Are you sure you want to delete this item?',
        'your-component-name',
        'confirmDelete',
        $id
    );
}
```

### Icons

- Use **Blade Lucide Icons** package
- Icons available via `@svg('lucide-icon-name')`
- Configure in `config/blade-icons.php`

### JavaScript

- **Vite** for asset bundling
- **Alpine.js** integrated via Livewire
- **Tippy.js** for tooltips
- Entry point: `resources/js/app.js`

---

## 🔨 Backend Development

### Models

**CRITICAL**: Always extend `App\Models\Base` instead of `Illuminate\Database\Eloquent\Model`

The Base model includes:

- ✅ **UUID primary keys** (InteractsWithUuid)
- ✅ **Auditing** (OwenIt\Auditing)
- ✅ **Media attachments** (Spatie Media Library)
- ✅ **Meta data** (InteractsWithMeta)
- ✅ **User tracking** (InteractsWithUser)
- ✅ **Resource routing** (InteractsWithResourceRoute)
- ✅ **Searchable** functionality
- ✅ **Token generation**

**Example Model:**

```php
<?php

namespace App\Models;

use App\Models\Base as Model;

class Product extends Model
{
    // UUID is automatically handled
    // Auditing is automatically enabled
    // Media can be attached using InteractsWithMedia
}
```

**Using Media:**

```php
// Add media to a model
$product->addMedia($request->file('image'))
    ->toMediaCollection('products');

// Retrieve media
$product->getFirstMediaUrl('products');
$product->getFirstMediaUrl('products', 'thumb'); // 130x130 thumbnail
```

### Database Conventions

- ✅ Use **UUID primary keys** (not auto-increment)
- ✅ Use **snake_case** for column names
- ✅ Use **soft deletes** where appropriate
- ✅ Use custom migration stub: `php artisan make:migration CreateProductsTable`

### Resource Routes

Models extending Base have automatic resource URL generation:

```php
// Generate resource URLs
$product->getResourceUrl('index');  // /products
$product->getResourceUrl('show');   // /products/{uuid}
$product->getResourceUrl('edit');   // /products/{uuid}/edit

// Customize route prefix by adding property to model:
protected $url_route_prefix = 'admin';
// Results in: /admin.products
```

---

## 🔐 Access Control & Authorization

### Roles & Permissions

Use **Spatie Laravel Permission** package with custom configuration.

**Default Roles:**

- `superadmin`: Full system access (wildcard `*` permission)
- `administrator`: Admin panel, user management, security features
- `user`: Default user role

**Permission Naming Convention:**

Use `module.action.target` format (e.g., `users.view.list`, `admin.access.telescope`)

**Permission Categories:**

- `admin.*`: Admin panel access, settings, Telescope, Horizon, impersonation
- `users.*`: User management (view, create, update, delete)
- `roles.*`: Role management
- `security.*`: Access control, audit logs
- `profile.*`: User profile operations
- `notifications.*`: Notification management
- `dashboard.*`: Dashboard access

**Checking Permissions:**

```php
// In controllers/components
if ($user->can('users.view.list')) {
    // Show user list
}

// In Blade
@can('users.create.account')
    <button>Create User</button>
@endcan

// In policies
public function viewAny(User $user)
{
    return $user->hasPermissionTo('users.view.list');
}
```

### Policies

**ALWAYS** create policies for models using the custom stub:

```bash
php artisan make:policy ProductPolicy --model=Product
```

Implement standard policy methods:

- `viewAny()`: List resources
- `view()`: View single resource
- `create()`: Create new resource
- `update()`: Update existing resource
- `delete()`: Delete resource
- `restore()`: Restore soft-deleted resource
- `forceDelete()`: Permanently delete

**Example Policy:**

```php
public function viewAny(User $user)
{
    return $user->hasPermissionTo('products.view.list');
}

public function update(User $user, Product $product)
{
    return $user->hasPermissionTo('products.update.item');
}
```

### Impersonation

- Available via `lab404/laravel-impersonate`
- Enabled/disabled via `config/impersonate.php`
- Superadmin **cannot** be impersonated
- Check: `$user->canImpersonate()` and `$user->canBeImpersonated()`

---

## 🛠️ Helper Functions

Helpers are organized in `support/` directory and auto-loaded via `composer.json`.

### User Helper

```php
user(); // Get authenticated user (shorthand for auth()->user())
```

### Flash Messages

```php
flash('success', 'Operation completed!');
flash('error', 'Something went wrong!');
flash('info', 'Please note...');
flash('warning', 'Be careful!');
flash('danger', 'Critical error!');

// Variants include TailwindCSS classes for border, bg, and text
```

### API Helpers

```php
// Return API response
return api_response($apiObject);

// Handle API exceptions
return api_exception($exception);

// Get API accept header
api_accept_header(); // Returns: application/{tree}.{subtype}.{version}+json
```

### Formatting

```php
money_format(1234.56); // Returns: "1,234.56"
```

### Menu Builder

```php
// Build menus using the menu helper
$menu = menu('admin-sidebar')->build();
```

### Utility Helpers

Additional helpers available for:

- **dump**: Enhanced dump utilities
- **json**: JSON handling
- **media**: Media operations
- **notification**: Notification helpers
- **options**: Option/setting management
- **sorter**: Sorting utilities
- **str**: String operations
- **uuid-id**: UUID generation

---

## 🎭 Livewire Patterns

### Traits for Livewire Components

**InteractsWithLivewireAlert:**

```php
use App\Concerns\InteractsWithLivewireAlert;

class MyComponent extends Component
{
    use InteractsWithLivewireAlert;

    public function save()
    {
        // ... save logic
        $this->alert('Success', 'Item saved successfully!');
    }
}
```

**InteractsWithLivewireConfirm:**

```php
use App\Concerns\InteractsWithLivewireConfirm;

class MyComponent extends Component
{
    use InteractsWithLivewireConfirm;

    public function deleteItem($id)
    {
        $this->confirm(
            'Delete Item',
            'Are you sure? This action cannot be undone.',
            'my-component', // Component name
            'performDelete', // Listener method
            $id // Additional parameters
        );
    }

    public function performDelete($id)
    {
        // Actually delete the item
    }
}
```

---

## 🧪 Testing

### Pest PHP

Use **Pest PHP** as the testing framework (not PHPUnit syntax).

**Running Tests:**

```bash
composer test              # Run all tests
composer test-arch         # Run architecture tests only
composer test-coverage     # Run tests with coverage
```

**Creating Tests:**

```bash
php artisan make:test ProductTest --pest           # Feature test
php artisan make:test ProductUnitTest --unit --pest # Unit test
```

### Feature Tests

Feature tests verify complete user workflows and HTTP interactions.

**Location**: `tests/Feature/`

**Example Feature Test:**

```php
<?php

use App\Models\Product;
use App\Models\User;

it('can create a product', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/products', [
            'name' => 'Test Product',
            'price' => 99.99,
        ])
        ->assertRedirect('/products');

    expect(Product::count())->toBe(1);
    expect(Product::first()->name)->toBe('Test Product');
});

it('requires authentication to create products', function () {
    post('/products', ['name' => 'Test'])
        ->assertRedirect('/login');
});

it('validates product data', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/products', [])
        ->assertSessionHasErrors(['name', 'price']);
});

it('authorizes product updates', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    actingAs($user)
        ->put("/products/{$product->uuid}", [
            'name' => 'Updated Product',
        ])
        ->assertForbidden();
});
```

### Unit Tests

Unit tests verify individual methods, classes, and logic in isolation.

**Location**: `tests/Unit/`

**Example Unit Test for Models:**

```php
<?php

use App\Models\Product;

it('generates a slug from name', function () {
    $product = new Product(['name' => 'Test Product']);

    expect($product->slug)->toBe('test-product');
});

it('calculates discounted price correctly', function () {
    $product = Product::factory()->make([
        'price' => 100,
        'discount_percentage' => 10,
    ]);

    expect($product->discounted_price)->toBe(90.0);
});

it('has uuid as primary key', function () {
    $product = Product::factory()->create();

    expect($product->getKeyName())->toBe('uuid');
    expect($product->uuid)->toBeString();
});

it('uses soft deletes', function () {
    $product = Product::factory()->create();
    $uuid = $product->uuid;

    $product->delete();

    expect(Product::withTrashed()->where('uuid', $uuid)->exists())->toBeTrue();
    expect(Product::where('uuid', $uuid)->exists())->toBeFalse();
});
```

**Example Unit Test for Helpers:**

```php
<?php

it('formats money correctly', function () {
    expect(money_format(1234.56))->toBe('1,234.56');
    expect(money_format(0))->toBe('0.00');
    expect(money_format(999999.99))->toBe('999,999.99');
});

it('gets flash variant classes', function () {
    $variant = flash_variant('success');

    expect($variant)->toHaveKey('border');
    expect($variant)->toHaveKey('bg');
    expect($variant)->toHaveKey('text');
    expect($variant['border'])->toBe('border-green-500');
});

it('returns user from helper', function () {
    $testUser = User::factory()->create();

    actingAs($testUser);

    expect(user())->toBeInstanceOf(User::class);
    expect(user()->id)->toBe($testUser->id);
});
```

**Example Unit Test for Actions:**

```php
<?php

use App\Actions\CreateProductAction;
use App\Models\Product;
use App\Models\User;

it('creates a product with valid data', function () {
    $user = User::factory()->create();

    $action = new CreateProductAction();
    $product = $action->execute([
        'name' => 'Test Product',
        'price' => 99.99,
        'user_id' => $user->id,
    ]);

    expect($product)->toBeInstanceOf(Product::class);
    expect($product->name)->toBe('Test Product');
    expect($product->price)->toBe(99.99);
});

it('throws exception with invalid data', function () {
    $action = new CreateProductAction();

    $action->execute([]);
})->throws(ValidationException::class);
```

**Example Unit Test for Policies:**

```php
<?php

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;

beforeEach(function () {
    $this->policy = new ProductPolicy();
    $this->user = User::factory()->create();
});

it('allows viewing products with correct permission', function () {
    $this->user->givePermissionTo('products.view.list');

    expect($this->policy->viewAny($this->user))->toBeTrue();
});

it('denies viewing products without permission', function () {
    expect($this->policy->viewAny($this->user))->toBeFalse();
});

it('allows superadmin to do anything', function () {
    $this->user->assignRole('superadmin');
    $product = Product::factory()->create();

    expect($this->policy->viewAny($this->user))->toBeTrue();
    expect($this->policy->view($this->user, $product))->toBeTrue();
    expect($this->policy->create($this->user))->toBeTrue();
    expect($this->policy->update($this->user, $product))->toBeTrue();
    expect($this->policy->delete($this->user, $product))->toBeTrue();
});
```

**Example Unit Test for Exceptions:**

```php
<?php

use App\Exceptions\ActionException;
use App\Exceptions\ContractException;

it('throws action exception for missing model property', function () {
    expect(fn () => ActionException::throwIf(true, 'missingModelProperty', 'TestClass'))
        ->toThrow(ActionException::class, 'Missing model property in class TestClass');
});

it('throws contract exception for missing contract', function () {
    ContractException::throwIf(true, 'missingContract', 'MyClass', 'MyInterface');
})->throws(ContractException::class, 'MyClass did not implements MyInterface');

it('does not throw when condition is false', function () {
    ActionException::throwIf(false, 'missingModelProperty', 'TestClass');

    expect(true)->toBeTrue(); // If we get here, no exception was thrown
});
```

### Testing Livewire Components

```php
<?php

use App\Livewire\ProductForm;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

it('renders product form component', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(ProductForm::class)
        ->assertStatus(200);
});

it('can create product via livewire', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('products.create.item');

    actingAs($user);

    Livewire::test(ProductForm::class)
        ->set('name', 'Test Product')
        ->set('price', 99.99)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('product-created');

    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
});

it('validates livewire input', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(ProductForm::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name']);
});

it('displays alert on success', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('products.create.item');

    actingAs($user);

    Livewire::test(ProductForm::class)
        ->set('name', 'Test Product')
        ->set('price', 99.99)
        ->call('save')
        ->assertDispatched('displayAlert', title: 'Success');
});
```

### Testing with Factories

Use factories for consistent test data:

```php
<?php

use App\Models\Product;
use App\Models\User;

it('creates products with factory', function () {
    $product = Product::factory()->create();

    expect($product->uuid)->toBeString();
    expect($product->name)->toBeString();
});

it('creates products with custom attributes', function () {
    $product = Product::factory()->create([
        'name' => 'Custom Product',
        'price' => 199.99,
    ]);

    expect($product->name)->toBe('Custom Product');
    expect($product->price)->toBe(199.99);
});

it('creates multiple products', function () {
    $products = Product::factory()->count(5)->create();

    expect($products)->toHaveCount(5);
});

it('makes products without saving', function () {
    $product = Product::factory()->make();

    expect($product->exists)->toBeFalse();
    expect($product->name)->toBeString();
});
```

### Pest Helper Functions

**Common Assertions:**

```php
// Equality
expect($value)->toBe($expected);
expect($value)->toEqual($expected);

// Types
expect($value)->toBeString();
expect($value)->toBeInt();
expect($value)->toBeBool();
expect($value)->toBeArray();
expect($value)->toBeObject();
expect($value)->toBeNull();
expect($value)->toBeInstanceOf(Product::class);

// Collections
expect($array)->toHaveCount(5);
expect($array)->toContain('value');
expect($array)->toHaveKey('key');
expect($array)->not->toBeEmpty();

// Truthiness
expect($value)->toBeTrue();
expect($value)->toBeFalse();
expect($value)->toBeTruthy();
expect($value)->toBeFalsy();

// Strings
expect($string)->toContain('substring');
expect($string)->toStartWith('prefix');
expect($string)->toEndWith('suffix');
expect($string)->toMatch('/pattern/');

// Numbers
expect($number)->toBeGreaterThan(5);
expect($number)->toBeLessThan(10);
expect($number)->toBeGreaterThanOrEqual(5);
expect($number)->toBeLessThanOrEqual(10);
expect($number)->toBeBetween(5, 10);
```

**Datasets (Parameterized Tests):**

```php
it('validates email formats', function (string $email, bool $valid) {
    $validator = validator(['email' => $email], ['email' => 'email']);

    expect($validator->passes())->toBe($valid);
})->with([
    ['test@example.com', true],
    ['invalid.email', false],
    ['user@domain.co.uk', true],
    ['@example.com', false],
]);
```

**Setup and Teardown:**

```php
beforeEach(function () {
    // Runs before each test
    $this->user = User::factory()->create();
});

afterEach(function () {
    // Runs after each test
    // Clean up if needed
});

beforeAll(function () {
    // Runs once before all tests in the file
});

afterAll(function () {
    // Runs once after all tests in the file
});
```

### Architecture Tests

Location: `tests/Feature/ArchitectureTest.php`

```php
// Enforce architectural rules
it('does not using url method')
    ->expect('App')
    ->not->toUse(['url']);

it('runs on PHP 8.4 or above')
    ->expect('PHP_VERSION_ID')
    ->toBeGreaterThanOrEqual(80400);

it('ensures all models extend Base')
    ->expect('App\Models')
    ->toExtend('App\Models\Base')
    ->ignoring('App\Models\Base');

it('ensures policies have required methods')
    ->expect('App\Policies')
    ->toHaveMethod(['viewAny', 'view', 'create', 'update', 'delete']);

it('ensures controllers use authorization')
    ->expect('App\Http\Controllers')
    ->toUse('Illuminate\Foundation\Auth\Access\AuthorizesRequests');
```

### Test Environment

Configuration in `phpunit.xml`:

- SQLite in-memory database
- Array cache driver
- Sync queue
- Telescope disabled
- Pulse disabled
- Fast bcrypt rounds for testing

---

## 📦 Code Quality

### Laravel Pint (Code Formatting)

```bash
composer format  # Format code using Laravel Pint
```

**Configuration**: `pint.json`

- Preset: Laravel
- Relaxed PHPDoc rules (documentation flexibility)
- Excludes: vendor, node_modules, storage, bootstrap/cache

**Auto-formatting**: GitHub Actions runs Pint on every push

### PHPStan (Static Analysis)

```bash
composer analyse  # Run PHPStan analysis
```

**Configuration**: `phpstan.neon.dist`

- Integrated with Larastan for Laravel-specific rules
- Level: Maximum strictness
- Paths: app, config, database, routes, support, tests

### Rector (Automated Refactoring)

```bash
composer rector  # Refactor code to modern standards
```

**Configuration**: `rector.php`

- Target: Laravel 11, PHP 8.3
- Sets: Laravel Level Set, Code Quality
- Caching enabled in `storage/rector`
- Skips: `CompactToVariablesRector` (Laravel convention)

### PHPLint

```bash
composer lint  # Check PHP syntax errors
```

---

## 🚀 Development Workflow

### Local Development

```bash
composer dev  # Runs concurrently:
              # - php artisan serve (server)
              # - php artisan queue:listen (queue worker)
              # - php artisan pail (real-time logs)
              # - npm run dev (Vite HMR)
```

### Common Commands

```bash
# Frontend
npm run dev           # Start Vite dev server with HMR
npm run build         # Build production assets
bin/build-fe-assets   # Custom build script
bin/reinstall-npm     # Clean reinstall of node_modules

# Backend
php artisan migrate   # Run migrations
php artisan db:seed   # Run seeders
php artisan reload:db # Drop, migrate, and seed (custom command)

# Queue
php artisan queue:work
php artisan horizon   # For Redis-based queues

# Monitoring
php artisan telescope # Access via /telescope
php artisan horizon   # Access via /horizon
php artisan pail      # Real-time logs

# Deployment
bin/deploy -b main    # Deploy specific branch
bin/backup-app        # Backup application
bin/backup-media      # Backup media files
```

---

## 🔄 Custom Exceptions

Use static exception helpers with the `InteractsWithExceptions` trait:

```php
use App\Concerns\InteractsWithExceptions;

class CustomException extends Exception
{
    use InteractsWithExceptions;
}

// Usage:
CustomException::throwIf($condition, 'methodName', 'message', ...$args);
CustomException::throwUnless($condition, 'methodName', 'message', ...$args);
```

**Built-in Custom Exceptions:**

- `ActionException`: For action-related errors
- `ContractException`: For contract/interface violations
- `ThrowException`: General purpose exception

**Example:**

```php
ContractException::throwIf(
    !$object instanceof RequiredInterface,
    'missingContract',
    get_class($object),
    RequiredInterface::class
);
```

---

## 🎯 Best Practices

### Models

- ✅ **Always** extend `App\Models\Base`
- ✅ Use **UUIDs** for primary keys
- ✅ Enable **soft deletes** for user-facing data
- ✅ Implement **media conversions** when using media
- ✅ Keep models **thin** - move logic to Actions or Services

### Controllers

- ✅ Use **resource controllers** for CRUD operations
- ✅ Authorize using **policies**: `$this->authorize('view', $model)`
- ✅ Return **flash messages** on success/error
- ✅ Use **form requests** for validation

### Livewire Components

- ✅ Use **traits** for common functionality (alerts, confirmations)
- ✅ Keep components **focused** and single-responsibility
- ✅ Use **events** for component communication
- ✅ Authorize actions: `$this->authorize('update', $model)`

### Routes

- ✅ Organize routes in `routes/web/` by feature
- ✅ Use **resource routes** when possible
- ✅ Apply **middleware** for auth and permissions
- ✅ Use **route model binding** with UUIDs

### Helpers

- ✅ Create **domain-specific** helper files in `support/`
- ✅ Use **short, descriptive** function names
- ✅ Always check `function_exists()` before defining
- ✅ Document parameters and return types

### Security

- ✅ **Always** authorize actions in controllers and Livewire
- ✅ Use **policies** for all models
- ✅ Validate **all** user input
- ✅ Use **prepared statements** (Eloquent does this)
- ✅ Enable **auditing** on sensitive models

### Testing

- ✅ Write **feature tests** for user workflows
- ✅ Write **architecture tests** to enforce rules
- ✅ Test **authorization** separately
- ✅ Use **factories** for test data
- ✅ Aim for **high coverage** on business logic

---

## 📚 Packages & Dependencies

### Core Packages

- **spatie/laravel-permission**: Role and permission management
- **spatie/laravel-medialibrary**: Media/file management
- **owen-it/laravel-auditing**: Model auditing trail
- **laravel/telescope**: Debugging and monitoring
- **laravel/horizon**: Queue monitoring
- **cleaniquecoders/traitify**: Common traits and contracts
- **cleaniquecoders/laravel-media-secure**: Protected file access

### Authentication & Security

- **yadahan/laravel-authentication-log**: Login tracking
- **lab404/laravel-impersonate**: User impersonation

### Development Tools

- **barryvdh/laravel-debugbar**: Debug toolbar
- **larastan/larastan**: PHPStan + Laravel
- **driftingly/rector-laravel**: Rector + Laravel rules
- **pestphp/pest-plugin-arch**: Architecture testing
- **cleaniquecoders/laravel-db-doc**: Database documentation

### Frontend

- **blade-ui-kit/blade-icons**: Blade icon components
- **mallardduck/blade-lucide-icons**: Lucide icon set

---

## 🌍 Environment Configuration

### Required Environment Variables

```env
# Application
APP_NAME="Your App Name"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Superadmin Seeder
SUPERADMIN_NAME="Admin Name"
SUPERADMIN_EMAIL="admin@example.com"
SUPERADMIN_PASSWORD=password

# Database
DB_CONNECTION=mysql
DB_DATABASE=your_database_name

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Media
MEDIA_DISK=media

# Features
ACCESS_CONTROL_ENABLED=true
ADMIN_ENABLED=true
TELESCOPE_ENABLED=true
```

### Telescope Configuration

Telescope watchers can be individually enabled/disabled:

- `TELESCOPE_QUERY_WATCHER=false` - Disable query tracking
- `TELESCOPE_REQUEST_WATCHER=false` - Disable request tracking
- `TELESCOPE_BATCH_WATCHER=false` - Disable batch tracking
- etc.

---

## 📝 Custom Artisan Stubs

Custom stubs available in `stubs/` directory:

```bash
php artisan make:model Product        # Uses stubs/model.stub (extends Base)
php artisan make:migration CreateX    # Uses stubs/migration.create.stub
php artisan make:test ProductTest     # Uses stubs/pest.stub (Pest syntax)
php artisan make:policy ProductPolicy # Uses stubs/policy.stub
```

**Stub Customization:**
All stubs extend Base model and follow project conventions automatically.

---

## 🐳 Docker & DevOps

### Docker Compose Services

- **MySQL**: Database server (port 3306)
- **Redis**: Cache and session store (port 6379)
- **Mailpit**: Mail testing - SMTP (1025), UI (8025)
- **Meilisearch**: Full-text search engine (port 7700)
- **MinIO**: S3-compatible storage (ports 9000, 9001)

### Deployment Script

```bash
# Deploy specific branch
bin/deploy -b main -r origin -p /var/www/project-name

# Deploy latest tag
bin/deploy  # Automatically finds and deploys latest Git tag
```

**Deployment steps:**

1. Pull latest code
2. Install Composer dependencies
3. Install NPM dependencies
4. Build frontend assets
5. Run migrations
6. Clear caches
7. Restart queue workers
8. Optimize Laravel

---

## 🎓 Learning Resources

### Package Documentation

- [Laravel](https://laravel.com/docs)
- [Livewire](https://livewire.laravel.com)
- [TailwindCSS](https://tailwindcss.com)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary)
- [Pest PHP](https://pestphp.com)
- [Laravel Telescope](https://laravel.com/docs/telescope)
- [Laravel Horizon](https://laravel.com/docs/horizon)

### Project Structure

- Access control: `config/access-control.php`
- Helpers: `support/` directory
- Custom traits: `app/Concerns/`
- Policies: `app/Policies/`
- Livewire: `app/Livewire/`

---

## ⚠️ Common Pitfalls to Avoid

1. ❌ **Don't** extend `Illuminate\Database\Eloquent\Model` - use `App\Models\Base`
2. ❌ **Don't** use auto-increment IDs - UUIDs are the standard
3. ❌ **Don't** forget to authorize actions in controllers/Livewire
4. ❌ **Don't** bypass policies - always use `$this->authorize()`
5. ❌ **Don't** use `url()` helper - use named routes and `route()` instead
6. ❌ **Don't** forget to add policies to `AuthServiceProvider`
7. ❌ **Don't** use PHPUnit syntax - use Pest syntax for tests
8. ❌ **Don't** hardcode permissions - use `config/access-control.php`
9. ❌ **Don't** forget to run migrations after publishing package configs
10. ❌ **Don't** skip code quality tools - run Pint, PHPStan, and Rector regularly

---

## ✨ Code Generation Guidelines

When generating code for this project:

1. **Models**: Always extend `App\Models\Base`, never Eloquent Model directly
2. **Tests**: Use Pest syntax with `it()` and `expect()`, not PHPUnit
3. **Permissions**: Follow `module.action.target` naming convention
4. **Routes**: Prefer resource routes with UUID route model binding
5. **Policies**: Implement all 7 standard methods (viewAny, view, create, update, delete, restore, forceDelete)
6. **Livewire**: Use traits for alerts and confirmations
7. **Helpers**: Create in `support/` directory with domain-specific organization
8. **Migrations**: Use UUID primary keys, timestamps, and soft deletes where appropriate
9. **Views**: Use TailwindCSS utilities and Blade components
10. **JavaScript**: Minimal - prefer Livewire + Alpine.js over heavy JavaScript

---

**Last Updated**: Generated by CleaniqueCoders Kickoff Template
**Version**: Laravel 12+ | PHP 8.4+ | Livewire 4 | TailwindCSS 4
