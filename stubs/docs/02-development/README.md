# Development

This section covers day-to-day development workflows, components, features, and best practices for building with this Laravel application.

## Overview

This project is built with:

- **Backend**: Laravel 12+, PHP 8.4+
- **Frontend**: Livewire 3, Alpine.js, TailwindCSS v4
- **Database**: MySQL with UUID primary keys
- **Real-time**: Laravel Echo, WebSockets
- **Admin**: Livewire Flux components

## Table of Contents

### Core Concepts

1. [Database](01-database.md) - Schema, migrations, models, and relationships
2. [Livewire Components](02-livewire.md) - Building interactive UI components
3. [API Development](03-api.md) - Creating and documenting APIs
4. [Artisan Commands](04-commands.md) - Custom console commands

### Features & Components

1. [Datatable](05-datatable.md) - Table components with sorting, filtering, pagination
2. [Access Control](06-access-control.md) - Roles, permissions, and authorization
3. [File Uploads](07-upload-helper.md) - File handling and storage utilities
4. [Secure File Access](08-secure-file-access.md) - Protected file downloads
5. [Sidebar Navigation](09-sidebar.md) - Dynamic menu system
6. [Toast Notifications](10-toast-notifications.md) - Flash messages and alerts

## Development Workflow

### Daily Development

1. **Start services**: `php artisan serve` (or Docker containers)
2. **Watch assets**: `npm run dev`
3. **Run queue worker**: `php artisan queue:work`
4. **Run scheduler**: `php artisan schedule:work`

### Code Quality

```bash
# Format code
composer lint

# Run tests
composer test

# Static analysis
composer analyse
```

### Testing

This project uses Pest for testing:

```bash
# Run all tests
vendor/bin/pest

# Run specific test
vendor/bin/pest --filter=UserTest

# Run with coverage
vendor/bin/pest --coverage
```

## Key Conventions

### Models

- Extend `App\Models\Base` (not Eloquent Model)
- Use UUID primary keys
- Include audit trails and soft deletes
- Follow naming: `User`, `BlogPost`, `OrderItem`

### Livewire

- Use Volt for simple components
- Store in `resources/views/livewire/`
- Follow naming: `user-profile.blade.php`, `CreatePost.php`

### Routes

- API routes in `routes/api.php`
- Web routes in `routes/web.php`
- Use resource controllers where applicable

### Helpers

Domain-specific helpers in `support/`:

- `support/user.php` - User utilities
- `support/flash.php` - Flash messages
- `support/media.php` - Media handling
- `support/menu.php` - Menu builders

## Project Structure

```text
app/
├── Http/
│   ├── Controllers/    # Request handlers
│   └── Middleware/     # Request filters
├── Models/             # Eloquent models
├── Livewire/          # Livewire components
└── Console/           # Artisan commands

resources/
├── views/
│   ├── livewire/      # Livewire templates
│   └── components/    # Blade components
└── js/                # Frontend assets

support/               # Helper functions
tests/                 # Pest tests
```

## Cross-References

- **Getting Started**: [Installation](../01-getting-started/01-installation.md), [Setup](../01-getting-started/02-setup.md)
- **Architecture**: [Code Quality](../03-architecture/01-code-quality.md)
- **Deployment**: [Deployment Process](../04-deployment/01-deployment.md)

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Pest Documentation](https://pestphp.com)
- [TailwindCSS v4](https://tailwindcss.com)
