------
applyTo: '**'
------

# Backend Development Instructions

This document defines backend development standards, patterns, and best practices for the application.

## Core Principles

1. **Type Safety First**: Use PHP 8.4+ features including typed properties, enums, and match expressions
2. **Convention Over Configuration**: Follow Laravel conventions and PSR-12 standards
3. **Security by Default**: Always validate input, use policies, and audit sensitive operations
4. **Consistency**: Use established patterns (Base model, Traitify contracts, enum structure)
5. **Testability**: Write testable code with proper dependency injection

## Model Development Standards

### Base Model Pattern

**ALL models MUST extend `App\Models\Base`** which provides:

```php
use App\Models\Base;
use Illuminate\Database\Eloquent\SoftDeletes;

class YourModel extends Base
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'status',
        // ... other fields
    ];

    protected $casts = [
        'status' => YourStatusEnum::class, // Always use enums for status/type fields
        'meta' => 'array',
        'created_at' => 'datetime',
    ];
}
```

**Base model includes:**
- `InteractsWithUuid` - Dual-key pattern: auto-increment `id` for internal DB relations + `uuid` column for public-facing identifiers
- `InteractsWithMedia` - Spatie Media Library integration
- `InteractsWithMeta` - JSON metadata storage
- `InteractsWithUser` - User tracking (created_by, updated_by)
- `InteractsWithToken` - Secure token generation
- `InteractsWithSearchable` - Search functionality
- `InteractsWithResourceRoute` - Resource routing helpers
- `AuditableTrait` - Activity logging via owen-it/laravel-auditing

### Multi-Tenancy with organization_id

**Models that require multi-tenant isolation SHOULD include `organization_id`:**

```php
protected $fillable = [
    'organization_id', // For multi-tenant scoping
    // ... other fields
];

// Relationships
public function organization(): BelongsTo
{
    return $this->belongsTo(Organization::class);
}

// Scope queries by organization
public function scopeForOrganization(Builder $query, string $organizationId): Builder
{
    return $query->where('organization_id', $organizationId);
}
```

**When to include organization_id:**
- Data that belongs to a specific organization
- Resources with organization-level access control

**When NOT to include organization_id:**
- System-level data (users, permissions, roles - managed by Spatie)
- Global resources

### Soft Deletes

**ALWAYS use soft deletes for user-facing models:**

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class YourModel extends Base
{
    use SoftDeletes;

    // Soft delete is automatically handled
    // deleted_at column added via migration
}
```

**Exceptions (no soft deletes):**
- Pivot tables
- System logs
- Audit trails
- Temporary/cache tables

## Enum Development Standards

### When to Use Enums

Use enums for:
- Status fields (active, pending, completed, etc.)
- Type fields (general, vip, student, etc.)
- Method fields (qr_code, manual, rfid, etc.)
- Role fields (owner, manager, crew, etc.)
- Any fixed set of string values stored in database

### Enum Structure (REQUIRED)

**All enums MUST:**
1. Be placed in `app/Enums/`
2. Implement `CleaniqueCoders\Traitify\Contracts\Enum`
3. Use `InteractsWithEnum` trait
4. Be string-backed
5. Include `label()` and `description()` methods

**Use the enum.stub:**

```bash
php artisan make:enum YourStatusEnum
```

**Example:**

```php
<?php

namespace App\Enums;

use CleaniqueCoders\Traitify\Contracts\Enum as Contract;
use CleaniqueCoders\Traitify\Concerns\InteractsWithEnum;

enum Status: string implements Contract
{
    use InteractsWithEnum;

    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DRAFT => 'Resource is being prepared.',
            self::ACTIVE => 'Resource is active.',
            self::INACTIVE => 'Resource is inactive.',
        };
    }
}
```

### Enum Usage in Models

**Always cast enum fields:**

```php
protected $casts = [
    'status' => Status::class,
    'type' => Type::class,
];
```

### Enum Usage in Migrations

**Use native enum in migrations:**

```php
Schema::create('resources', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->index();
    $table->enum('status', ['draft', 'active', 'inactive'])
        ->default('draft');
});
```

### Enum Usage in Factories

**Use enum cases in factories:**

```php
use App\Enums\Status;

public function definition(): array
{
    return [
        'status' => fake()->randomElement([
            Status::DRAFT,
            Status::ACTIVE,
            Status::INACTIVE,
        ]),
    ];
}

// State methods
public function active(): static
{
    return $this->state(['status' => Status::ACTIVE]);
}
```

### Common Enum Examples

**Events:**
- `EventStatus` - draft, active, cancelled, completed
- `EventVisibility` - public, private, unlisted

**Communications:**
- `EmailCampaignStatus` - draft, scheduled, sent
- `EmailMessageStatus` - pending, sent, bounced, failed

**Organization:**
- `OrganizationStatus` - active, inactive, suspended

## Database Standards

### Dual-Key Pattern (Auto-Increment ID + UUID)

**ALL models use a dual-key pattern: auto-increment `id` for internal DB relations + `uuid` column for public-facing identifiers:**

```php
// Migration
Schema::create('your_table', function (Blueprint $table) {
    $table->id();                   // Auto-increment int PK (internal relations)
    $table->uuid('uuid')->index();  // UUID column (public-facing identifier)
    $table->foreignId('organization_id')->constrained();
    // ...
});

