<!-- Based on: https://github.com/github/awesome-copilot/blob/main/instructions/performance-optimization.instructions.md -->
---
applyTo: "**/*.php,**/*.js,**/*.blade.php"
description: "Performance optimization guidelines for Laravel applications"
---

# Performance Optimization Guidelines for Laravel Applications

Follow performance-first principles to build fast, scalable Laravel applications.

## General Performance Principles

- **Measure first, optimize second**: Use profiling tools before making changes
- **Optimize for the common case**: Focus on frequently executed code paths
- **Minimize resource usage**: Efficient use of memory, CPU, and I/O
- **Cache strategically**: Cache expensive operations and frequently accessed data
- **Lazy load when possible**: Load data only when needed

## Database Performance

### Query Optimization
- **Avoid N+1 queries**: Use eager loading with `with()` and `load()`
- **Select only needed columns**: Avoid `SELECT *`
- **Use database indexes**: Index frequently queried columns
- **Limit result sets**: Always paginate large datasets

```php
// ✅ CORRECT: Eager loading prevents N+1 queries
$posts = Post::with(['user', 'comments.user'])->get();

// ✅ CORRECT: Select specific columns
$users = User::select(['id', 'name', 'email'])->get();

// ✅ CORRECT: Pagination for large datasets
$products = Product::paginate(15);

// ❌ WRONG: N+1 query problem
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->user->name; // Separate query for each post
}
```

### Eloquent Optimization
```php
// ✅ CORRECT: Chunking for large datasets
Product::chunk(100, function ($products) {
    foreach ($products as $product) {
        // Process product
    }
});

// ✅ CORRECT: Use exists() instead of count()
if (Product::where('category', 'electronics')->exists()) {
    // More efficient than count() > 0
}

// ✅ CORRECT: Bulk operations
Product::whereIn('id', $ids)->update(['status' => 'active']);
```

### Database Indexes
```php
// Migration: Add indexes for frequently queried columns
Schema::table('products', function (Blueprint $table) {
    $table->index(['category', 'status']);
    $table->index('user_id');
    $table->index('created_at');
});
```

## Caching Strategies

### Application Caching
```php
// ✅ CORRECT: Cache expensive operations
$products = Cache::remember('products.featured', 3600, function () {
    return Product::where('featured', true)
        ->with('category')
        ->get();
});

// ✅ CORRECT: Cache database queries
$userCount = Cache::remember('users.count', 3600, function () {
    return User::count();
});

// ✅ CORRECT: Tag-based cache invalidation
Cache::tags(['products'])->put('products.all', $products, 3600);
Cache::tags(['products'])->flush(); // Clear all product caches
```

### Model Caching
```php
// Model with automatic caching
class Product extends Model
{
    protected static function booted()
    {
        static::updated(function ($product) {
            Cache::forget("product.{$product->id}");
        });
    }

    public static function findCached($id)
    {
        return Cache::remember("product.{$id}", 3600, function () use ($id) {
            return static::find($id);
        });
    }
}
```

### Configuration Caching
```bash
# Production optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Frontend Performance

### Asset Optimization
```php
// ✅ CORRECT: Asset versioning and minification
// vite.config.js
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs', 'axios'],
                },
            },
        },
    },
});
```

### Blade Template Optimization
```blade
{{-- ✅ CORRECT: Lazy loading images --}}
<img src="{{ $product->image_url }}" loading="lazy" alt="{{ $product->name }}">

{{-- ✅ CORRECT: Conditional loading --}}
@if($user->avatar)
    <img src="{{ $user->avatar_url }}" alt="Avatar">
@else
    <div class="avatar-placeholder"></div>
@endif

{{-- ✅ CORRECT: Component caching --}}
@cache('product-card', $product->id, $product->updated_at)
    <x-product-card :product="$product" />
@endcache
```

### JavaScript Performance
```js
// ✅ CORRECT: Debounce user input
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ✅ CORRECT: Lazy load heavy components
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Load component
                loadComponent(entry.target);
                observer.unobserve(entry.target);
            }
        });
    });

    document.querySelectorAll('.lazy-component').forEach(el => {
        observer.observe(el);
    });
});
```

## Livewire Performance

### Component Optimization
```php
class ProductList extends Component
{
    // ✅ CORRECT: Use computed properties for expensive operations
    public function getProductsProperty()
    {
        return Product::with('category')
            ->where('status', 'active')
            ->paginate(10);
    }

    // ✅ CORRECT: Debounce search input
    #[Debounce(500)]
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // ✅ CORRECT: Lazy loading
    public function placeholder()
    {
        return view('components.skeleton');
    }
}
```

### Livewire Best Practices
```php
// ✅ CORRECT: Use wire:key for dynamic lists
@foreach($products as $product)
    <div wire:key="product-{{ $product->id }}">
        <x-product-card :product="$product" />
    </div>
@endforeach

