# Configuration Backup

Backup/restore of application configuration via
[cleaniquecoders/laravel-config-backup](https://github.com/cleaniquecoders/laravel-config-backup):
`.env` + allowlisted DB-stored settings exported as a portable AES-256 password-encrypted ZIP.
Encrypted DB values are re-encrypted with the destination server's `APP_KEY` on import, so
backups are portable across servers. A safety snapshot is taken before every restore.

This complements the `bin/backup-*` scripts: those cover the full database and media;
config backup covers *configuration only* (secrets, settings) in a portable format.

## Pre-configured Defaults

- **Admin UI** at `/admin/config-backup` (sidebar: Settings → Config Backup), gated by the
  `admin.manage.config-backup` permission
- **Database allowlist** pre-filled with `Spatie\LaravelSettings\Models\SettingsProperty` —
  the Admin → Settings values are included in every backup
- **Storage**: `local` disk under `config-backups/` (keep it private — archives contain secrets),
  retention of 10 backups

## CLI

```bash
php artisan config-backup:create --sections=env,database --notes="pre-deploy"
php artisan config-backup:list
php artisan config-backup:restore {uuid} --dry-run
```

## Scheduled Backups

Off by default. Enable via `.env` (requires a password for unattended encryption):

```env
CONFIG_BACKUP_SCHEDULE=true
CONFIG_BACKUP_SCHEDULE_CRON="0 2 * * *"
CONFIG_BACKUP_SCHEDULE_PASSWORD=CHANGE_ME_BEFORE_DEPLOY
```

Optional notifications: `CONFIG_BACKUP_NOTIFICATIONS=true` + `CONFIG_BACKUP_NOTIFICATION_MAIL`.
