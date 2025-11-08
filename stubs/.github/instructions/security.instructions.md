<!-- Based on: https://github.com/github/awesome-copilot/blob/main/instructions/security-and-owasp.instructions.md -->
---
applyTo: "**/*.php,**/*.js,**/*.blade.php"
description: "Security best practices and OWASP guidelines for Laravel applications"
---

# Security Best Practices for Laravel Applications

Follow security-first principles based on OWASP Top 10 and Laravel security best practices.

## Authentication & Authorization

### Access Control (A01: Broken Access Control)
- **Use Laravel's built-in authorization**: Always use policies and gates for access control
- **Principle of least privilege**: Grant minimal permissions required for functionality
- **Deny by default**: Explicitly check permissions before allowing access

```php
// ✅ CORRECT: Use policies
$this->authorize('update', $product);

// ✅ CORRECT: Check permissions
if ($user->can('products.create.item')) {
    // Allow action
}

// ❌ WRONG: Role-based checks without proper policies
if ($user->role === 'admin') {
    // Too broad, no specific permission check
}
```

### Session Security
- **Regenerate session ID** after login to prevent session fixation
- **Use secure session cookies**: `HttpOnly`, `Secure`, `SameSite=Strict`
- **Implement session timeout** for sensitive operations

```php
// ✅ CORRECT: Regenerate session after login
auth()->login($user);
request()->session()->regenerate();
```

## Data Protection

### Cryptographic Security (A02: Cryptographic Failures)
- **Use strong hashing**: Always use bcrypt or Argon2 for passwords
- **Never hardcode secrets**: Use environment variables or secure storage
- **Encrypt sensitive data at rest**: Use Laravel's encryption for PII
- **Always use HTTPS**: Force HTTPS in production

```php
// ✅ CORRECT: Environment variables for secrets
$apiKey = env('THIRD_PARTY_API_KEY');

// ✅ CORRECT: Encrypt sensitive data
$encryptedData = encrypt($sensitiveData);

// ❌ WRONG: Hardcoded secrets
$apiKey = 'sk_this_is_a_bad_idea_12345';
```

### Data Validation
- **Validate all input**: Never trust user input
- **Use Laravel's validation**: Leverage form requests and validation rules
- **Sanitize output**: Use proper escaping for different contexts

```php
// ✅ CORRECT: Comprehensive validation
class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'email' => ['required', 'email', 'unique:users'],
        ];
    }
}
```

## Injection Prevention (A03: Injection)

### SQL Injection Prevention
- **Always use Eloquent ORM**: Eloquent automatically prevents SQL injection
- **Use parameterized queries**: When raw SQL is necessary
- **Never concatenate user input** into SQL queries

```php
// ✅ CORRECT: Eloquent queries (automatically safe)
$products = Product::where('name', $request->search)->get();

// ✅ CORRECT: Parameterized raw queries
DB::select('SELECT * FROM products WHERE name = ?', [$name]);

// ❌ WRONG: String concatenation
DB::select("SELECT * FROM products WHERE name = '$name'");
```

### XSS Prevention
- **Use Blade templating**: Automatic escaping by default
- **Be careful with {!! !!}**: Only use for trusted content
- **Validate and sanitize HTML**: Use libraries like HTMLPurifier when needed

```php
{{-- ✅ CORRECT: Automatic escaping --}}
<h1>{{ $user->name }}</h1>

{{-- ⚠️ CAREFUL: Unescaped output --}}
<div>{!! $trustedHtmlContent !!}</div>

{{-- ❌ WRONG: Unescaped user input --}}
<div>{!! $user->bio !!}</div>
```

### Command Injection Prevention
- **Avoid shell execution**: Use PHP functions instead of shell commands
- **Validate file paths**: Prevent directory traversal attacks
- **Use Laravel's file handling**: Leverage built-in file utilities

```php
// ✅ CORRECT: Use Laravel's file storage
Storage::put($path, $content);

// ❌ WRONG: Shell execution with user input
shell_exec("cat " . $userInput);
```

## File Upload Security

### Secure File Handling
- **Validate file types**: Check MIME type and extension
- **Limit file size**: Prevent DoS through large uploads
- **Store outside web root**: Use Laravel's storage system
- **Scan for malware**: Implement virus scanning for uploads

```php
// ✅ CORRECT: Secure file upload
class FileUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048', // 2MB max
            ],
        ];
    }
}

// Store securely
$path = $request->file('file')->store('uploads', 'private');
```

## Configuration Security (A05: Security Misconfiguration)

### Environment Configuration
- **Debug mode off in production**: Never enable debug in production
- **Use environment-specific configs**: Separate dev/staging/production settings
- **Set security headers**: Use Laravel's security middleware
- **Keep dependencies updated**: Regularly update Laravel and packages

