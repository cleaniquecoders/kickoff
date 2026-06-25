<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Seed from the env-driven config so existing deployments keep their
        // current behaviour (public registration on) after migrating.
        $this->migrator->add(
            'authentication.public_registration_enabled',
            (bool) config('admin.public_registration', true),
        );
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('authentication.public_registration_enabled');
    }
};
