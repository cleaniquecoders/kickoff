# Deployment

This section covers production deployment processes, infrastructure setup, and operational procedures.

## Overview

This project uses a Git-based deployment workflow with automated scripts for consistent and reliable releases.

## Table of Contents

1. [Deployment Process](01-deployment.md) - Production deployment guide

## Deployment Strategy

### Release Process

- **Tags**: Deploy only tagged releases (no direct branch deploys)
- **Automation**: Use `bin/deploy` script for consistency
- **Rollback**: Git-based rollback to previous tags
- **Zero Downtime**: Use queue workers and horizon for background tasks

### Infrastructure

**Required Services**:

- **Web Server**: Nginx or Apache
- **PHP**: 8.3+ with required extensions
- **Database**: MySQL 8.0+
- **Cache**: Redis for sessions and cache
- **Queue**: Redis or database driver
- **Storage**: MinIO or S3 for media files
- **Search**: Elasticsearch (optional)

**Docker Services** (configured in `docker-compose.yml`):

- MinIO (S3-compatible storage)
- Elasticsearch
- Redis

### Environment Setup

1. **SSH Keys**: Create deployment keys for GitHub access
2. **Environment Variables**: Configure `.env` on server
3. **Permissions**: Set correct file permissions for Laravel
4. **Supervisor**: Configure for Horizon queue worker
5. **Cron**: Schedule Laravel scheduler

### Security Considerations

- Use deployment keys (not personal SSH keys)
- Environment variables never in version control
- HTTPS only in production
- Database credentials properly secured
- File permissions set correctly (`storage/` writable)

## Deployment Workflow

### Initial Setup

1. Clone repository on server
2. Run `bin/install` for initial setup
3. Configure web server
4. Set up Supervisor for queues
5. Configure cron for scheduler

### Subsequent Deployments

1. Create and push a Git tag
2. SSH into server
3. Run `bin/deploy` script
4. Script automatically:
   - Pulls latest tagged release
   - Installs dependencies
   - Runs migrations
   - Clears caches
   - Restarts services

### Manual Deployment Steps

If not using `bin/deploy`:

```bash
# Pull latest tag
git fetch --tags
git checkout tags/v1.0.0

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Run migrations
php artisan migrate --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
php artisan queue:restart
supervisorctl restart horizon
```

## Monitoring & Maintenance

### Logging

- Application logs: `storage/logs/laravel.log`
- Web server logs: `/var/log/nginx/` or `/var/log/apache2/`
- Supervisor logs: `/var/log/supervisor/`
- Queue logs: Check Horizon dashboard

### Backups

Use provided scripts:

```bash
# Backup application
bin/backup-app

# Backup media files
bin/backup-media
```

### Performance

- **Opcache**: Enable in production
- **Queue Workers**: Scale based on load
- **Database**: Index frequently queried columns
- **CDN**: Serve static assets via CDN

## Troubleshooting

### Common Issues

- **Migrations fail**: Check database permissions
- **Assets not loading**: Run `npm run build`
- **Queue not processing**: Check Supervisor and Horizon
- **Permission errors**: Verify `storage/` and `bootstrap/cache/` are writable

### Health Checks

```bash
# Check application status
php artisan about

# Check queue status
php artisan horizon:status

# Check scheduled tasks
php artisan schedule:list
```

## Cross-References

- **Getting Started**: [Installation](../01-getting-started/01-installation.md)
- **Development**: [Development Workflows](../02-development/README.md)
- **Architecture**: [Code Quality](../03-architecture/01-code-quality.md)

## Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Horizon Documentation](https://laravel.com/docs/horizon)
- [Forge Deployment](https://forge.laravel.com)
