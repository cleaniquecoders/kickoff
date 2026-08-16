import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Input, Label, Separator, Switch, Textarea } from '@/components/ui/*'; // split per shadcn convention
import { Check } from 'lucide-react';
import { useState } from 'react';

type Props = {
    canonicalEnabled?: any;
    googleAnalyticsId?: any;
    googleSiteVerification?: any;
    googleTagManagerId?: any;
    metaDescription?: any;
    metaKeywords?: any;
    metaTitle?: any;
    ogImage?: any;
    organizationLogo?: any;
    organizationName?: any;
    organizationSameAs?: any;
    robotsTxt?: any;
    twitterSite?: any;
};

export default function AdminSettingsSeo(props: Props) {
    const [canonicalEnabled, setCanonicalEnabled] = useState<any>(props.canonicalEnabled);
    const [googleAnalyticsId, setGoogleAnalyticsId] = useState<any>(props.googleAnalyticsId);
    const [googleSiteVerification, setGoogleSiteVerification] = useState<any>(props.googleSiteVerification);
    const [googleTagManagerId, setGoogleTagManagerId] = useState<any>(props.googleTagManagerId);
    const [metaDescription, setMetaDescription] = useState<any>(props.metaDescription);
    const [metaKeywords, setMetaKeywords] = useState<any>(props.metaKeywords);
    const [metaTitle, setMetaTitle] = useState<any>(props.metaTitle);
    const [ogImage, setOgImage] = useState<any>(props.ogImage);
    const [organizationLogo, setOrganizationLogo] = useState<any>(props.organizationLogo);
    const [organizationName, setOrganizationName] = useState<any>(props.organizationName);
    const [organizationSameAs, setOrganizationSameAs] = useState<any>(props.organizationSameAs);
    const [robotsTxt, setRobotsTxt] = useState<any>(props.robotsTxt);
    const [twitterSite, setTwitterSite] = useState<any>(props.twitterSite);
    function handleSave(...args: any[]) {
        router.post(route('port.save'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }

    return (
        <AppLayout>
            <Head title="AdminSettingsSeo" />
<div>
{{-- Full-page Livewire component: pin the route so the trail survives wire updates
(request()->url() would be /livewire/update on re-render). --}}
<div className="flex items-end justify-between">
<div>
<h1 className="text-2xl font-semibold tracking-tight">
    <p className="text-sm text-muted-foreground mt-2">
        </div>
        </div>
        <Separator />
        <form onSubmit={handleSave}>
            {/* TODO: port <x-card> */}
            {/* TODO: port <x-card.header> */}
            <div className="flex items-center">
            {/* TODO: port <x-lucide-search> */}
            <h2 className="text-xl font-semibold">
                </div>
                {/* TODO: port <x-card.body> */}
                <div>
                <div className="grid gap-2">
                    <Label htmlFor="metaTitle">Meta Title</Label>
                    <Input id="metaTitle" type="text" value={metaTitle} onChange={(e) => setMetaTitle(e.target.value)} />
                </div>
                <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                Default page title for search results and social shares. Falls back to the site name.
                </p>
                </div>
                <div>
                <Textarea value={metaDescription} onChange={(e) => setMetaDescription(e.target.value)} />
                <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                The snippet shown under your result in Google. Aim for 150–160 characters.
                </p>
                </div>
                <div>
                <div className="grid gap-2">
                    <Label htmlFor="metaKeywords">Meta Keywords</Label>
                    <Input id="metaKeywords" type="text" value={metaKeywords} onChange={(e) => setMetaKeywords(e.target.value)} />
                </div>
                <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                Comma-separated. Ignored by Google — optional, kept for other engines.
                </p>
                </div>
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                <div className="grid gap-2">
                    <Label htmlFor="ogImage">Share Image URL</Label>
                    <Input id="ogImage" type="url" value={ogImage} onChange={(e) => setOgImage(e.target.value)} />
                </div>
                <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                Open Graph / X card image (1200×630 recommended).
                </p>
                </div>
                <div>
                <div className="grid gap-2">
                    <Label htmlFor="twitterSite">X / Twitter Handle</Label>
                    <Input id="twitterSite" type="text" value={twitterSite} onChange={(e) => setTwitterSite(e.target.value)} />
                </div>
                <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                Used for the <code>twitter:site</code> card attribution.
                </p>
                </div>
                </div>
                <div className="grid gap-2">
                    <Switch checked={canonicalEnabled} onCheckedChange={setCanonicalEnabled} />
                    <Label>
                    </div>
                    <p className="text-xs text-zinc-500 dark:text-zinc-400">
                    Adds <code>&lt;link rel="canonical"&gt;</code> for the current URL on every page to avoid duplicate-content penalties.
                    </p>
                    {/* TODO: port <x-card> */}
                    {/* TODO: port <x-card.header> */}
                    <div className="flex items-center">
                    {/* TODO: port <x-lucide-building-2> */}
                    <h2 className="text-xl font-semibold">
                        </div>
                        {/* TODO: port <x-card.body> */}
                        <p className="text-xs text-zinc-500 dark:text-zinc-400">
                        Rendered as a JSON-LD <code>Organization</code> schema on every page once a name is set.
                        Page-level schemas (FAQ, Article, Product, Course, Event) use the <code>seo_schema_*()</code> helpers — see the SEO guide in <code>docs/</code>.
                        </p>
                        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div className="grid gap-2">
                            <Label htmlFor="organizationName">Organization Name</Label>
                            <Input id="organizationName" type="text" value={organizationName} onChange={(e) => setOrganizationName(e.target.value)} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="organizationLogo">Organization Logo URL</Label>
                            <Input id="organizationLogo" type="url" value={organizationLogo} onChange={(e) => setOrganizationLogo(e.target.value)} />
                        </div>
                        </div>
                        <div>
                        <Textarea value={organizationSameAs} onChange={(e) => setOrganizationSameAs(e.target.value)} />
                        <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        One URL per line — company social/profile pages for the schema's <code>sameAs</code>.
                        </p>
                        </div>
                        {/* TODO: port <x-card> */}
                        {/* TODO: port <x-card.header> */}
                        <div className="flex items-center">
                        {/* TODO: port <x-lucide-chart-line> */}
                        <h2 className="text-xl font-semibold">
                            </div>
                            {/* TODO: port <x-card.body> */}
                            <p className="text-xs text-zinc-500 dark:text-zinc-400">
                            Tracking snippets render only when an ID is set. Leave blank on local/staging to keep dev traffic out of your property.
                            </p>
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                            <div className="grid gap-2">
                                <Label htmlFor="googleAnalyticsId">Google Analytics (GA4)</Label>
                                <Input id="googleAnalyticsId" type="text" value={googleAnalyticsId} onChange={(e) => setGoogleAnalyticsId(e.target.value)} />
                            </div>
                            <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Measurement ID from GA4 → Admin → Data Streams.
                            </p>
                            </div>
                            <div>
                            <div className="grid gap-2">
                                <Label htmlFor="googleTagManagerId">Google Tag Manager</Label>
                                <Input id="googleTagManagerId" type="text" value={googleTagManagerId} onChange={(e) => setGoogleTagManagerId(e.target.value)} />
                            </div>
                            <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Container ID. Use GTM <em>or</em> GA4 directly — not both, or events double-fire.
                            </p>
                            </div>
                            </div>
                            <div>
                            <div className="grid gap-2">
                                <Label htmlFor="googleSiteVerification">Search Console Verification</Label>
                                <Input id="googleSiteVerification" type="text" value={googleSiteVerification} onChange={(e) => setGoogleSiteVerification(e.target.value)} />
                            </div>
                            <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            The <code>content</code> value of the Google Search Console HTML-tag verification.
                            </p>
                            </div>
                            {/* TODO: port <x-card> */}
                            {/* TODO: port <x-card.header> */}
                            <div className="flex items-center">
                            {/* TODO: port <x-lucide-bot> */}
                            <h2 className="text-xl font-semibold">
                                </div>
                                {/* TODO: port <x-card.body> */}
                                <div>
                                <Textarea value={robotsTxt} onChange={(e) => setRobotsTxt(e.target.value)} className="font-mono" />
                                <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                Served at <a href="{/* TODO: route('seo.robots') */}" target="_blank" rel="noopener noreferrer" class="font-medium text-brand-500 hover:underline">/robots.txt</a>.
                                The <code>Sitemap: {/* TODO: route('seo.sitemap') */}</code> line is appended automatically.
                                </p>
                                </div>
                                <div className="rounded-lg border border-zinc-200 bg-zinc-50 p-3 text-xs text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-400">
                                <a href="{/* TODO: route('seo.sitemap') */}" target="_blank" rel="noopener noreferrer" className="font-medium text-brand-500 hover:underline">/sitemap.xml</a>
                                lists the public routes automatically. For content-heavy sites, schedule
                                <code>php artisan seo:generate-sitemap</code> to crawl the full site into <code>public/sitemap.xml</code>.
                                </div>
                                {/* TODO: port <x-card.footer> */}
                                <Button className=""><Check className="size-4" />
                                    Save changes
                                </Button>
                                </form>
                                </div>
        </AppLayout>
    );
}