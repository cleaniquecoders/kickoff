---
mode: 'agent'
model: 'Claude Sonnet 4'
tools: ['search/codebase', 'edit', 'runTests', 'problems']
description: 'Generate comprehensive tests using Pest PHP for Laravel applications'
---

# Test Generator for Laravel Applications

You are a testing specialist focused on generating comprehensive test suites using Pest PHP for Laravel applications following the CleaniqueCoders Kickoff template standards.

## Test Types to Generate

Ask the user what type of tests to create:

1. **Feature Tests**: Complete user workflows and HTTP interactions
2. **Unit Tests**: Individual methods, classes, and logic testing
3. **Livewire Tests**: Component interaction and state testing
4. **API Tests**: JSON API endpoint testing
5. **Authorization Tests**: Permission and policy testing
6. **Database Tests**: Model relationships and query testing
7. **Validation Tests**: Form request and validation rule testing
8. **Integration Tests**: Multi-component workflow testing

## Required Information

Gather information about:

### For Feature Tests
- Controller/route to test
- User roles involved
- Expected workflows
- Authorization requirements
- Validation scenarios

### For Unit Tests
- Class/method to test
- Input/output scenarios
- Edge cases to cover
- Dependencies to mock

### For Livewire Tests
- Component name
- Properties and methods
- User interactions
- Real-time features

## Pest PHP Standards

**ALWAYS use Pest syntax**, not PHPUnit:

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

## Feature Test Templates

### HTTP Controller Testing
```php
<?php

use App\Models\Product;
use App\Models\User;

describe('ProductController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('administrator');
    });

    describe('index', function () {
        it('shows product list to authenticated users', function () {
            $products = Product::factory()->count(3)->create();

            actingAs($this->user)
                ->get('/products')
                ->assertStatus(200)
                ->assertViewIs('products.index')
                ->assertSee($products->first()->name);
        });

        it('requires authentication', function () {
            get('/products')
                ->assertRedirect('/login');
        });

        it('filters products by search term', function () {
            $product1 = Product::factory()->create(['name' => 'Laravel Book']);
            $product2 = Product::factory()->create(['name' => 'Vue Guide']);

            actingAs($this->user)
                ->get('/products?search=Laravel')
                ->assertSee('Laravel Book')
                ->assertDontSee('Vue Guide');
        });
    });

    describe('store', function () {
        it('creates product with valid data', function () {
            $this->admin->givePermissionTo('products.create.item');

            actingAs($this->admin)
                ->post('/products', [
                    'name' => 'Test Product',
                    'price' => 99.99,
                    'description' => 'Test description',
                ])
                ->assertRedirect()
                ->assertSessionHas('success');

            expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
        });

        it('validates required fields', function () {
            actingAs($this->admin)
                ->post('/products', [])
                ->assertSessionHasErrors(['name', 'price']);
        });

        it('requires proper permissions', function () {
            actingAs($this->user)
                ->post('/products', [
                    'name' => 'Test Product',
                    'price' => 99.99,
                ])
                ->assertForbidden();
        });
    });

    describe('update', function () {
        it('updates product with valid data', function () {
            $product = Product::factory()->create();
            $this->admin->givePermissionTo('products.update.item');

            actingAs($this->admin)
                ->put("/products/{$product->uuid}", [
                    'name' => 'Updated Name',
                    'price' => $product->price,
                ])
                ->assertRedirect();

            expect($product->fresh()->name)->toBe('Updated Name');
        });

        it('prevents unauthorized updates', function () {
            $product = Product::factory()->create();

            actingAs($this->user)
                ->put("/products/{$product->uuid}", [
                    'name' => 'Hacked Name',
                ])
                ->assertForbidden();
        });
    });

    describe('destroy', function () {
        it('soft deletes product', function () {
            $product = Product::factory()->create();
            $this->admin->givePermissionTo('products.delete.item');

            actingAs($this->admin)
                ->delete("/products/{$product->uuid}")
                ->assertRedirect();

            expect($product->fresh())->toBeNull();
            expect(Product::withTrashed()->find($product->uuid))->toBeInstanceOf(Product::class);
        });
    });
});
```

