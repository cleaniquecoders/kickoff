<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Open/click tracking was env-only (MAILHISTORY_TRACK_*): turning it off needed a
        // deploy. Seed from the current config so existing installs keep their behaviour.
        $this->migrator->add('mail.track_opens', (bool) config('mailhistory.tracking.open.enabled', true));
        $this->migrator->add('mail.track_clicks', (bool) config('mailhistory.tracking.click.enabled', true));
    }

    public function down(): void
    {
        $this->migrator->delete('mail.track_opens');
        $this->migrator->delete('mail.track_clicks');
    }
};
