<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * DB-stored SEO & analytics configuration.
 *
 * Drives the shared `partials/seo.blade.php` + `partials/analytics.blade.php`
 * head partials (meta defaults, Open Graph, canonical, JSON-LD organization
 * schema, GA4/GTM snippets) and the dynamic `/robots.txt` route. Everything is
 * admin-editable at Admin > Settings > SEO & Analytics — `.env` only provides
 * first-boot defaults via `config/seo.php`.
 *
 * @property string|null $meta_title Default meta/OG title (falls back to the site name).
 * @property string|null $meta_description Default meta description used site-wide unless a page overrides it.
 * @property string|null $meta_keywords Comma-separated meta keywords (legacy — ignored by Google, kept for completeness).
 * @property bool $canonical_enabled Whether a canonical <link> for the current URL is emitted.
 * @property string|null $og_image Absolute URL of the default Open Graph / Twitter share image.
 * @property string|null $twitter_site The site's X/Twitter @handle for twitter:site.
 * @property string|null $google_analytics_id GA4 Measurement ID (G-XXXXXXXXXX); snippet renders only when set.
 * @property string|null $google_tag_manager_id GTM container ID (GTM-XXXXXXX); snippet renders only when set.
 * @property string|null $google_site_verification Google Search Console verification token.
 * @property string $robots_txt Content served at /robots.txt (the Sitemap line is appended automatically).
 * @property string|null $organization_name Organization name for the JSON-LD Organization schema.
 * @property string|null $organization_logo Absolute URL of the organization logo for the schema.
 * @property string|null $organization_same_as Newline-separated social/profile URLs for the schema's sameAs.
 */
class SeoSettings extends Settings
{
    public ?string $meta_title;

    public ?string $meta_description;

    public ?string $meta_keywords;

    public bool $canonical_enabled;

    public ?string $og_image;

    public ?string $twitter_site;

    public ?string $google_analytics_id;

    public ?string $google_tag_manager_id;

    public ?string $google_site_verification;

    public string $robots_txt;

    public ?string $organization_name;

    public ?string $organization_logo;

    public ?string $organization_same_as;

    public static function group(): string
    {
        return 'seo';
    }
}
