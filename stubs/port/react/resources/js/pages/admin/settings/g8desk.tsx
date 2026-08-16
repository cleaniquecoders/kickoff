import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Input, Label, Separator, Switch } from '@/components/ui/*'; // split per shadcn convention
import { Check } from 'lucide-react';
import { useState } from 'react';

type Props = Record<string, never>;

export default function AdminSettingsG8desk(props: Props) {


    return (
        <AppLayout>
            <Head title="AdminSettingsG8desk" />
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
            {/* TODO: port <x-lucide-life-buoy> */}
            <h2 className="text-xl font-semibold">
                </div>
                {/* TODO: port <x-card.body> */}
                <div className="grid gap-2">
                    <Switch checked={enabled} onCheckedChange={setEnabled} />
                    <Label>
                    </div>
                    <p className="text-xs text-zinc-500 dark:text-zinc-400">
                    No SDK, no npm package — when enabled, the widget loads directly from g8desk
                    for authenticated users on every app page (~5 lines of markup).
                    </p>
                    <div className="rounded-lg border border-zinc-200 bg-zinc-50 p-3 text-xs text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-400">
                    Need keys? In g8desk, open
                    <a href="{rtrim(baseUrl ?: 'https://g8desk.com', '/')}/ops/organizations" target="_blank" rel="noopener noreferrer" className="font-medium text-brand-500 hover:underline">Ops → your organisation → Intake Channels</a>,
                    create a channel, then <strong>Widget → Generate embed</strong> to reveal the public key and secret to paste below.
                    </div>
                    <div>
                    <div className="grid gap-2">
                        <Label htmlFor="baseUrl">Base URL</Label>
                        <Input id="baseUrl" type="url" value={baseUrl} onChange={(e) => setBaseUrl(e.target.value)} />
                    </div>
                    <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    The g8desk base URL. The widget script loads from <code>{base_url}/intake/widget.js</code>.
                    </p>
                    </div>
                    <div>
                    <div className="grid gap-2">
                        <Label htmlFor="publicKey">Public Key</Label>
                        <Input id="publicKey" type="text" value={publicKey} onChange={(e) => setPublicKey(e.target.value)} />
                    </div>
                    <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    The pk_… data-key from g8desk → channel → Widget → Generate embed.
                    </p>
                    </div>
                    <div>
                    <div className="grid gap-2">
                        <Label htmlFor="widgetSecret">Widget Secret</Label>
                        <Input id="widgetSecret" type="password" value={widgetSecret} onChange={(e) => setWidgetSecret(e.target.value)} />
                    </div>
                    <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    The g8wi_… identity secret; stored encrypted, never sent to the browser.
                    </p>
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