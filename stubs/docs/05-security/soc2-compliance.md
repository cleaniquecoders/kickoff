# SOC 2 Compliance Controls

This document describes the security controls implemented in this project to support SOC 2 Type II compliance across the five Trust Service Criteria.

## 1. Security (CC1-CC9)

### Authentication & Access Control
- **RBAC**: Spatie Laravel Permission with three default roles (`superadmin`, `administrator`, `user`) and 26+ granular permissions using `module.action.target` format
- **MFA/2FA**: Laravel Fortify configured with two-factor authentication (TOTP), password confirmation required
- **Password Policy**: Enforced globally via `Password::defaults()` — minimum 12 characters, mixed case, numbers, symbols, uncompromised check (configurable via `config/security.php`)
- **Email Verification**: Required for all admin and security routes (`MustVerifyEmail`)
- **Single-Device Sessions**: Optional logout from other devices on authentication (`config('auth.single-device')`)
- **Impersonation**: Disabled by default, superadmin cannot be impersonated

### Rate Limiting
- Global throttle middleware via `throttleWithRedis()`
- Route-level rate limiting on auth (`throttle:60,1`), admin, and security routes
- Fortify rate limiters on login and two-factor endpoints

### Security Headers
- `SecurityHeaders` middleware applied globally:
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: DENY`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy: camera=(), microphone=(), geolocation=()`
  - `Strict-Transport-Security` (production only)
- HTTPS forced in production via `URL::forceScheme('https')`

### Authorization
- Policy-based authorization on all resources (User, Role, Audit, Team, Media)
- Gate definitions in `AdminServiceProvider` for admin panel, monitoring tools, and management functions
- Livewire components enforce `$this->authorize()` on state-changing methods

## 2. Availability (A1)

### Backup & Recovery
- **Application Backup** (`bin/backup-app`): Full application + database dump, zip compression, integrity verification
- **Database Backup** (`bin/backup-db`): Dedicated database backup with `--single-transaction`, gzip compression
- **Media Backup** (`bin/backup-media`): Incremental (last 24 hours) media file backup
- **Encryption**: Optional GPG encryption via `BACKUP_GPG_RECIPIENT` env var
- **Retention Policy**: Automatic cleanup of backups older than `BACKUP_RETENTION_DAYS` (default 30)

### Deployment
- **Maintenance Mode**: `php artisan down --retry=60` during deployment
- **Health Check**: Automated HTTP check to `/up` endpoint post-deployment
- **Auto-Rollback**: Reverts to previous commit if health check fails
- **Pre-Deploy Backup**: Database backup before code changes

### Infrastructure
- Docker Compose with health checks on MySQL, Redis, MinIO
- Restart policies (`unless-stopped`) on all services
- Redis password authentication enabled

## 3. Processing Integrity (PI1)

### Audit Trail
- All models extending `Base` automatically audited via `owen-it/laravel-auditing`
- Events tracked: `created`, `updated`, `deleted`, `restored`
- Console commands audited (`AUDIT_CONSOLE=true`)
- Audit records are **immutable** — `AuditPolicy` blocks create, update, delete, restore, forceDelete
- IP address, user agent, and URL captured on every audit entry

### Data Validation
- Architecture tests enforce no raw SQL queries — Eloquent ORM only
- `env()` restricted to config files only
- No `dd()`, `dump()`, `ray()` in application code

### Static Analysis
- PHPStan at level 5 with Larastan
- Rector for automated PHP 8.4+ refactoring
- Laravel Pint for code style enforcement
- Security CI workflow runs `composer audit` on every push

## 4. Confidentiality (C1)

### Encryption
- **At Rest**: `APP_KEY`-based encryption, `SESSION_ENCRYPT=true` by default
- **PII Encryption**: `EncryptsPii` trait available for field-level encryption on sensitive columns
- **In Transit**: HTTPS enforced in production, `MAIL_ENCRYPTION=tls`
- **Backup Encryption**: Optional GPG encryption for all backup scripts

