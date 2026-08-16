<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Seo" />

<!--
TODO (10):
 - x-breadcrumbs blade component — port to kit equivalent or drop
 - x-card blade component — port to kit equivalent or drop
 - x-card.header blade component — port to kit equivalent or drop
 - x-lucide-search blade component — port to kit equivalent or drop
 - x-card.body blade component — port to kit equivalent or drop
 - x-lucide-building-2 blade component — port to kit equivalent or drop
 - x-lucide-chart-line blade component — port to kit equivalent or drop
 - x-lucide-bot blade component — port to kit equivalent or drop
 - x-card.footer blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<div>
    
    <!-- TODO: x-breadcrumbs -->

    <div class="flex items-end justify-between">
        <div>
            <Heading size="xl" level="1">SEO &amp; Analytics</Heading>
            <Text class="mt-2">Search engine defaults, structured data, crawl rules, and analytics tracking.</Text>
        </div>
    </div>

    <Separator variant="subtle" class="my-6" />

    <form @submit.prevent="save" class="max-w-2xl space-y-6">
        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-search -->
                    <Heading size="lg">Meta &amp; Social</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <div>
                    <Input v-model="metaTitle" :label="__('Meta Title')" :placeholder="config('app.name')" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Default page title for search results and social shares. Falls back to the site name.
                    </p>
                </div>

                <div>
                    <Textarea v-model="metaDescription" :label="__('Meta Description')" rows="3" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        The snippet shown under your result in Google. Aim for 150–160 characters.
                    </p>
                </div>

                <div>
                    <Input v-model="metaKeywords" :label="__('Meta Keywords')" placeholder="laravel, saas, malaysia" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Comma-separated. Ignored by Google — optional, kept for other engines.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <Input v-model="ogImage" type="url" :label="__('Share Image URL')" placeholder="https://example.com/og.png" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Open Graph / X card image (1200×630 recommended).
                        </p>
                    </div>
                    <div>
                        <Input v-model="twitterSite" :label="__('X / Twitter Handle')" placeholder="@yourhandle" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Used for the <code>twitter:site</code> card attribution.
                        </p>
                    </div>
                </div>

                <Field variant="inline">
                    <Switch v-model="canonicalEnabled" />
                    <Label>{{ __('Emit canonical URLs') }}</Label>
                </Field>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Adds <code>&lt;link rel="canonical"&gt;</code> for the current URL on every page to avoid duplicate-content penalties.
                </p>
            
        

        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-building-2 -->
                    <Heading size="lg">Structured Data</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Rendered as a JSON-LD <code>Organization</code> schema on every page once a name is set.
                    Page-level schemas (FAQ, Article, Product, Course, Event) use the <code>seo_schema_*()</code> helpers — see the SEO guide in <code>docs/</code>.
                </p>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <Input v-model="organizationName" :label="__('Organization Name')" placeholder="Acme Sdn Bhd" />
                    <Input v-model="organizationLogo" type="url" :label="__('Organization Logo URL')" placeholder="https://example.com/logo.png" />
                </div>

                <div>
                    <Textarea v-model="organizationSameAs" :label="__('Social Profile URLs')" rows="3" placeholder="https://www.facebook.com/acme&#10;https://www.linkedin.com/company/acme" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        One URL per line — company social/profile pages for the schema's <code>sameAs</code>.
                    </p>
                </div>
            
        

        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-chart-line -->
                    <Heading size="lg">Analytics</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Tracking snippets render only when an ID is set. Leave blank on local/staging to keep dev traffic out of your property.
                </p>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <Input v-model="googleAnalyticsId" :label="__('Google Analytics (GA4)')" placeholder="G-XXXXXXXXXX" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Measurement ID from GA4 → Admin → Data Streams.
                        </p>
                    </div>
                    <div>
                        <Input v-model="googleTagManagerId" :label="__('Google Tag Manager')" placeholder="GTM-XXXXXXX" />
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Container ID. Use GTM <em>or</em> GA4 directly — not both, or events double-fire.
                        </p>
                    </div>
                </div>

                <div>
                    <Input v-model="googleSiteVerification" :label="__('Search Console Verification')" placeholder="abc123..." />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        The <code>content</code> value of the Google Search Console HTML-tag verification.
                    </p>
                </div>
            
        

        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-bot -->
                    <Heading size="lg">Crawling</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <div>
                    <Textarea v-model="robotsTxt" :label="__('robots.txt')" rows="6" class="font-mono" />
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
            
            <!-- TODO: x-card.footer -->
                <Button type="submit" variant="primary" icon="check" class="cursor-pointer">
                    Save changes
                </Button>
            
        
    </form>
</div>
</template>