### API Testing
```php
<?php

use App\Models\Product;
use App\Models\User;

describe('Product API', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $token = $this->user->createToken('test-token');
        $this->headers = ['Authorization' => 'Bearer ' . $token->plainTextToken];
    });

    describe('GET /api/products', function () {
        it('returns paginated products', function () {
            Product::factory()->count(25)->create();

            $response = $this->getJson('/api/products', $this->headers);

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'price',
                            'created_at'
                        ]
                    ],
                    'links',
                    'meta'
                ]);

            expect($response->json('data'))->toHaveCount(15); // Default pagination
        });

        it('filters products by search', function () {
            Product::factory()->create(['name' => 'Laravel Book']);
            Product::factory()->create(['name' => 'Vue Guide']);

            $response = $this->getJson('/api/products?search=Laravel', $this->headers);

            $response->assertStatus(200);
            expect($response->json('data'))->toHaveCount(1);
            expect($response->json('data.0.name'))->toBe('Laravel Book');
        });
    });

    describe('POST /api/products', function () {
        it('creates product with valid data', function () {
            $this->user->givePermissionTo('products.create.item');

            $data = [
                'name' => 'Test Product',
                'price' => 99.99,
                'description' => 'Test description'
            ];

            $response = $this->postJson('/api/products', $data, $this->headers);

            $response->assertStatus(201)
                ->assertJsonFragment($data);

            expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
        });

        it('validates required fields', function () {
            $response = $this->postJson('/api/products', [], $this->headers);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'price']);
        });
    });
});
```

## Unit Test Templates

### Model Testing
```php
<?php

use App\Models\Product;
use App\Models\User;
use App\Models\Category;

describe('Product Model', function () {
    it('extends App\\Models\\Base', function () {
        $product = new Product();

        expect($product)->toBeInstanceOf(App\Models\Base::class);
    });

    it('uses UUID as primary key', function () {
        $product = Product::factory()->create();

        expect($product->getKeyName())->toBe('uuid');
        expect($product->uuid)->toBeString();
        expect($product->uuid)->toHaveLength(36);
    });

    it('uses soft deletes', function () {
        $product = Product::factory()->create();
        $uuid = $product->uuid;

        $product->delete();

        expect(Product::find($uuid))->toBeNull();
        expect(Product::withTrashed()->find($uuid))->toBeInstanceOf(Product::class);
    });

    describe('relationships', function () {
        it('belongs to user', function () {
            $user = User::factory()->create();
            $product = Product::factory()->for($user)->create();

            expect($product->user)->toBeInstanceOf(User::class);
            expect($product->user->uuid)->toBe($user->uuid);
        });

        it('belongs to category', function () {
            $category = Category::factory()->create();
            $product = Product::factory()->for($category)->create();

            expect($product->category)->toBeInstanceOf(Category::class);
            expect($product->category->uuid)->toBe($category->uuid);
        });
    });

    describe('scopes', function () {
        it('filters active products', function () {
            Product::factory()->create(['status' => 'active']);
            Product::factory()->create(['status' => 'inactive']);

            $activeProducts = Product::active()->get();

            expect($activeProducts)->toHaveCount(1);
            expect($activeProducts->first()->status)->toBe('active');
        });

        it('searches by name', function () {
            Product::factory()->create(['name' => 'Laravel Book']);
            Product::factory()->create(['name' => 'Vue Guide']);

            $results = Product::search('Laravel')->get();

            expect($results)->toHaveCount(1);
            expect($results->first()->name)->toBe('Laravel Book');
        });
    });

    describe('accessors and mutators', function () {
        it('formats price as money', function () {
            $product = Product::factory()->make(['price' => 1234.56]);

            expect($product->formatted_price)->toBe('$1,234.56');
        });

        it('generates slug from name', function () {
            $product = Product::factory()->make(['name' => 'Test Product Name']);

            expect($product->slug)->toBe('test-product-name');
        });
    });

    describe('casting', function () {
        it('casts price to decimal', function () {
            $product = Product::factory()->create(['price' => '99.99']);

            expect($product->price)->toBeFloat();
            expect($product->price)->toBe(99.99);
        });

        it('casts meta to array', function () {
            $meta = ['color' => 'red', 'size' => 'large'];
            $product = Product::factory()->create(['meta' => $meta]);

            expect($product->meta)->toBeArray();
            expect($product->meta)->toBe($meta);
        });
    });
});
```

