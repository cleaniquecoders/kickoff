<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Admin\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Admin\Settings\Seo — review query/prop hydration before production use.
 */
class SeoController
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/settings/seo', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Admin/Settings/Seo.php
            // ['canonicalEnabled', 'googleAnalyticsId', 'googleSiteVerification', 'googleTagManagerId', 'metaDescription', 'metaKeywords', 'metaTitle', 'ogImage', 'organizationLogo', 'organizationName', 'organizationSameAs', 'robotsTxt', 'twitterSite']
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ported from Livewire $this->validate()

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

        // TODO: port the component's save/action methods here.
    }
}
