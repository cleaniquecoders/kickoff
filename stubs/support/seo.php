<?php

declare(strict_types=1);

use Illuminate\Support\HtmlString;

if (! function_exists('seo_title')) {
    /**
     * The default meta/OG title — admin-set meta title or the site name.
     */
    function seo_title(): string
    {
        return config('seo.meta.title') ?: config('app.name', 'Laravel');
    }
}

if (! function_exists('seo_schema')) {
    /**
     * Render an arbitrary schema.org structure as a JSON-LD script tag.
     * Null values are stripped recursively so optional fields can be passed as-is.
     *
     * @param  array<string, mixed>  $schema
     */
    function seo_schema(array $schema): HtmlString
    {
        $clean = function (array $data) use (&$clean): array {
            $cleaned = collect($data)
                ->map(fn ($value) => is_array($value) ? $clean($value) : $value)
                ->filter(fn ($value) => $value !== null && $value !== [] && $value !== '')
                ->all();

            // Keep JSON arrays as arrays (not objects) when filtering left gaps.
            return array_is_list($data) ? array_values($cleaned) : $cleaned;
        };

        $schema = $clean(['@context' => 'https://schema.org'] + $schema);

        return new HtmlString(
            '<script type="application/ld+json">'
            .json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG)
            .'</script>'
        );
    }
}

if (! function_exists('seo_schema_organization')) {
    /**
     * Organization schema from the admin-set SEO settings.
     * Renders nothing until an organization name is configured.
     */
    function seo_schema_organization(): HtmlString
    {
        $name = config('seo.organization.name');

        if (blank($name)) {
            return new HtmlString('');
        }

        $sameAs = collect(preg_split('/\R+/', (string) config('seo.organization.same_as')) ?: [])
            ->map(fn (string $urlLine) => trim($urlLine))
            ->filter()
            ->values()
            ->all();

        return seo_schema([
            '@type' => 'Organization',
            'name' => $name,
            'url' => config('app.url'),
            'logo' => config('seo.organization.logo'),
            'sameAs' => $sameAs,
        ]);
    }
}

if (! function_exists('seo_schema_website')) {
    /**
     * WebSite schema for the application root.
     */
    function seo_schema_website(): HtmlString
    {
        return seo_schema([
            '@type' => 'WebSite',
            'name' => seo_title(),
            'url' => config('app.url'),
            'description' => config('seo.meta.description'),
        ]);
    }
}

if (! function_exists('seo_schema_webpage')) {
    /**
     * WebPage schema for a specific page (landing pages, static pages).
     */
    function seo_schema_webpage(?string $name = null, ?string $description = null, ?string $url = null): HtmlString
    {
        return seo_schema([
            '@type' => 'WebPage',
            'name' => $name ?? seo_title(),
            'description' => $description ?? config('seo.meta.description'),
            'url' => $url ?? request()->url(),
        ]);
    }
}

if (! function_exists('seo_schema_breadcrumb')) {
    /**
     * BreadcrumbList schema.
     *
     * @param  array<int, array{name: string, url?: string|null}>  $items  Ordered trail, e.g. [['name' => 'Home', 'url' => route('home')], ['name' => 'Blog']]
     */
    function seo_schema_breadcrumb(array $items): HtmlString
    {
        return seo_schema([
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn (array $item, int $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ])->all(),
        ]);
    }
}

if (! function_exists('seo_schema_faq')) {
    /**
     * FAQPage schema for FAQ rich results.
     *
     * @param  array<int, array{question: string, answer: string}>  $faqs
     */
    function seo_schema_faq(array $faqs): HtmlString
    {
        return seo_schema([
            '@type' => 'FAQPage',
            'mainEntity' => collect($faqs)->values()->map(fn (array $faq) => [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ])->all(),
        ]);
    }
}

if (! function_exists('seo_schema_article')) {
    /**
     * Article schema for blog posts / news.
     *
     * @param  array{headline: string, description?: string, image?: string, author?: string, published_at?: DateTimeInterface|string, updated_at?: DateTimeInterface|string, url?: string}  $data
     */
    function seo_schema_article(array $data): HtmlString
    {
        $toIso = fn ($date) => $date instanceof DateTimeInterface ? $date->format(DateTimeInterface::ATOM) : $date;

        return seo_schema([
            '@type' => 'Article',
            'headline' => $data['headline'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'author' => isset($data['author']) ? ['@type' => 'Person', 'name' => $data['author']] : null,
            'datePublished' => $toIso($data['published_at'] ?? null),
            'dateModified' => $toIso($data['updated_at'] ?? null),
            'mainEntityOfPage' => $data['url'] ?? request()->url(),
        ]);
    }
}

if (! function_exists('seo_schema_product')) {
    /**
     * Product schema with an optional offer.
     *
     * @param  array{name: string, description?: string, image?: string, sku?: string, brand?: string, price?: string|float, currency?: string, availability?: string, url?: string}  $data
     */
    function seo_schema_product(array $data): HtmlString
    {
        return seo_schema([
            '@type' => 'Product',
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'sku' => $data['sku'] ?? null,
            'brand' => isset($data['brand']) ? ['@type' => 'Brand', 'name' => $data['brand']] : null,
            'offers' => isset($data['price']) ? [
                '@type' => 'Offer',
                'price' => (string) $data['price'],
                'priceCurrency' => $data['currency'] ?? 'MYR',
                'availability' => $data['availability'] ?? 'https://schema.org/InStock',
                'url' => $data['url'] ?? request()->url(),
            ] : null,
        ]);
    }
}

if (! function_exists('seo_schema_course')) {
    /**
     * Course schema for training / course pages.
     *
     * @param  array{name: string, description?: string, provider?: string, url?: string}  $data
     */
    function seo_schema_course(array $data): HtmlString
    {
        return seo_schema([
            '@type' => 'Course',
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'provider' => [
                '@type' => 'Organization',
                'name' => $data['provider'] ?? config('seo.organization.name') ?? config('app.name'),
                'url' => config('app.url'),
            ],
            'url' => $data['url'] ?? request()->url(),
        ]);
    }
}

if (! function_exists('seo_schema_event')) {
    /**
     * Event schema for event registration pages.
     *
     * @param  array{name: string, description?: string, image?: string, starts_at: DateTimeInterface|string, ends_at?: DateTimeInterface|string, location?: string, url?: string}  $data
     */
    function seo_schema_event(array $data): HtmlString
    {
        $toIso = fn ($date) => $date instanceof DateTimeInterface ? $date->format(DateTimeInterface::ATOM) : $date;

        return seo_schema([
            '@type' => 'Event',
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'startDate' => $toIso($data['starts_at']),
            'endDate' => $toIso($data['ends_at'] ?? null),
            'location' => isset($data['location']) ? [
                '@type' => 'Place',
                'name' => $data['location'],
            ] : null,
            'url' => $data['url'] ?? request()->url(),
        ]);
    }
}