// Model inherits UUID from Base model automatically
```

### Foreign Keys

**Always add proper constraints:**

```php
$table->foreignUuid('organization_id')
    ->constrained('organizations')
    ->onUpdate('cascade')
    ->onDelete('restrict'); // or cascade for child records

$table->foreignUuid('event_id')
    ->constrained()
    ->cascadeOnDelete(); // when parent must delete children
```

### Indexes

**Add indexes for:**
- Foreign keys (organization_id, event_id, user_id)
- Status fields with organization_id
- Frequently filtered/sorted columns
- Unique constraints (slug, code, serial)

```php
$table->index(['organization_id', 'status']);
$table->index(['event_id', 'created_at']);
$table->unique(['organization_id', 'slug']);
```

### Money Storage

**Always use integer (minor units) for money:**

```php
// Migration
$table->bigInteger('price_amount')->default(0); // cents/pence
$table->string('price_currency', 3)->default('MYR'); // ISO 4217

// Model
protected $casts = [
    'price_amount' => 'integer',
];

// Usage: Store RM 79.90 as 7990
```

## Relationship Standards

### Naming Conventions

```php
// BelongsTo - singular, matches FK
public function organization(): BelongsTo
{
    return $this->belongsTo(Organization::class);
}

// HasMany - plural
public function events(): HasMany
{
    return $this->hasMany(Event::class);
}

// BelongsToMany - plural
public function tags(): BelongsToMany
{
    return $this->belongsToMany(Tag::class, 'event_tag')
        ->withTimestamps();
}
```

### Return Types

**ALWAYS specify return types:**

```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

public function event(): BelongsTo // REQUIRED
{
    return $this->belongsTo(Event::class);
}
```

## Validation Standards

### Form Requests

**Use Form Requests for validation (NOT controller validation):**

```bash
php artisan make:request StoreEventRequest
```

```php
<?php

namespace App\Http\Requests;

use App\Enums\EventStatus;
use App\Enums\EventVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Event::class);
    }

    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'uuid', 'exists:organizations,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:events,slug'],
            'status' => ['required', Rule::enum(EventStatus::class)],
            'visibility' => ['required', Rule::enum(EventVisibility::class)],
            'start_date' => ['required', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.enum' => 'Invalid status. Must be one of: draft, active, cancelled, completed.',
        ];
    }
}
```

### Enum Validation

**Always validate enum fields:**

```php
use Illuminate\Validation\Rule;
use App\Enums\EventStatus;

// In rules()
'status' => ['required', Rule::enum(EventStatus::class)],

// For updates (nullable)
'status' => ['sometimes', 'nullable', Rule::enum(EventStatus::class)],
```

## Authorization Standards

### Policy Structure

**Create policies for all models:**

```bash
php artisan make:policy EventPolicy --model=Event
```

```php
<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view events');
    }

    public function view(User $user, Event $event): bool
    {
        // Check organization membership
        return $user->isMemberOf($event->organization_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create events');
    }

    public function update(User $user, Event $event): bool
    {
        return $user->can('update events')
            && $user->isMemberOf($event->organization_id);
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->can('delete events')
            && $user->isOwnerOf($event->organization_id);
    }
}
```

### Feature Flags (Pennant)

**Use for premium features:**

```php
use Laravel\Pennant\Feature;
use App\Models\Organization;

// In controller/action
if (Feature::for($organization)->active('email_campaigns')) {
    // Premium feature enabled
}

// In policy
public function createCampaign(User $user, Organization $organization): bool
{
    return Feature::for($organization)->active('email_campaigns')
        && $user->can('create campaigns');
}

// In Blade
@feature('email_campaigns', $organization)
    <x-button>Create Campaign</x-button>
@endfeature
```

## Controller Standards

### Keep Controllers Thin

**Use Actions for business logic:**

```php
<?php

namespace App\Http\Controllers;

use App\Actions\Events\CreateEvent;
use App\Http\Requests\StoreEventRequest;

class EventController extends Controller
{
    public function store(StoreEventRequest $request, CreateEvent $action)
    {
        $this->authorize('create', Event::class);

        $event = $action->execute($request->validated());

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }
}
```

### Action Classes

```php
<?php

namespace App\Actions\Events;

use App\Models\Event;
use Illuminate\Support\Str;

