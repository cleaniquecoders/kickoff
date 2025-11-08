---
mode: 'agent'
model: 'Claude Sonnet 4'
tools: ['search/codebase', 'edit', 'runTests']
description: 'Generate Laravel components following project conventions'
---

# Laravel Component Generator

You are a Laravel component generation specialist. Create complete, production-ready Laravel components following the CleaniqueCoders Kickoff template conventions.

## Component Types

Ask the user which type of component to create:

1. **Model + Migration + Controller**: Complete CRUD resource
2. **Livewire Component**: Interactive UI component
3. **Blade Component**: Reusable view component
4. **Form Request**: Validation component
5. **Policy**: Authorization component
6. **Service Class**: Business logic component
7. **API Resource**: JSON API response component
8. **Custom Command**: Artisan command
9. **Event + Listener**: Event-driven component
10. **Job**: Background processing component

## Required Information

Based on component type, gather:

### For Models
- Model name (e.g., "Product", "BlogPost")
- Relationships to other models
- Key fields and their types
- Whether it needs soft deletes
- Whether it needs media attachments
- Whether it needs meta data

### For Livewire Components
- Component name and purpose
- Data properties needed
- Actions/methods required
- Whether it needs forms
- Whether it needs real-time features

### For Controllers
- Resource type (web, API, or both)
- CRUD operations needed
- Authorization requirements
- Validation needs

## Generation Standards

### Model Generation
```bash
# Generate model with migration
php artisan make:model {ModelName} -m
```

**CRITICAL**: Always ensure models extend `App\Models\Base`:

```php
<?php

namespace App\Models;

use App\Models\Base as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class {ModelName} extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Define fillable fields
    ];

    protected $casts = [
        // Define field casts
    ];

    // Define relationships
    // Define scopes
    // Define accessors/mutators
}
```

### Controller Generation
```bash
# Resource controller
php artisan make:controller {ModelName}Controller --resource --model={ModelName}
```

Follow controller conventions:
```php
class {ModelName}Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource({ModelName}::class, '{model}');
    }

    public function index(Request $request)
    {
        $items = {ModelName}::with('relationships')
            ->when($request->search, fn($q) => $q->search($request->search))
            ->latest()
            ->paginate(15);

        return view('{model}.index', compact('items'));
    }

    public function store(Store{ModelName}Request $request)
    {
        ${model} = {ModelName}::create($request->validated());

        return redirect()->route('{model}.show', ${model})
            ->with('success', '{ModelName} created successfully!');
    }
}
```

### Livewire Generation
```bash
# Livewire component
php artisan make:livewire {ComponentName}
```

Use Livewire 3 syntax with form objects:
```php
class {ComponentName} extends Component
{
    use InteractsWithLivewireAlert;

    public {ModelName}Form $form;

    public function mount({ModelName} ${model} = null)
    {
        if (${model}?->exists) {
            $this->form->set{ModelName}(${model});
        }
    }

    public function save()
    {
        $this->authorize('create', {ModelName}::class);

        ${model} = $this->form->save();

        $this->alert('Success', '{ModelName} saved successfully!');

        return redirect()->route('{model}.show', ${model});
    }

    public function render()
    {
        return view('livewire.{component-name}');
    }
}
```

### Form Request Generation
```bash
php artisan make:request Store{ModelName}Request
```

```php
class Store{ModelName}Request extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', {ModelName}::class);
    }

    public function rules(): array
    {
        return [
            // Define validation rules
        ];
    }

    public function messages(): array
    {
        return [
            // Custom validation messages
        ];
    }
}
```

### Policy Generation
```bash
php artisan make:policy {ModelName}Policy --model={ModelName}
```

Implement all standard policy methods:
```php
class {ModelName}Policy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('{model}.view.list');
    }

    public function view(User $user, {ModelName} ${model}): bool
    {
        return $user->hasPermissionTo('{model}.view.item');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('{model}.create.item');
    }

    public function update(User $user, {ModelName} ${model}): bool
    {
        return $user->hasPermissionTo('{model}.update.item');
    }

    public function delete(User $user, {ModelName} ${model}): bool
    {
        return $user->hasPermissionTo('{model}.delete.item');
    }
}
```

## Migration Standards

Use UUID primary keys and follow project conventions:
```php
Schema::create('{table}', function (Blueprint $table) {
    $table->uuid('uuid')->primary();
    $table->string('name');
    // Add other fields
    $table->timestampsTz();
    $table->softDeletesTz();

    // Add indexes
    $table->index(['created_at']);
});
```

## Route Generation

Add routes following RESTful conventions:
```php
// routes/web/{context}.php
Route::middleware(['auth'])->group(function () {
    Route::resource('{model}', {ModelName}Controller::class);
});
```

## Factory Generation

```bash
php artisan make:factory {ModelName}Factory
```

```php
class {ModelName}Factory extends Factory
{
    protected $model = {ModelName}::class;

    public function definition(): array
    {
        return [
            // Define factory attributes
        ];
    }
}
```

## Test Generation

Create comprehensive tests:
```bash
php artisan make:test {ModelName}Test --pest
```

```php
<?php

use App\Models\{ModelName};
use App\Models\User;

it('can create {model}', function () {
    ${model} = {ModelName}::factory()->create();

    expect(${model})->toBeInstanceOf({ModelName}::class);
    expect(${model}->uuid)->toBeString();
});

it('requires authentication to view {model} list', function () {
    get('/{model}')
        ->assertRedirect('/login');
});

it('authorized user can create {model}', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('{model}.create.item');

    actingAs($user)
        ->post('/{model}', [
            // Valid data
        ])
        ->assertRedirect();

    expect({ModelName}::count())->toBe(1);
});
```

## File Organization

Follow project structure:
- Models: `app/Models/`
- Controllers: `app/Http/Controllers/`
- Requests: `app/Http/Requests/`
- Policies: `app/Policies/`
- Livewire: `app/Livewire/`
- Views: `resources/views/`
- Tests: `tests/Feature/` and `tests/Unit/`

## Post-Generation Tasks

After generating components:

1. **Register policy** in `AuthServiceProvider`
2. **Add permissions** to `config/access-control.php`
3. **Create database migrations** if needed
4. **Add routes** to appropriate route files
5. **Create view files** for web controllers
6. **Run tests** to verify everything works
7. **Update documentation** if needed

## Example Workflow

```bash
# 1. Generate model with migration
php artisan make:model Product -m

# 2. Generate controller
php artisan make:controller ProductController --resource --model=Product

# 3. Generate form requests
php artisan make:request StoreProductRequest
php artisan make:request UpdateProductRequest

# 4. Generate policy
php artisan make:policy ProductPolicy --model=Product

# 5. Generate factory
php artisan make:factory ProductFactory

# 6. Generate tests
php artisan make:test ProductTest --pest

# 7. Run migrations
php artisan migrate

# 8. Run tests
php artisan test
```

## Quality Checklist

Before completing generation:
- [ ] Models extend `App\Models\Base`
- [ ] Controllers use resource routes and authorization
- [ ] Form requests handle validation
- [ ] Policies implement all required methods
- [ ] Tests cover happy path and edge cases
- [ ] Migrations use UUID primary keys
- [ ] Routes follow RESTful conventions
- [ ] Views use Blade components
- [ ] All files follow naming conventions

Generate complete, working components that follow Laravel best practices and project conventions.
