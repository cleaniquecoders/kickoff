<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Settings;

use App\Settings\SeoSettings;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Admin > Settings > SEO & Analytics.
 *
 * Manages the DB-stored SEO defaults (meta tags, Open Graph, canonical,
 * JSON-LD organization schema), the /robots.txt content, and the analytics
 * IDs (GA4 / GTM / Search Console verification) rendered by the shared
 * `partials/seo.blade.php` + `partials/analytics.blade.php` head partials.
 */
#[Layout('components.layouts.app')]
class Seo extends Component
{
    public ?string $metaTitle = null;

    public ?string $metaDescription = null;

    public ?string $metaKeywords = null;

    public bool $canonicalEnabled = true;

    public ?string $ogImage = null;

    public ?string $twitterSite = null;

    public ?string $googleAnalyticsId = null;

    public ?string $googleTagManagerId = null;

    public ?string $googleSiteVerification = null;

    public string $robotsTxt = '';

    public ?string $organizationName = null;

    public ?string $organizationLogo = null;

    public ?string $organizationSameAs = null;

    public function mount(): void
    {
        $this->authorize('manage.settings');

        $settings = app(SeoSettings::class);
        $this->metaTitle = $settings->meta_title;
        $this->metaDescription = $settings->meta_description;
        $this->metaKeywords = $settings->meta_keywords;
        $this->canonicalEnabled = $settings->canonical_enabled;
        $this->ogImage = $settings->og_image;
        $this->twitterSite = $settings->twitter_site;
        $this->googleAnalyticsId = $settings->google_analytics_id;
        $this->googleTagManagerId = $settings->google_tag_manager_id;
        $this->googleSiteVerification = $settings->google_site_verification;
        $this->robotsTxt = $settings->robots_txt;
        $this->organizationName = $settings->organization_name;
        $this->organizationLogo = $settings->organization_logo;
        $this->organizationSameAs = $settings->organization_same_as;
    }

    public function save(): void
    {
        $this->authorize('manage.settings');

        // Livewire 4 needs inline rules.
        $this->validate([
            'metaTitle' => ['nullable', 'string', 'max:255'],
            'metaDescription' => ['nullable', 'string', 'max:500'],
            'metaKeywords' => ['nullable', 'string', 'max:255'],
            'canonicalEnabled' => ['boolean'],
            'ogImage' => ['nullable', 'url', 'max:2048'],
            'twitterSite' => ['nullable', 'regex:/^@[A-Za-z0-9_]{1,15}$/'],
            'googleAnalyticsId' => ['nullable', 'regex:/^G-[A-Z0-9]{4,}$/i'],
            'googleTagManagerId' => ['nullable', 'regex:/^GTM-[A-Z0-9]{4,}$/i'],
            'googleSiteVerification' => ['nullable', 'string', 'max:255'],
            'robotsTxt' => ['required', 'string', 'max:5000'],
            'organizationName' => ['nullable', 'string', 'max:255'],
            'organizationLogo' => ['nullable', 'url', 'max:2048'],
            'organizationSameAs' => ['nullable', 'string', 'max:2000'],
        ], [
            'googleAnalyticsId.regex' => __('The Measurement ID must look like G-XXXXXXXXXX.'),
            'googleTagManagerId.regex' => __('The container ID must look like GTM-XXXXXXX.'),
            'twitterSite.regex' => __('The handle must look like @yourhandle.'),
        ], [
            'metaTitle' => 'meta title',
            'metaDescription' => 'meta description',
            'metaKeywords' => 'meta keywords',
            'ogImage' => 'share image URL',
            'twitterSite' => 'X/Twitter handle',
            'googleAnalyticsId' => 'Google Analytics ID',
            'googleTagManagerId' => 'Tag Manager ID',
            'googleSiteVerification' => 'site verification token',
            'robotsTxt' => 'robots.txt content',
            'organizationName' => 'organization name',
            'organizationLogo' => 'organization logo URL',
            'organizationSameAs' => 'social profile URLs',
        ]);

        $settings = app(SeoSettings::class);
        $settings->meta_title = $this->nullable($this->metaTitle);
        $settings->meta_description = $this->nullable($this->metaDescription);
        $settings->meta_keywords = $this->nullable($this->metaKeywords);
        $settings->canonical_enabled = $this->canonicalEnabled;
        $settings->og_image = $this->nullable($this->ogImage);
        $settings->twitter_site = $this->nullable($this->twitterSite);
        $settings->google_analytics_id = $this->nullable($this->googleAnalyticsId);
        $settings->google_tag_manager_id = $this->nullable($this->googleTagManagerId);
        $settings->google_site_verification = $this->nullable($this->googleSiteVerification);
        $settings->robots_txt = $this->robotsTxt;
        $settings->organization_name = $this->nullable($this->organizationName);
        $settings->organization_logo = $this->nullable($this->organizationLogo);
        $settings->organization_same_as = $this->nullable($this->organizationSameAs);
        $settings->save();

        $this->dispatch('toast',
            type: 'success',
            message: __('SEO & analytics settings saved successfully!'),
            duration: 3000,
        );
    }

    /**
     * Normalize empty form input to null so blank fields don't render tags.
     */
    private function nullable(?string $value): ?string
    {
        return ($value === null || trim($value) === '') ? null : trim($value);
    }

    public function render(): View
    {
        return view('livewire.admin.settings.seo');
    }
}