### Helper Testing
```php
<?php

describe('Helper Functions', function () {
    describe('money_format', function () {
        it('formats positive amounts correctly', function () {
            expect(money_format(1234.56))->toBe('1,234.56');
            expect(money_format(0))->toBe('0.00');
            expect(money_format(999999.99))->toBe('999,999.99');
        });

        it('handles edge cases', function () {
            expect(money_format(null))->toBe('0.00');
            expect(money_format(''))->toBe('0.00');
            expect(money_format(-100))->toBe('-100.00');
        });
    });

    describe('user helper', function () {
        it('returns authenticated user', function () {
            $user = User::factory()->create();

            actingAs($user);

            expect(user())->toBeInstanceOf(User::class);
            expect(user()->uuid)->toBe($user->uuid);
        });

        it('returns null when not authenticated', function () {
            expect(user())->toBeNull();
        });
    });

    describe('flash_variant', function () {
        it('returns correct CSS classes for variants', function () {
            $success = flash_variant('success');

            expect($success)->toBeArray();
            expect($success)->toHaveKey('border');
            expect($success)->toHaveKey('bg');
            expect($success)->toHaveKey('text');
            expect($success['border'])->toBe('border-green-500');
        });
    });
});
```

## Livewire Testing

### Component Testing
```php
<?php

use App\Livewire\ProductForm;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

describe('ProductForm Component', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->user->givePermissionTo('products.create.item');
    });

    it('renders successfully', function () {
        actingAs($this->user);

        Livewire::test(ProductForm::class)
            ->assertStatus(200);
    });

    it('can create product', function () {
        actingAs($this->user);

        Livewire::test(ProductForm::class)
            ->set('form.name', 'Test Product')
            ->set('form.price', 99.99)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('displayAlert');

        expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
    });

    it('validates required fields', function () {
        actingAs($this->user);

        Livewire::test(ProductForm::class)
            ->set('form.name', '')
            ->call('save')
            ->assertHasErrors(['form.name']);
    });

    it('can edit existing product', function () {
        $product = Product::factory()->create();
        actingAs($this->user);

        Livewire::test(ProductForm::class, ['product' => $product])
            ->assertSet('form.name', $product->name)
            ->set('form.name', 'Updated Name')
            ->call('save')
            ->assertHasNoErrors();

        expect($product->fresh()->name)->toBe('Updated Name');
    });

    it('requires authorization', function () {
        $user = User::factory()->create(); // No permissions

        actingAs($user);

        Livewire::test(ProductForm::class)
            ->set('form.name', 'Test Product')
            ->call('save')
            ->assertForbidden();
    });
});
```

## Authorization Testing

### Policy Testing
```php
<?php

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;

describe('ProductPolicy', function () {
    beforeEach(function () {
        $this->policy = new ProductPolicy();
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('administrator');
        $this->superadmin = User::factory()->create();
        $this->superadmin->assignRole('superadmin');
    });

    describe('viewAny', function () {
        it('allows users with correct permission', function () {
            $this->user->givePermissionTo('products.view.list');

            expect($this->policy->viewAny($this->user))->toBeTrue();
        });

        it('denies users without permission', function () {
            expect($this->policy->viewAny($this->user))->toBeFalse();
        });

        it('always allows superadmin', function () {
            expect($this->policy->viewAny($this->superadmin))->toBeTrue();
        });
    });

    describe('view', function () {
        it('allows viewing specific product with permission', function () {
            $product = Product::factory()->create();
            $this->user->givePermissionTo('products.view.item');

            expect($this->policy->view($this->user, $product))->toBeTrue();
        });

        it('denies viewing without permission', function () {
            $product = Product::factory()->create();

            expect($this->policy->view($this->user, $product))->toBeFalse();
        });
    });

    describe('create', function () {
        it('allows creation with permission', function () {
            $this->user->givePermissionTo('products.create.item');

            expect($this->policy->create($this->user))->toBeTrue();
        });
    });

    describe('update', function () {
        it('allows owner to update their product', function () {
            $product = Product::factory()->for($this->user)->create();
            $this->user->givePermissionTo('products.update.item');

            expect($this->policy->update($this->user, $product))->toBeTrue();
        });

        it('denies updating others products', function () {
            $otherUser = User::factory()->create();
            $product = Product::factory()->for($otherUser)->create();

            expect($this->policy->update($this->user, $product))->toBeFalse();
        });
    });
});
```

## Validation Testing

