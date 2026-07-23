{{--
    SEO meta tags — driven by Admin > Settings > SEO & Analytics (config('seo.*')
    is overlaid from App\Settings\SeoSettings at boot).

    Pages may override the description per page the same way $title works:
    <x-layouts.app :title="__('Pricing')" :description="__('Plans and pricing.')">
--}}
@php
    $seoTitle = isset($title) ? $title.' - '.config('app.name') : seo_title();
    $seoDescription = $description ?? config('seo.meta.description');
@endphp

@if ($seoDescription)
    <meta name="description" content="{{ $seoDescription }}">
@endif
@if (config('seo.meta.keywords'))
    <meta name="keywords" content="{{ config('seo.meta.keywords') }}">
@endif
@if (config('seo.google.site_verification'))
    <meta name="google-site-verification" content="{{ config('seo.google.site_verification') }}">
@endif
@if (config('seo.canonical'))
    <link rel="canonical" href="{{ request()->url() }}">
@endif

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ $seoTitle }}">
@if ($seoDescription)
    <meta property="og:description" content="{{ $seoDescription }}">
@endif
<meta property="og:url" content="{{ request()->url() }}">
<meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
@if (config('seo.og_image'))
    <meta property="og:image" content="{{ config('seo.og_image') }}">
@endif

{{-- Twitter / X Card --}}
<meta name="twitter:card" content="{{ config('seo.og_image') ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $seoTitle }}">
@if ($seoDescription)
    <meta name="twitter:description" content="{{ $seoDescription }}">
@endif
@if (config('seo.og_image'))
    <meta name="twitter:image" content="{{ config('seo.og_image') }}">
@endif
@if (config('seo.twitter_site'))
    <meta name="twitter:site" content="{{ config('seo.twitter_site') }}">
@endif

{{-- Structured data (JSON-LD) — Organization renders only once configured.
     Per-page schemas (FAQ, Article, Product, …) via the seo_schema_*() helpers. --}}
{{ seo_schema_organization() }}
{{ seo_schema_website() }}
