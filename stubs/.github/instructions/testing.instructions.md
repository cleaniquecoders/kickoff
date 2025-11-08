<!-- Based on: https://github.com/github/awesome-copilot/blob/main/collections (Testing & Test Automation collection) -->
---
applyTo: "**/tests/**/*.php,**/*Test.php,**/*test.php"
description: "Testing standards and practices for Laravel applications using Pest PHP"
---

# Testing Instructions for Laravel Applications

This project uses **Pest PHP** as the testing framework following the CleaniqueCoders Kickoff template standards.

## Testing Principles

- **Write tests first**: Follow TDD principles where possible
- **Test behavior, not implementation**: Focus on what the code does, not how it does it
- **One assertion per test**: Keep tests focused and clear
- **Descriptive test names**: Use clear, readable test descriptions
- **Arrange-Act-Assert**: Structure tests with clear setup, action, and verification phases

## Test Structure Standards

### Pest PHP Syntax
Use Pest PHP syntax with `it()` and `expect()`, not PHPUnit syntax:

```php
// ✅ CORRECT: Pest syntax
it('can create a product', function () {
    $product = Product::factory()->create();

    expect($product)->toBeInstanceOf(Product::class);
    expect($product->uuid)->toBeString();
});

// ❌ WRONG: PHPUnit syntax
public function testCanCreateProduct()
{
    $this->assertInstanceOf(Product::class, $product);
}
```

### Test Organization
- **Feature Tests** (`tests/Feature/`): Test complete user workflows and HTTP interactions
- **Unit Tests** (`tests/Unit/`): Test individual methods, classes, and logic in isolation
- **Architecture Tests** (`tests/Feature/ArchitectureTest.php`): Enforce architectural rules

## Feature Test Patterns

### HTTP Testing
```php
it('can view product list', function () {
    $products = Product::factory()->count(3)->create();

    $response = get('/products');

    $response->assertStatus(200);
    $response->assertViewIs('products.index');
    $response->assertSee($products->first()->name);
});

it('requires authentication to create products', function () {
    post('/products', ['name' => 'Test Product'])
        ->assertRedirect('/login');
});
```

### Authorization Testing
```php
it('allows admin to delete products', function () {
    $admin = User::factory()->create();
    $admin->assignRole('administrator');
    $product = Product::factory()->create();

    actingAs($admin)
        ->delete("/products/{$product->uuid}")
        ->assertRedirect('/products');

    expect(Product::find($product->id))->toBeNull();
});
```

### Validation Testing
```php
it('validates required fields', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/products', [])
        ->assertSessionHasErrors(['name', 'price']);
});
```

## Unit Test Patterns

### Model Testing
```php
it('generates a slug from name', function () {
    $product = Product::factory()->make(['name' => 'Test Product']);

    expect($product->slug)->toBe('test-product');
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

### Helper Testing
```php
it('formats money correctly', function () {
    expect(money_format(1234.56))->toBe('1,234.56');
    expect(money_format(0))->toBe('0.00');
});

it('returns user from helper', function () {
    $user = User::factory()->create();

    actingAs($user);

    expect(user())->toBeInstanceOf(User::class);
    expect(user()->id)->toBe($user->id);
});
```

### Policy Testing
```php
beforeEach(function () {
    $this->policy = new ProductPolicy();
    $this->user = User::factory()->create();
});

it('allows viewing products with correct permission', function () {
    $this->user->givePermissionTo('products.view.list');

    expect($this->policy->viewAny($this->user))->toBeTrue();
});

it('allows superadmin to do anything', function () {
    $this->user->assignRole('superadmin');
    $product = Product::factory()->create();

    expect($this->policy->viewAny($this->user))->toBeTrue();
    expect($this->policy->view($this->user, $product))->toBeTrue();
});
```

## Livewire Testing

```php
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
```

## Database Testing

### Use Factories
```php
it('creates products with factory', function () {
    $product = Product::factory()->create([
        'name' => 'Custom Product',
        'price' => 199.99,
    ]);

    expect($product->name)->toBe('Custom Product');
    expect($product->price)->toBe(199.99);
});
```

### Database Assertions
```php
it('stores product in database', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/products', [
            'name' => 'Test Product',
            'price' => 99.99,
        ]);

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'price' => 99.99,
    ]);
});
```

## Test Setup and Helpers

### Setup Functions
```php
beforeEach(function () {
    // Runs before each test
    $this->user = User::factory()->create();
});

beforeAll(function () {
    // Runs once before all tests in the file
});
```

### Datasets (Parameterized Tests)
```php
it('validates email formats', function (string $email, bool $valid) {
    $validator = validator(['email' => $email], ['email' => 'email']);

    expect($validator->passes())->toBe($valid);
})->with([
    ['test@example.com', true],
    ['invalid.email', false],
    ['user@domain.co.uk', true],
]);
```

## Common Assertions

```php
// Equality
expect($value)->toBe($expected);
expect($value)->toEqual($expected);

// Types
expect($value)->toBeString();
expect($value)->toBeInstanceOf(Product::class);

// Collections
expect($array)->toHaveCount(5);
expect($array)->toContain('value');

// Truthiness
expect($value)->toBeTrue();
expect($value)->toBeFalse();

// Strings
expect($string)->toContain('substring');
expect($string)->toStartWith('prefix');
```

## Test Environment

- Use SQLite in-memory database for fast tests
- Use `RefreshDatabase` trait to reset database between tests
- Mock external services and APIs
- Use factories for consistent test data
- Test in isolation - no dependencies between tests

## Best Practices

1. **Test the happy path first**, then edge cases
2. **Use descriptive test names** that explain the scenario
3. **Keep tests simple** and focused on one behavior
4. **Use factories** instead of manual model creation
5. **Test authorization separately** from functionality
6. **Mock external dependencies** to ensure test reliability
7. **Run tests frequently** during development
8. **Aim for high coverage** on business logic, not necessarily 100%
9. **Test error conditions** and validation failures
10. **Keep test data minimal** - only create what you need
