<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * bliptrace/laravel 1.3 options. Null = not set in the UI, so BLIPTRACE_* env
     * (or the SDK default) stays in charge — same rule as the first migration.
     */
    private const array FIELDS = [
        'max_breadcrumbs', 'breadcrumbs_requests', 'breadcrumbs_queries', 'breadcrumbs_logs',
        'breadcrumbs_jobs', 'breadcrumbs_cache', 'tags',
    ];

    public function up(): void
    {
        foreach (self::FIELDS as $field) {
            $this->migrator->add("bliptrace.{$field}", null);
        }
    }

    public function down(): void
    {
        foreach (self::FIELDS as $field) {
            $this->migrator->deleteIfExists("bliptrace.{$field}");
        }
    }
};
