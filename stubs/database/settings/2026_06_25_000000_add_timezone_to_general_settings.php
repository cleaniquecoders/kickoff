<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Seed from the env-driven config so existing deployments keep their
        // current timezone after migrating.
        $this->migrator->add('general.timezone', (string) config('app.timezone', 'UTC'));
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('general.timezone');
    }
};