class CreateEvent
{
    public function execute(array $data): Event
    {
        // Generate slug if not provided
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Create event
        $event = Event::create($data);

        // Audit log (automatic via Base model)

        return $event;
    }
}
```

## Factory Standards

### Factory Pattern

```php
<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Enums\EventVisibility;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'title' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(),
            'status' => fake()->randomElement([
                EventStatus::DRAFT,
                EventStatus::ACTIVE,
            ]),
            'visibility' => EventVisibility::PUBLIC,
            'start_date' => fake()->dateTimeBetween('+1 week', '+3 months'),
        ];
    }

    // State methods
    public function active(): static
    {
        return $this->state([
            'status' => EventStatus::ACTIVE,
            'visibility' => EventVisibility::PUBLIC,
        ]);
    }

    public function draft(): static
    {
        return $this->state(['status' => EventStatus::DRAFT]);
    }
}
```

## Testing Standards

### Model Tests

```php
use App\Enums\EventStatus;
use App\Models\Event;

it('creates event with enum status', function () {
    $event = Event::factory()->create([
        'status' => EventStatus::DRAFT,
    ]);

    expect($event->status)->toBeInstanceOf(EventStatus::class)
        ->and($event->status)->toBe(EventStatus::DRAFT);
});

it('scopes events by organization', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();

    Event::factory()->count(3)->create(['organization_id' => $org1->id]);
    Event::factory()->count(2)->create(['organization_id' => $org2->id]);

    expect(Event::forOrganization($org1->id)->count())->toBe(3);
});
```

## Architecture Compliance

### Prohibited Patterns

**❌ NEVER use in app code:**
- `dd()`, `dump()`, `ray()` - use logging instead
- `url()` - use `route()` for named routes
- `DB::raw()`, `DB::select()` - use Eloquent/Query Builder
- `env()` - only in config files
- UUID-only primary keys - use dual-key pattern (`id` + `uuid`)
- String status values - use enums

**✅ ALWAYS use:**
- Named routes: `route('events.show', $event)`
- Eloquent/Query Builder for queries
- Enums for status/type fields
- Dual-key pattern (`id` + `uuid`) in migrations
- Form Requests for validation
- Policies for authorization
- Soft deletes for user data

### Naming Conventions

**Required suffixes:**
- Controllers: `EventController`
- Policies: `EventPolicy`
- Form Requests: `StoreEventRequest`, `UpdateEventRequest`
- Actions: `CreateEvent`, `UpdateEvent`

**Directories:**
- Traits: `App\Concerns`
- Enums: `App\Enums`
- Interfaces: `App\Contracts`

## Migration Standards

### Template

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('your_table', function (Blueprint $table) {
            // Primary key + public identifier
            $table->id();                   // Auto-increment int PK (internal)
            $table->uuid('uuid')->index();  // UUID column (public-facing)

            // Foreign keys (multi-tenant)
            $table->foreignId('organization_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('restrict');

            // Business fields
            $table->string('name');
            $table->enum('status', ['draft', 'active', 'cancelled'])
                ->default('draft');

            // Metadata
            $table->json('meta')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('your_table');
    }
};
```

## Code Quality Checklist

Before committing, ensure:

- [ ] Model extends `App\Models\Base`
- [ ] Dual-key pattern: `$table->id()` + `$table->uuid('uuid')->index()`
- [ ] Multi-tenant models have `organization_id`
- [ ] Soft deletes for user-facing models
- [ ] All status/type fields use enums
- [ ] Enums implement Traitify contract
- [ ] Foreign keys have proper constraints
- [ ] Indexes on filtered/sorted columns
- [ ] Relationships have return types
- [ ] Validation in Form Requests
- [ ] Authorization via Policies
- [ ] No debug functions (dd, dump, ray)
- [ ] No raw SQL queries
- [ ] Factory uses enum cases
- [ ] Tests cover happy and error paths

## Quick Reference

### Command Cheat Sheet

```bash
# Generate with proper stubs
php artisan make:model Event
php artisan make:enum EventStatus
php artisan make:policy EventPolicy --model=Event
php artisan make:request StoreEventRequest
php artisan make:factory EventFactory --model=Event

# Code quality
composer format         # Format code
composer analyse        # Static analysis
composer test          # Run tests
composer test-arch     # Architecture tests

# Development
php artisan reload:all --dev  # Fresh DB + dev data
php artisan seed:dev          # Seed dev data only
```

### Common Pitfalls to Avoid

1. **Not using Base model** → Missing dual-key pattern (id + uuid), auditing, media
2. **String status values** → Use enums for type safety
3. **Missing organization_id** → Breaks multi-tenancy
4. **No soft deletes** → Data loss on accidental delete
5. **Controller validation** → Use Form Requests
6. **Missing return types** → Relationships need types
7. **Raw SQL queries** → Use Eloquent/Query Builder
8. **UUID-only primary keys** → Use dual-key pattern: `$table->id()` + `$table->uuid('uuid')->index()`

## Summary

Following these standards ensures:
- ✅ Type-safe code with enums
- ✅ Multi-tenant data isolation
- ✅ Consistent patterns across codebase
- ✅ Audit trails for sensitive operations
- ✅ Testable, maintainable code
- ✅ Architecture compliance