### Secrets Management
- No default credentials in `.env.example` — all sensitive values use `CHANGE_ME_BEFORE_DEPLOY` placeholders
- Telescope hides sensitive request parameters (`password`, `password_confirmation`, `current_password`, `secret`) and headers (`authorization`, `cookie`, CSRF tokens)
- Telescope disabled by default (`TELESCOPE_ENABLED=false`)

### Access Control
- Media files stored with `private` visibility
- Media access requires `auth:sanctum` middleware and model-level policy authorization
- UUID primary keys prevent ID enumeration

## 5. Privacy (P1-P8)

### PII Handling
- `EncryptsPii` trait for field-level encryption of PII columns
- `RedactsPiiInAudit` trait masks sensitive fields in audit trail `old_values`/`new_values`
- User model hides `password` and `remember_token` from serialization

### Data Retention
- `data:purge` artisan command with configurable retention:
  - Audit records: `--audit-days=365` (default)
  - Telescope entries: `--telescope-hours=48` (default)
  - Soft-deleted users: permanently removed after 90 days
  - Supports `--dry-run` for preview
- Backup retention: configurable via `BACKUP_RETENTION_DAYS`

### Data Protection
- Soft deletes on User model preserve audit trail
- UUID-based lookups prevent information disclosure from sequential IDs

## Configuration Reference

### Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `SESSION_ENCRYPT` | `true` | Encrypt session data at rest |
| `PASSWORD_MIN_LENGTH` | `12` | Minimum password length |
| `PASSWORD_REQUIRE_MIXED_CASE` | `true` | Require upper and lowercase |
| `PASSWORD_REQUIRE_NUMBERS` | `true` | Require numeric characters |
| `PASSWORD_REQUIRE_SYMBOLS` | `true` | Require special characters |
| `PASSWORD_REQUIRE_UNCOMPROMISED` | `true` | Check against known breaches |
| `AUDIT_CONSOLE` | `true` | Audit console commands |
| `BACKUP_RETENTION_DAYS` | `30` | Days to retain backups |
| `BACKUP_GPG_RECIPIENT` | (empty) | GPG key ID for backup encryption |
| `TELESCOPE_ENABLED` | `false` | Enable/disable Telescope |

### Key Files

| File | Purpose |
|------|---------|
| `config/security.php` | Password policy, session security settings |
| `config/audit.php` | Audit trail configuration |
| `config/fortify.php` | Authentication features, 2FA |
| `config/access-control.php` | Roles, permissions, role scopes |
| `app/Http/Middleware/SecurityHeaders.php` | Security response headers |
| `app/Concerns/EncryptsPii.php` | PII encryption trait |
| `app/Concerns/RedactsPiiInAudit.php` | Audit PII masking trait |
| `app/Console/Commands/PurgeExpiredDataCommand.php` | Data retention/purge |
| `bin/backup-app` | Application + database backup |
| `bin/backup-db` | Database backup |
| `bin/backup-media` | Media file backup |
| `bin/deploy` | Deployment with rollback |

## Extending Controls

### Adding PII Encryption to a Model

```php
use App\Concerns\EncryptsPii;

class Customer extends Base
{
    use EncryptsPii;

    protected function piiFields(): array
    {
        return ['phone', 'address', 'national_id'];
    }
}
```

> Do NOT encrypt fields used in WHERE clauses or unique constraints.

### Masking PII in Audit Logs

```php
use App\Concerns\RedactsPiiInAudit;

class Customer extends Base
{
    use RedactsPiiInAudit;

    protected array $auditRedactFields = ['phone', 'national_id'];
}
```

### Scheduling Data Purge

Add to your scheduler (`routes/console.php` or `app/Console/Kernel.php`):

```php
Schedule::command('data:purge --audit-days=365 --telescope-hours=48')->daily();
```
