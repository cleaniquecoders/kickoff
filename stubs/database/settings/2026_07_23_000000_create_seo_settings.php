<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Ships with sensible empty defaults: no analytics snippets render and
        // no meta description is emitted until an admin fills these in at
        // Admin > Settings > SEO & Analytics. GA/GTM seed from .env so existing
        // deploys that already export the IDs pick them up on first migrate.
        $this->migrator->add('seo.meta_title', null);
        $this->migrator->add('seo.meta_description', null);
        $this->migrator->add('seo.meta_keywords', null);
        $this->migrator->add('seo.canonical_enabled', true);
        $this->migrator->add('seo.og_image', null);
        $this->migrator->add('seo.twitter_site', null);
        $this->migrator->add('seo.google_analytics_id', config('seo.google.analytics_id'));
        $this->migrator->add('seo.google_tag_manager_id', config('seo.google.tag_manager_id'));
        $this->migrator->add('seo.google_site_verification', config('seo.google.site_verification'));
        $this->migrator->add('seo.robots_txt', "User-agent: *\nDisallow:");
        $this->migrator->add('seo.organization_name', null);
        $this->migrator->add('seo.organization_logo', null);
        $this->migrator->add('seo.organization_same_as', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('seo.organization_same_as');
        $this->migrator->deleteIfExists('seo.organization_logo');
        $this->migrator->deleteIfExists('seo.organization_name');
        $this->migrator->deleteIfExists('seo.robots_txt');
        $this->migrator->deleteIfExists('seo.google_site_verification');
        $this->migrator->deleteIfExists('seo.google_tag_manager_id');
        $this->migrator->deleteIfExists('seo.google_analytics_id');
        $this->migrator->deleteIfExists('seo.twitter_site');
        $this->migrator->deleteIfExists('seo.og_image');
        $this->migrator->deleteIfExists('seo.canonical_enabled');
        $this->migrator->deleteIfExists('seo.meta_keywords');
        $this->migrator->deleteIfExists('seo.meta_description');
        $this->migrator->deleteIfExists('seo.meta_title');
    }
};