### Form Request Testing
```php
<?php

use App\Http\Requests\StoreProductRequest;
use App\Models\User;

describe('StoreProductRequest', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->request = new StoreProductRequest();
        $this->request->setUserResolver(fn() => $this->user);
    });

    describe('authorization', function () {
        it('allows authorized users', function () {
            $this->user->givePermissionTo('products.create.item');

            expect($this->request->authorize())->toBeTrue();
        });

        it('denies unauthorized users', function () {
            expect($this->request->authorize())->toBeFalse();
        });
    });

    describe('validation rules', function () {
        it('requires name field', function () {
            $rules = $this->request->rules();

            expect($rules['name'])->toContain('required');
        });

        it('validates price as numeric', function () {
            $rules = $this->request->rules();

            expect($rules['price'])->toContain('numeric');
            expect($rules['price'])->toContain('min:0');
        });

        it('validates email uniqueness', function () {
            $rules = $this->request->rules();

            expect($rules['email'])->toContain('unique:users');
        });
    });

    describe('validation scenarios', function () {
        it('passes with valid data', function (array $data) {
            $validator = validator($data, $this->request->rules());

            expect($validator->passes())->toBeTrue();
        })->with([
            [['name' => 'Test Product', 'price' => 99.99, 'email' => 'test@example.com']],
            [['name' => 'Another Product', 'price' => 0, 'email' => 'unique@example.com']],
        ]);

        it('fails with invalid data', function (array $data, array $expectedErrors) {
            $validator = validator($data, $this->request->rules());

            expect($validator->fails())->toBeTrue();

            foreach ($expectedErrors as $field) {
                expect($validator->errors()->has($field))->toBeTrue();
            }
        })->with([
            [[], ['name', 'price', 'email']],
            [['name' => '', 'price' => -10], ['name', 'price', 'email']],
            [['name' => 'Test', 'price' => 'invalid'], ['price', 'email']],
        ]);
    });
});
```

## Database Testing

### Factory Testing
```php
<?php

use App\Models\Product;
use App\Models\Category;

describe('Product Factory', function () {
    it('creates valid product', function () {
        $product = Product::factory()->create();

        expect($product)->toBeInstanceOf(Product::class);
        expect($product->uuid)->toBeString();
        expect($product->name)->toBeString();
        expect($product->price)->toBeFloat();
    });

    it('creates product with custom attributes', function () {
        $product = Product::factory()->create([
            'name' => 'Custom Product',
            'price' => 199.99,
        ]);

        expect($product->name)->toBe('Custom Product');
        expect($product->price)->toBe(199.99);
    });

    it('creates product with relationships', function () {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        expect($product->category_id)->toBe($category->uuid);
        expect($product->category)->toBeInstanceOf(Category::class);
    });

    it('creates multiple products', function () {
        $products = Product::factory()->count(5)->create();

        expect($products)->toHaveCount(5);
        expect(Product::count())->toBe(5);
    });
});
```

## Performance Testing

### Query Performance
```php
<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

describe('Product Performance', function () {
    it('loads products without N+1 queries', function () {
        Product::factory()->count(10)->create();

        DB::enableQueryLog();

        $products = Product::with('category')->get();

        $queries = DB::getQueryLog();

        expect($products)->toHaveCount(10);
        expect(count($queries))->toBeLessThanOrEqual(2); // One for products, one for categories
    });

    it('handles bulk operations efficiently', function () {
        $start = microtime(true);

        Product::factory()->count(1000)->create();

        $duration = microtime(true) - $start;

        expect($duration)->toBeLessThan(5.0); // Should complete in under 5 seconds
    });

    it('paginates large datasets', function () {
        Product::factory()->count(100)->create();

        $products = Product::paginate(15);

        expect($products->items())->toHaveCount(15);
        expect($products->total())->toBe(100);
        expect($products->hasPages())->toBeTrue();
    });
});
```

## Test Organization

### Test Structure
```php
<?php

describe('Feature Name', function () {
    beforeEach(function () {
        // Setup for all tests
    });

    beforeAll(function () {
        // One-time setup
    });

    describe('specific functionality', function () {
        it('does something specific', function () {
            // Test implementation
        });

        it('handles edge case', function () {
            // Edge case test
        });
    });

    describe('another functionality', function () {
        // More tests
    });
});
```

### Datasets for Parameterized Tests
```php
it('validates different email formats', function (string $email, bool $valid) {
    $validator = validator(['email' => $email], ['email' => 'email']);

    expect($validator->passes())->toBe($valid);
})->with([
    ['test@example.com', true],
    ['invalid.email', false],
    ['user@domain.co.uk', true],
    ['@example.com', false],
    ['test@', false],
]);
```

## Test Quality Checklist

- [ ] Tests use Pest syntax (it/expect)
- [ ] Tests are descriptive and readable
- [ ] Tests cover happy path and edge cases
- [ ] Authorization is tested separately
- [ ] Database changes are verified
- [ ] Performance considerations included
- [ ] Tests use factories for data
- [ ] Tests are isolated and independent
- [ ] Mock external dependencies
- [ ] Use appropriate test type (feature/unit)

Generate comprehensive test suites that ensure code quality and reliability while following Laravel and Pest PHP best practices.
