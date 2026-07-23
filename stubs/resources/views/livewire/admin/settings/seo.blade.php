<div>
    {{-- Full-page Livewire component: pin the route so the trail survives wire updates
         (request()->url() would be /livewire/update on re-render). --}}
    <x-breadcrumbs class="mb-6" for="admin.settings.seo" />

    <div class="flex items-end justify-between">
        <div>
            <flux:heading size="xl" level="1">SEO &amp; Analytics</flux:heading>
            <flux:text class="mt-2">Search engine defaults, structured data, crawl rules, and analytics tracking.</flux:text>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-6" />

    <form wire:submit="save" class="max-w-2xl space-y-6">
        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-search class="h-6 w-6 text-brand-500 me-3" />
                    <flux:heading size="lg">Meta &amp; Social</flux:heading>
                </div>
            </x-card.header>
            <x-card.body class="space-y-6">
                <div>
                    <flux:input wire:model="metaTitle" :label="__('Meta Title')" :placeholder="config('app.name')" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Default page title for search results and social shares. Falls back to the site name.
                    </p>
                </div>

                <div>
                    <flux:textarea wire:model="metaDescription" :label="__('Meta Description')" rows="3" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        The snippet shown under your result in Google. Aim for 150–160 characters.
                    </p>
                </div>

                <div>
                    <flux:input wire:model="metaKeywords" :label="__('Meta Keywords')" placeholder="laravel, saas, malaysia" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Comma-separated. Ignored by Google — optional, kept for other engines.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <flux:input wire:model="ogImage" type="url" :label="__('Share Image URL')" placeholder="https://example.com/og.png" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Open Graph / X card image (1200×630 recommended).
                        </p>
                    </div>
                    <div>
                        <flux:input wire:model="twitterSite" :label="__('X / Twitter Handle')" placeholder="@yourhandle" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Used for the <code>twitter:site</code> card attribution.
                        </p>
                    </div>
                </div>

                <flux:field variant="inline">
                    <flux:switch wire:model="canonicalEnabled" />
                    <flux:label>{{ __('Emit canonical URLs') }}</flux:label>
                </flux:field>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Adds <code>&lt;link rel="canonical"&gt;</code> for the current URL on every page to avoid duplicate-content penalties.
                </p>
            </x-card.body>
        </x-card>

        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-building-2 class="h-6 w-6 text-brand-500 me-3" />
                    <flux:heading size="lg">Structured Data</flux:heading>
                </div>
            </x-card.header>
            <x-card.body class="space-y-6">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Rendered as a JSON-LD <code>Organization</code> schema on every page once a name is set.
                    Page-level schemas (FAQ, Article, Product, Course, Event) use the <code>seo_schema_*()</code> helpers — see the SEO guide in <code>docs/</code>.
                </p>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <flux:input wire:model="organizationName" :label="__('Organization Name')" placeholder="Acme Sdn Bhd" />
                    <flux:input wire:model="organizationLogo" type="url" :label="__('Organization Logo URL')" placeholder="https://example.com/logo.png" />
                </div>

                <div>
                    <flux:textarea wire:model="organizationSameAs" :label="__('Social Profile URLs')" rows="3" placeholder="https://www.facebook.com/acme&#10;https://www.linkedin.com/company/acme" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        One URL per line — company social/profile pages for the schema's <code>sameAs</code>.
                    </p>
                </div>
            </x-card.body>
        </x-card>

        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-chart-line class="h-6 w-6 text-brand-500 me-3" />
                    <flux:heading size="lg">Analytics</flux:heading>
                </div>
            </x-card.header>
            <x-card.body class="space-y-6">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Tracking snippets render only when an ID is set. Leave blank on local/staging to keep dev traffic out of your property.
                </p>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <flux:input wire:model="googleAnalyticsId" :label="__('Google Analytics (GA4)')" placeholder="G-XXXXXXXXXX" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Measurement ID from GA4 → Admin → Data Streams.
                        </p>
                    </div>
                    <div>
                        <flux:input wire:model="googleTagManagerId" :label="__('Google Tag Manager')" placeholder="GTM-XXXXXXX" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Container ID. Use GTM <em>or</em> GA4 directly — not both, or events double-fire.
                        </p>
                    </div>
                </div>

                <div>
                    <flux:input wire:model="googleSiteVerification" :label="__('Search Console Verification')" placeholder="abc123..." />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        The <code>content</code> value of the Google Search Console HTML-tag verification.
                    </p>
                </div>
            </x-card.body>
        </x-card>

        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-bot class="h-6 w-6 text-brand-500 me-3" />
                    <flux:heading size="lg">Crawling</flux:heading>
                </div>
            </x-card.header>
            <x-card.body class="space-y-6">
                <div>
                    <flux:textarea wire:model="robotsTxt" :label="__('robots.txt')" rows="6" class="font-mono" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Served at <a href="{{ route('seo.robots') }}" target="_blank" rel="noopener noreferrer" class="font-medium text-brand-500 hover:underline">/robots.txt</a>.
                        The <code>Sitemap: {{ route('seo.sitemap') }}</code> line is appended automatically.
                    </p>
                </div>

                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3 text-xs text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-400">
                    <a href="{{ route('seo.sitemap') }}" target="_blank" rel="noopener noreferrer" class="font-medium text-brand-500 hover:underline">/sitemap.xml</a>
                    lists the public routes automatically. For content-heavy sites, schedule
                    <code>php artisan seo:generate-sitemap</code> to crawl the full site into <code>public/sitemap.xml</code>.
                </div>
            </x-card.body>
            <x-card.footer>
                <flux:button type="submit" variant="primary" icon="check" class="cursor-pointer">
                    Save changes
                </flux:button>
            </x-card.footer>
        </x-card>
    </form>
</div>
