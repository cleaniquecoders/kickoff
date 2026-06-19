<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Full SMTP configuration, seeded from the current config/.env so existing
        // installs keep working. Admin-editable from Administration > Mail > Settings.
        $this->migrator->add('mail.mailer', (string) config('mail.default', 'smtp'));
        $this->migrator->add('mail.host', (string) config('mail.mailers.smtp.host', '127.0.0.1'));
        $this->migrator->add('mail.port', (int) config('mail.mailers.smtp.port', 587));
        $this->migrator->add('mail.username', (string) config('mail.mailers.smtp.username', ''));
        $this->migrator->add('mail.password', (string) config('mail.mailers.smtp.password', ''));
        $this->migrator->add('mail.encryption', (string) config('mail.mailers.smtp.encryption', ''));
    }

    public function down(): void
    {
        $this->migrator->delete('mail.mailer');
        $this->migrator->delete('mail.host');
        $this->migrator->delete('mail.port');
        $this->migrator->delete('mail.username');
        $this->migrator->delete('mail.password');
        $this->migrator->delete('mail.encryption');
    }
};
