# Stubs System

The stubs directory contains a complete Laravel project template that gets copied
to target projects during bootstrap.

## Purpose

Stubs provide a standardized project structure with:

- Pre-configured packages and settings
- Custom Artisan command stubs
- Helper functions organized by domain
- Deployment and utility scripts
- Documentation templates
- GitHub workflows for CI/CD

## Directory Structure

```text
stubs/
├── .github/
│   ├── workflows/         # CI/CD workflows
│   ├── instructions/      # Copilot instructions
│   ├── prompts/          # AI prompts
│   └── chatmodes/        # Chat mode configs
├── app/
│   ├── Console/
│   ├── Http/
│   ├── Models/
│   └── Livewire/
├── bin/                   # Utility scripts
├── config/                # Laravel configs
├── database/
├── docs/                  # Project documentation
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   ├── web/
│   ├── api/
│   └── console/
├── stubs/                 # Custom Artisan stubs
├── support/               # Helper functions
├── tests/
└── [config files]         # rector.php, pint.json, etc.
```

## Placeholder Replacement

### Placeholders

Two placeholders are used throughout stubs:

- `${PROJECT_NAME}`: Replaced with project name argument
- `${OWNER}`: Replaced with owner argument

### Files with Placeholders

**README.md**:

```markdown
# ${PROJECT_NAME}

Developed by ${OWNER}
```

**.env.example**:

```env
APP_NAME="${PROJECT_NAME}"
DB_DATABASE=project_name
```

**bin/ scripts**:

```bash
PROJECT_NAME="${PROJECT_NAME}"
```

### Replacement Process

Implemented in `StartCommand::updatePlaceholder()`:

```php
private function updatePlaceholder($placeholder, $file)
{
    if (is_file($file)) {
        $content = file_get_contents($file);
        $newContent = str_replace(
            $placeholder,
            $placeholder === self::PLACEHOLDER_PROJECT_NAME
                ? $this->getProjectName()
                : $this->getProjectOwner(),
            $content
        );
        file_put_contents($file, $newContent);
    }
}
```

## Key Stub Components

### Configuration Files

**rector.php**:

- PHP 8.3 target
- Laravel 11 rule set
- Code refactoring rules

**pint.json**:

- Laravel Pint configuration
- Relaxed PHPDoc rules

**phpunit.xml**:

- Test environment settings
- SQLite in-memory database

**tailwind.config.js**:

- TailwindCSS v4 configuration
- Custom content paths

**docker-compose.yml**:

- MinIO (S3-compatible storage)
- Elasticsearch
- Redis

### Utility Scripts (bin/)

**install**:

```bash
# Creates database, updates .env, runs migrations
mysql -e "CREATE DATABASE IF NOT EXISTS ${PROJECT_NAME}"
php artisan migrate --seed
```

**deploy**:

```bash
# Git-based deployment with branch/tag support
git pull origin main
composer install --no-dev
php artisan migrate --force
```

**backup-app**, **backup-media**:

```bash
# Application and media file backups
tar -czf backup-$(date +%Y%m%d).tar.gz app/ config/ database/
```

**build-fe-assets**, **reinstall-npm**, **update-dependencies**:

```bash
# Frontend build and dependency management
npm run build
composer update
```

### Custom Artisan Stubs (stubs/)

**model.stub**:

```php
// Extends App\Models\Base instead of Eloquent Model
class {{ class }} extends Base
{
    // UUID primary keys by default
    // Auditing enabled
    // Media library support
}
```

**migration.create.stub**:

```php
// UUID primary keys
$table->uuid('id')->primary();
```

**pest.stub**:

```php
// Pest test syntax
test('example', function () {
    expect(true)->toBeTrue();
});
```

**policy.stub**:

```php
// Standard CRUD policy methods
```

### Helper Functions (support/)

Organized by domain:

- `user.php`: User-related helpers
- `flash.php`: Flash message helpers
- `media.php`: Media handling helpers
- `menu.php`: Menu generation helpers
- `helpers.php`: General utilities and loader

**Loading mechanism** (`support/helpers.php`):

```php
require_all_in(__DIR__.'/*.php');

function require_all_in($pattern) {
    foreach (glob($pattern) as $file) {
        if ($file !== __FILE__) {
            require_once $file;
        }
    }
}
```

All helpers wrapped in `function_exists()` checks.

### Documentation Templates

**CHANGELOG.md**, **CONTRIBUTING.md**, **CODE_OF_CONDUCT.md**:

- Standard open-source documentation
- Placeholders for customization

**docs/**:

- `deployment/`: Deployment guides
- `development/`: Development documentation
- `standards/`: Code quality standards

### GitHub Copilot Instructions

**`.github/copilot-instructions.md`**:

Comprehensive guide for AI assistants working with generated projects:

- Laravel conventions
- Model architecture (Base model with UUIDs, auditing)
- Livewire component patterns
- Route organization
- Testing strategies
- Frontend stack (TailwindCSS, Alpine.js)

This file is copied to all generated projects.

## Stubs vs Package Helpers

**Critical distinction**:

### Package Helpers (`support/helpers.php`)

CLI utilities for the kickoff package:

- `step()`: Progress indicators
- `runCommand()`: Shell execution
- `copyRecursively()`: File operations

### Stubs Helpers (`stubs/support/helpers.php`)

Laravel application helpers for generated projects:

- `current_user()`: Get authenticated user
- `flash()`: Flash messages
- `menu()`: Menu generation
- `upload_file()`: File uploads

Do not confuse these - they serve different codebases.

## Modifying Stubs

### Adding New Files

1. Create file in appropriate `stubs/` subdirectory
2. Use placeholders where needed: `${PROJECT_NAME}`, `${OWNER}`
3. No code changes required - `copyRecursively()` handles it

### Updating Existing Files

1. Edit file in `stubs/` directory
2. Test with `bin/sandbox run`
3. Verify changes in `test-output/sandbox/`

### Adding New Placeholders

1. Define constant in `StartCommand`:

   ```php
   const PLACEHOLDER_NEW = '${NEW_PLACEHOLDER}';
   ```

2. Add replacement logic in `setupProjectName()`:

   ```php
   step('Update new files', function () {
       $this->updatePlaceholder(self::PLACEHOLDER_NEW, $file);
   }, $output, $verbose);
   ```

## Next Steps

- [Helper Functions](04-helper-functions.md) - Learn about CLI utility functions
- [Bootstrap Process](02-bootstrap-process.md) - How stubs are copied and processed
- [Adding Features](../02-development/04-adding-features.md) - How to add new stubs