// ✅ CORRECT: Optimize re-rendering
<div wire:ignore>
    {{-- Content that shouldn't re-render --}}
    <div id="chart"></div>
</div>
```

## Queue Performance

### Background Jobs
```php
// ✅ CORRECT: Use queues for heavy operations
class ProcessLargeFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    public function handle()
    {
        // Heavy processing logic
        $this->processFile();
    }

    public function failed($exception)
    {
        // Handle failed job
        Log::error('File processing failed', ['exception' => $exception]);
    }
}

// Dispatch job
ProcessLargeFile::dispatch($file);
```

### Queue Optimization
```php
// Queue configuration for performance
// config/queue.php
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => env('REDIS_QUEUE', 'default'),
    'retry_after' => 90,
    'block_for' => 0,
    'after_commit' => false,
],
```

## Memory Management

### Efficient Data Handling
```php
// ✅ CORRECT: Stream large datasets
public function exportProducts()
{
    return response()->streamDownload(function () {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['ID', 'Name', 'Price']);

        Product::chunk(1000, function ($products) use ($handle) {
            foreach ($products as $product) {
                fputcsv($handle, [$product->id, $product->name, $product->price]);
            }
        });

        fclose($handle);
    }, 'products.csv');
}

// ✅ CORRECT: Clear large collections from memory
$products = collect($largeDataset)
    ->map(function ($item) {
        return $this->transform($item);
    })
    ->filter()
    ->values();

unset($largeDataset); // Free memory
```

## API Performance

### Efficient API Responses
```php
class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            // ✅ CORRECT: Conditional inclusion
            'description' => $this->when($request->include_description, $this->description),
            'category' => $this->whenLoaded('category'),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}

// ✅ CORRECT: Pagination for API responses
public function index(Request $request)
{
    $products = Product::with('category')
        ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
        ->paginate($request->per_page ?? 15);

    return ProductResource::collection($products);
}
```

### API Rate Limiting
```php
// ✅ CORRECT: Implement rate limiting
Route::middleware(['throttle:api'])->group(function () {
    Route::apiResource('products', ProductController::class);
});

// Custom rate limiting
RateLimiter::for('api', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(100)->by($request->user()->id)
        : Limit::perMinute(10)->by($request->ip());
});
```

## Monitoring and Profiling

### Performance Monitoring
```php
// ✅ CORRECT: Monitor slow queries
// AppServiceProvider
public function boot()
{
    DB::whenQueryingForLongerThan(500, function (Connection $connection, QueryExecuted $event) {
        Log::warning('Slow query detected', [
            'sql' => $event->sql,
            'bindings' => $event->bindings,
            'time' => $event->time,
        ]);
    });
}

// ✅ CORRECT: Profile critical paths
public function expensiveOperation()
{
    $start = microtime(true);

    // Expensive operation
    $result = $this->performOperation();

    $duration = microtime(true) - $start;

    if ($duration > 1.0) {
        Log::warning('Slow operation', [
            'operation' => 'expensiveOperation',
            'duration' => $duration,
        ]);
    }

    return $result;
}
```

### Laravel Telescope Integration
```php
// Monitor performance in development
if (app()->environment('local')) {
    app(\Laravel\Telescope\Telescope::class);
}
```

## Production Optimization

### Server Configuration
```nginx
# Nginx optimization for Laravel
location ~ \.php$ {
    fastcgi_cache_valid 200 1h;
    fastcgi_cache_use_stale error timeout updating http_500;
    fastcgi_cache_background_update on;
    fastcgi_cache_lock on;
}

# Static asset caching
location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

### PHP Configuration
```ini
# PHP optimization
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
```

## Performance Testing

### Benchmark Testing
```php
it('performs bulk operations efficiently', function () {
    $start = microtime(true);

    Product::factory()->count(1000)->create();

    $duration = microtime(true) - $start;

    expect($duration)->toBeLessThan(5.0); // Should complete in under 5 seconds
});

it('handles large datasets without memory issues', function () {
    $startMemory = memory_get_usage();

    Product::chunk(100, function ($products) {
        foreach ($products as $product) {
            // Process product
        }
    });

    $endMemory = memory_get_usage();
    $memoryUsed = $endMemory - $startMemory;

    expect($memoryUsed)->toBeLessThan(50 * 1024 * 1024); // Less than 50MB
});
```

## Performance Checklist

- [ ] Database queries optimized with proper indexes
- [ ] N+1 queries eliminated using eager loading
- [ ] Large datasets paginated or chunked
- [ ] Expensive operations cached appropriately
- [ ] Background jobs used for heavy processing
- [ ] Assets optimized and cached with proper headers
- [ ] Configuration cached in production
- [ ] Memory usage monitored for large operations
- [ ] API responses paginated and efficient
- [ ] Slow queries logged and monitored
- [ ] Performance tests included in test suite
- [ ] Production server properly configured

## Tools and Resources

- **Laravel Debugbar**: Development performance profiling
- **Laravel Telescope**: Application debugging and monitoring
- **Laravel Horizon**: Queue monitoring and metrics
- **Blackfire**: Production performance profiling
- **New Relic**: Application performance monitoring
- **Redis**: High-performance caching and session storage

Remember: Performance optimization is an ongoing process. Always measure before and after optimizations to verify improvements.
