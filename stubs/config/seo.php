<?php

/*
 * SEO & analytics defaults.
 *
 * These are FIRST-BOOT defaults only — once the app is migrated, the values in
 * App\Settings\SeoSettings (Admin > Settings > SEO & Analytics) are laid over
 * this config at boot by AppServiceProvider::applyDatabaseSettings(). Read via
 * config('seo.*') everywhere; never read the Settings class in views.
 */
return [

    'meta' => [
        // Falls back to config('app.name') when null.
        'title' => null,
        'description' => null,
        'keywords' => null,
    ],

    // Emit <link rel="canonical"> for the current URL.
    'canonical' => true,

    // Absolute URL of the default Open Graph / Twitter share image.
    'og_image' => null,

    // The site's X/Twitter @handle (e.g. "@acme").
    'twitter_site' => null,

    'google' => [
        // GA4 Measurement ID, e.g. G-XXXXXXXXXX. Snippet renders only when set.
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),

        // Google Tag Manager container ID, e.g. GTM-XXXXXXX.
        'tag_manager_id' => env('GOOGLE_TAG_MANAGER_ID'),

        // Google Search Console verification token (content of the meta tag).
        'site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    ],

    // Served at /robots.txt; the Sitemap: line is appended automatically.
    'robots_txt' => "User-agent: *\nDisallow:",

    // JSON-LD Organization schema (rendered when a name is set).
    'organization' => [
        'name' => null,
        'logo' => null,
        // Newline-separated social/profile URLs (sameAs).
        'same_as' => null,
    ],

];
