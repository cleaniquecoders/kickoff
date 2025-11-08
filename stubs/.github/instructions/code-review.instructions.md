---
applyTo: "**"
description: "Code review standards and guidelines for Laravel applications"
---

# Code Review Guidelines for Laravel Applications

Comprehensive code review standards for maintaining code quality, security, and performance.

## Code Review Principles

- **Be constructive and respectful**: Focus on code, not the person
- **Explain the "why"**: Provide reasoning for suggestions
- **Suggest improvements**: Don't just point out problems
- **Learn from each other**: Code reviews are learning opportunities
- **Maintain consistency**: Follow established patterns and conventions

## Review Checklist

### Architecture and Design
- [ ] Code follows Laravel conventions and "The Laravel Way"
- [ ] Single Responsibility Principle applied to classes and methods
- [ ] Proper separation of concerns (Controller → Service → Repository)
- [ ] Business logic placed in appropriate layers (not in controllers)
- [ ] Consistent naming conventions used throughout
- [ ] No code duplication (DRY principle followed)

### Security Review
- [ ] All user inputs properly validated using Laravel's validation
- [ ] Authorization checks implemented using policies/gates
- [ ] CSRF protection enabled for state-changing operations
- [ ] No hardcoded secrets or sensitive information
- [ ] File uploads properly validated and stored securely
- [ ] SQL injection prevention (Eloquent ORM used correctly)
- [ ] XSS prevention (proper output escaping in Blade templates)
- [ ] Authentication and session management secure

### Performance Review
- [ ] Database queries optimized (no N+1 queries)
- [ ] Appropriate use of eager loading
- [ ] Expensive operations cached where beneficial
- [ ] Large datasets paginated or chunked
- [ ] Background jobs used for heavy processing
- [ ] Memory usage efficient for large operations
- [ ] Appropriate database indexes suggested

### Testing Review
- [ ] Tests cover new functionality
- [ ] Tests follow Pest PHP conventions (it/expect syntax)
- [ ] Feature tests cover user workflows
- [ ] Unit tests cover business logic
- [ ] Tests are readable and maintainable
- [ ] Test data uses factories, not hardcoded values
- [ ] Authorization properly tested

### Code Quality
- [ ] Code is readable and self-documenting
- [ ] Methods are reasonably sized and focused
- [ ] Complex logic is commented appropriately
- [ ] Error handling implemented properly
- [ ] No dead or commented-out code
- [ ] Consistent code formatting (Laravel Pint)

## Review Categories

### ✅ Approve
Use when:
- Code meets all standards
- Minor style issues that can be addressed later
- Educational suggestions that don't block merge

### 🔄 Request Changes
Use when:
- Security vulnerabilities present
- Performance issues identified
- Tests missing or inadequate
- Architecture concerns need addressing

### 💬 Comment
Use for:
- Questions about approach
- Suggestions for improvement
- Learning opportunities
- Best practice discussions

## Laravel-Specific Review Points

### Models
```php
// ✅ GOOD: Proper model structure
class Product extends Base // Extends App\Models\Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'price', 'description'];

    protected $casts = [
        'price' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

// ❌ BAD: Extending wrong base class
class Product extends Model // Should extend App\Models\Base
{
    // Missing proper configuration
}
```

### Controllers
```php
// ✅ GOOD: Proper controller structure
class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->paginate(15);

        return view('products.index', compact('products'));
    }

    public function store(StoreProductRequest $request)
    {
        $product = auth()->user()->products()->create($request->validated());

        return redirect()->route('products.show', $product)
            ->with('success', 'Product created successfully!');
    }
}

// ❌ BAD: Fat controller with business logic
class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Validation in controller
        $request->validate([...]);

        // Business logic in controller
        if ($request->special_category) {
            // Complex business logic here
        }

        // Multiple responsibilities
    }
}
```

### Validation
```php
// ✅ GOOD: Form request validation
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Product::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'The selected category is invalid.',
        ];
    }
}

// ❌ BAD: Validation in controller
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
    ]);
}
```