```php
// ✅ CORRECT: Production configuration
// .env.production
APP_ENV=production
APP_DEBUG=false
HTTPS=true
```

### Security Headers
```php
// ✅ CORRECT: Security middleware
class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
```

## Rate Limiting & CSRF Protection

### Rate Limiting
- **Implement rate limiting**: Protect against brute force attacks
- **Use Laravel's throttle middleware**: Built-in protection
- **Monitor for abuse**: Log and alert on suspicious activity

```php
// ✅ CORRECT: Rate limiting
Route::middleware(['auth', 'throttle:60,1'])
    ->group(function () {
        Route::resource('products', ProductController::class);
    });
```

### CSRF Protection
- **Always use CSRF tokens**: Enable for all state-changing operations
- **Verify CSRF tokens**: Never disable CSRF protection
- **Use Laravel's helpers**: Built-in CSRF protection

```blade
{{-- ✅ CORRECT: CSRF protection --}}
<form method="POST" action="/products">
    @csrf
    {{-- form fields --}}
</form>
```

## API Security

### API Authentication
- **Use Laravel Sanctum**: For API token authentication
- **Implement proper scopes**: Limit token capabilities
- **Use HTTPS only**: Never expose tokens over HTTP

```php
// ✅ CORRECT: Sanctum token creation
$token = $user->createToken('api-token', ['products:read', 'products:write']);

// ✅ CORRECT: Scope protection
Route::middleware(['auth:sanctum', 'abilities:products:write'])
    ->post('/api/products', [ProductController::class, 'store']);
```

### API Rate Limiting
```php
// ✅ CORRECT: API rate limiting
Route::middleware(['auth:sanctum', 'throttle:api'])
    ->prefix('api')
    ->group(function () {
        // API routes
    });
```

## Logging & Monitoring

### Security Logging
- **Log security events**: Authentication, authorization failures
- **Monitor suspicious activity**: Failed logins, permission violations
- **Use structured logging**: JSON format for easier parsing
- **Protect log files**: Secure log storage and access

```php
// ✅ CORRECT: Security event logging
Log::channel('security')->warning('Unauthorized access attempt', [
    'user_id' => auth()->id(),
    'resource' => 'products',
    'action' => 'delete',
    'ip' => request()->ip(),
]);
```

## Data Privacy

### Personal Data Protection
- **Implement data encryption**: For sensitive personal information
- **Use data anonymization**: For analytics and testing
- **Provide data export**: GDPR compliance features
- **Secure data deletion**: Proper data removal processes

```php
// ✅ CORRECT: Encrypted sensitive data
class User extends Model
{
    protected $casts = [
        'ssn' => 'encrypted',
        'phone' => 'encrypted',
    ];
}
```

## Error Handling

### Secure Error Messages
- **Don't expose sensitive information**: Generic error messages in production
- **Log detailed errors**: Full details in logs, not user responses
- **Use custom error pages**: Avoid framework error pages in production

```php
// ✅ CORRECT: Safe error handling
try {
    // risky operation
} catch (Exception $e) {
    Log::error('Database operation failed', [
        'error' => $e->getMessage(),
        'user' => auth()->id(),
    ]);

    return response()->json(['error' => 'Operation failed'], 500);
}
```

## Security Checklist

- [ ] All routes properly authenticated and authorized
- [ ] CSRF protection enabled for state-changing operations
- [ ] Input validation implemented for all user inputs
- [ ] File uploads properly validated and stored securely
- [ ] Secrets stored in environment variables, not code
- [ ] HTTPS enforced in production
- [ ] Security headers configured
- [ ] Rate limiting implemented for sensitive endpoints
- [ ] Error messages don't expose sensitive information
- [ ] Dependencies regularly updated
- [ ] Security logging implemented
- [ ] Database queries use Eloquent or parameterized queries
- [ ] Output properly escaped in templates
- [ ] Session security configured properly

## Security Testing

Include security tests in your test suite:

```php
it('prevents SQL injection in search', function () {
    $response = get('/products?search=' . urlencode("'; DROP TABLE products; --"));

    $response->assertStatus(200);
    expect(Product::count())->toBeGreaterThan(0); // Table should still exist
});

it('requires CSRF token for product creation', function () {
    $response = post('/products', ['name' => 'Test']);

    $response->assertStatus(419); // CSRF token mismatch
});

it('rate limits login attempts', function () {
    for ($i = 0; $i < 10; $i++) {
        post('/login', ['email' => 'test@example.com', 'password' => 'wrong']);
    }

    $response = post('/login', ['email' => 'test@example.com', 'password' => 'wrong']);
    $response->assertStatus(429); // Too many requests
});
```

Remember: Security is not a feature to add later - it must be built into every aspect of your application from the beginning.
