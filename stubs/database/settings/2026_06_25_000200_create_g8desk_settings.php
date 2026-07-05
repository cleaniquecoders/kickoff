<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Ships disabled with the public g8desk base URL. The keys stay blank
        // until an admin pastes them in Admin > Settings > g8desk Support, so a
        // fresh install renders no widget until it's deliberately configured.
        $this->migrator->add('g8desk.enabled', false);
        $this->migrator->add('g8desk.base_url', 'https://g8desk.com');
        $this->migrator->add('g8desk.public_key', '');

        // Third arg = encrypted at rest (mirrors G8DeskSettings::encrypted()).
        $this->migrator->add('g8desk.widget_secret', '', true);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('g8desk.widget_secret');
        $this->migrator->deleteIfExists('g8desk.public_key');
        $this->migrator->deleteIfExists('g8desk.base_url');
        $this->migrator->deleteIfExists('g8desk.enabled');
    }
};