### Database Queries
```php
// ✅ GOOD: Optimized queries
$products = Product::with(['category', 'reviews' => function ($query) {
        $query->latest()->limit(5);
    }])
    ->select(['id', 'name', 'price', 'category_id'])
    ->where('status', 'active')
    ->paginate(15);

// ❌ BAD: N+1 query problem
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name; // Separate query for each product
}
```

### Livewire Components
```php
// ✅ GOOD: Proper Livewire component
class ProductForm extends Component
{
    use InteractsWithLivewireAlert;

    public ProductForm $form;

    public function mount(Product $product = null)
    {
        if ($product->exists) {
            $this->form->setProduct($product);
        }
    }

    public function save()
    {
        $this->authorize('create', Product::class);

        $product = $this->form->save();

        $this->alert('Success', 'Product saved successfully!');

        return redirect()->route('products.show', $product);
    }
}

// ❌ BAD: Business logic in component
class ProductForm extends Component
{
    public function save()
    {
        // Complex validation logic
        // Database operations
        // Email sending
        // File processing
        // All in one method
    }
}
```

## Testing Review

### Feature Tests
```php
// ✅ GOOD: Comprehensive feature test
it('allows authorized user to create product', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('products.create.item');

    $response = actingAs($user)->post('/products', [
        'name' => 'Test Product',
        'price' => 99.99,
        'category_id' => Category::factory()->create()->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
});

// ❌ BAD: Incomplete test
it('creates product', function () {
    post('/products', ['name' => 'Test']);
    // No assertions about authorization, validation, or success
});
```

## Common Issues to Flag

### Security Issues
- Missing authorization checks
- Unvalidated user input
- Hardcoded secrets
- Disabled CSRF protection
- Unsafe file uploads

### Performance Issues
- N+1 queries
- Missing pagination
- Inefficient database queries
- Missing caching for expensive operations
- Large memory allocations

### Architecture Issues
- Business logic in controllers
- Fat models with too many responsibilities
- Circular dependencies
- Tight coupling between components

### Laravel Convention Violations
- Not using Eloquent relationships
- Manual SQL queries instead of Eloquent
- Not following naming conventions
- Ignoring Laravel's built-in features

## Review Response Guidelines

### For Authors
- **Respond to all feedback**: Even if just to acknowledge
- **Ask for clarification**: If feedback isn't clear
- **Explain your reasoning**: When defending design decisions
- **Be open to suggestions**: Code reviews improve code quality
- **Make requested changes**: Or discuss alternatives

### For Reviewers
- **Focus on important issues**: Don't nitpick style if tools handle it
- **Provide examples**: Show better alternatives when suggesting changes
- **Consider the context**: Understand the bigger picture
- **Be specific**: Point to exact lines and explain issues clearly
- **Acknowledge good code**: Positive feedback is valuable too

## Review Comments Examples

### Good Comments
```
🔒 Security: This endpoint needs authorization. Consider adding `$this->authorize('view', $product)` before the query.

⚡ Performance: This will cause N+1 queries. Try using eager loading: `Product::with('category')->get()`

🧪 Testing: Consider adding a test case for the unauthorized access scenario.

👍 Nice use of Laravel's validation rules! The custom message is very user-friendly.
```

### Avoid These Comments
```
❌ "This is wrong" - Not helpful without explanation
❌ "Use better variable names" - Too vague
❌ "I don't like this approach" - No reasoning provided
❌ Style nitpicks handled by automated tools
```

## Tools for Code Review

- **GitHub/GitLab Reviews**: Use inline comments
- **Laravel Pint**: Automated code formatting
- **PHPStan/Larastan**: Static analysis
- **PHP CS Fixer**: Code style fixing
- **Pest**: Testing framework
- **Laravel Telescope**: Debugging assistance

## Post-Review Actions

### Before Merge
- [ ] All review comments addressed
- [ ] CI/CD pipeline passes
- [ ] Tests pass locally and in CI
- [ ] Code formatted with Laravel Pint
- [ ] No merge conflicts

### After Merge
- [ ] Monitor application performance
- [ ] Watch for any issues in production
- [ ] Update documentation if needed
- [ ] Consider refactoring opportunities

Remember: Code reviews are about building better software together. Focus on collaboration, learning, and maintaining high standards while being respectful and constructive.
