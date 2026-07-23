# SEO & Analytics

Everything search-engine and analytics related is configured from **Admin > Settings > SEO & Analytics** — no deploy needed. Values are stored via `App\Settings\SeoSettings` (Spatie Laravel Settings) and laid over `config('seo.*')` at boot, so views and routes always read config.

## What renders where

| Feature | Where | Controlled by |
|---|---|---|
| Meta title / description / keywords | `partials/seo.blade.php` (included by `partials/head.blade.php` in every layout) | Settings screen; per-page override below |
| Canonical URL | Same partial — `<link rel="canonical">` for the current URL | "Emit canonical URLs" toggle |
| Open Graph + X/Twitter cards | Same partial | Meta fields + share image + handle |
| Google Search Console verification | Same partial | Verification token field |
| JSON-LD `Organization` + `WebSite` schemas | Same partial (Organization only once a name is set) | Structured Data card |
| GA4 / GTM snippets | `partials/analytics.blade.php` (head) + `partials/analytics-noscript.blade.php` (body) | Analytics card — snippets render **only when an ID is set** |
| `/robots.txt` | `routes/web/seo.php` — dynamic, `Sitemap:` line auto-appended | robots.txt textarea |
| `/sitemap.xml` | `routes/web/seo.php` — public routes on the fly; a generated `public/sitemap.xml` takes precedence | — |

## Per-page meta

`$title` and `$description` flow through the layouts into the head partial:

```blade
<x-layouts.app :title="__('Pricing')" :description="__('Simple plans that scale with you.')">
    ...
</x-layouts.app>
```

Pages without a `$description` fall back to the site-wide meta description.

## Structured data helpers (`support/seo.php`)

`Organization` and `WebSite` render automatically. For page-level rich results, call the helpers from any Blade view (they return a ready `<script type="application/ld+json">` tag):

```blade
{{-- Breadcrumb trail --}}
{{ seo_schema_breadcrumb([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Blog', 'url' => route('blog.index')],
    ['name' => $post->title],
]) }}

{{-- FAQ rich results --}}
{{ seo_schema_faq([
    ['question' => 'How do I reset my password?', 'answer' => 'Use the Forgot Password link.'],
]) }}

{{-- Blog / news article --}}
{{ seo_schema_article([
    'headline' => $post->title,
    'description' => $post->excerpt,
    'image' => $post->cover_url,
    'author' => $post->author->name,
    'published_at' => $post->published_at,
    'updated_at' => $post->updated_at,
]) }}

{{-- Product with offer --}}
{{ seo_schema_product([
    'name' => $product->name,
    'description' => $product->summary,
    'price' => $product->price,
    'currency' => 'MYR',
]) }}

{{-- Training / course page --}}
{{ seo_schema_course(['name' => $course->name, 'description' => $course->summary]) }}

{{-- Event registration page --}}
{{ seo_schema_event([
    'name' => $event->name,
    'starts_at' => $event->starts_at,
    'ends_at' => $event->ends_at,
    'location' => $event->venue,
]) }}

{{-- Generic page --}}
{{ seo_schema_webpage() }}

{{-- Anything else — build it yourself --}}
{{ seo_schema(['@type' => 'HowTo', 'name' => '...']) }}
```

Null/empty values are stripped recursively, so optional fields can be passed as-is. Validate output with Google's [Rich Results Test](https://search.google.com/test/rich-results).

## Sitemap

Out of the box, `/sitemap.xml` lists the public routes (just the home page on a fresh install). As the site gains public content, either:

1. **Extend the route** in `routes/web/seo.php` with your public URLs, or
2. **Crawl the site** with `spatie/laravel-sitemap`:

```bash
php artisan seo:generate-sitemap   # writes public/sitemap.xml
```

Schedule it for content-heavy sites (in `routes/console.php`):

```php
Schedule::command('seo:generate-sitemap')->daily();
```

The generated static file takes precedence over the dynamic route.

## Google Analytics / Tag Manager

1. Get a **GA4 Measurement ID** (GA4 → Admin → Data Streams → `G-XXXXXXXXXX`) and/or a **GTM container ID** (`GTM-XXXXXXX`).
2. Paste into **Admin > Settings > SEO & Analytics**.
3. Done — the snippets render on the next request (GTM includes the `<noscript>` iframe after `<body>`).

Guidelines:

- Use **either** GA4 directly **or** GTM (loading GA4 through the container) — both at once double-fires events.
- Leave IDs **blank on local/staging** so dev traffic never pollutes the property. `.env` seeds (`GOOGLE_ANALYTICS_ID`, `GOOGLE_TAG_MANAGER_ID`) apply on first migrate only.

## hreflang

The stub is single-locale, so no `hreflang` tags are emitted. If the app becomes multilingual, add alternate links in `partials/seo.blade.php` per locale route and set `og:locale:alternate` accordingly.

## Performance checklist (Core Web Vitals)

These affect ranking but are infrastructure/markup concerns rather than settings:

- **Lazy loading** — add `loading="lazy"` to below-the-fold `<img>` tags.
- **Image compression / WebP-AVIF** — Spatie Media Library conversions can output WebP: `$this->addMediaConversion('web')->format('webp')`.
- **Minify CSS/JS** — Vite does this on `npm run build`; nothing to do.
- **Gzip / Brotli, HTTP/2+** — enable at the web server (nginx: `gzip on;` / `brotli on;`, HTTP/2 on the TLS listener).
- **Browser cache** — serve `build/` assets with long-lived `Cache-Control` (Vite fingerprints filenames).
- **CDN** — point a CDN at `public/`; set `ASSET_URL` if serving assets from it.

Measure with [PageSpeed Insights](https://pagespeed.web.dev) after deploying.
