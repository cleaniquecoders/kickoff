<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Every field starts null = "not set in the UI", so G8SCOPE_* env (or the
     * SDK default) stays in charge until an admin overrides it in
     * Admin > Settings > Error tracking. Never snapshot env here: a snapshot
     * would shadow env changes made later (e.g. by G8Deck).
     */
    private const array FIELDS = [
        'enabled', 'endpoint', 'environment', 'release', 'timeout', 'max_retries',
        'retry_base_ms', 'retry_cap_ms', 'max_frames', 'max_message',
        'ignore_exceptions', 'scrub_keys',
    ];

    public function up(): void
    {
        foreach (self::FIELDS as $field) {
            $this->migrator->add("bliptrace.{$field}", null);
        }

        // Third arg = encrypted at rest (mirrors BliptraceSettings::encrypted()).
        $this->migrator->add('bliptrace.key', null, true);
    }

    public function down(): void
    {
        foreach ([...self::FIELDS, 'key'] as $field) {
            $this->migrator->deleteIfExists("bliptrace.{$field}");
        }
    }
};
