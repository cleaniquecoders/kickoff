import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Alert, Button, Input, Label, Separator } from '@/components/ui/*'; // split per shadcn convention
import { Key, Trash2 } from 'lucide-react';
import { useState } from 'react';

type Props = {
    plainTextToken?: any;
    tokenName?: any;
};

export default function SettingsMcpTokens(props: Props) {
    const [plainTextToken, setPlainTextToken] = useState<any>(props.plainTextToken);
    const [tokenName, setTokenName] = useState<any>(props.tokenName);
    function handleCreateToken(...args: any[]) {
        router.post(route('port.createToken'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleRevoke(...args: any[]) {
        router.post(route('port.revoke'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function confirmAction(fn: () => void, message: string) {
        // TODO: replace with AlertDialog per shadcn conventions
        if (window.confirm(message)) fn();
    }

    return (
        <AppLayout>
            <Head title="SettingsMcpTokens" />
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
        <div className="space-y-6">
        {/*One-time token display */}
        {/* TODO: port @if($plainTextToken) */}
        <Alert>
            <AlertTitle>
                <AlertDescription>
                    This is the only time the token is shown.
                    <div className="grid gap-2">
                        <Input id="field" type="text" className="mt-2" />
                    </div>
                    <p className="mt-3 text-sm">Add it to Claude:</p>
                    <div className="grid gap-2">
                        <Input id="field" type="text" className="mt-1" />
                    </div>
                </AlertDescription>
            </Alert>
            {/* TODO: port @endif */}
            {/*Generate a new token */}
            {/* TODO: port <x-card> */}
            <div className="p-6">
            <h2 className="text-xl font-semibold">
                <p className="text-sm text-muted-foreground mt-1">
                    <form onSubmit={handleCreateToken}>
                        <div className="flex-1">
                        <div className="grid gap-2">
                            <Input id="tokenName" type="text" value={tokenName} onChange={(e) => setTokenName(e.target.value)} />
                        </div>
                        </div>
                        <Button className=""><Key className="size-4" />
                            Generate Token
                        </Button>
                        </form>
                        </div>
                        {/*Active tokens */}
                        {/* TODO: port <x-card> */}
                        <div className="p-6">
                        <h2 className="text-xl font-semibold">
                            <p className="text-sm text-muted-foreground mt-1">
                                <div className="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                                {/* TODO: port @forelse */}
                                <div className="flex items-center justify-between gap-4 py-3">
                                <div className="min-w-0">
                                <p className="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{token.name}</p>
                                <p className="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                {__('Created :when', .when' => token.created_at.diffForHumans()])}
                                · {__('Last used :when', .when' => token.last_used_at?.diffForHumans() ?? __('never')])}
                                </p>
                                </div>
                                <Button variant="destructive" onClick={() => confirmAction(() => handleRevoke('{{ $token->id }}'), '{{ __(\'Revoke this token? Clients using it will lose access.\') }}')} className=""><Trash2 className="size-4" />
                                    Revoke
                                </Button>
                                </div>
                                {/* TODO: port @empty */}
                                <div className="flex flex-col items-center justify-center gap-2 py-10 text-center">
                                <Key className="size-4" />
                                <p className="text-sm text-muted-foreground text-zinc-500 dark:text-zinc-400">
                                    No tokens yet. Generate one above to connect an AI client.
                                </p>
                                </div>
                                {/* TODO: port @endforelse */}
                                </div>
                                </div>
                                {/*OAuth connected apps */}
                                {/* TODO: port <x-card> */}
                                <div className="p-6">
                                <h2 className="text-xl font-semibold">
                                    <p className="text-sm text-muted-foreground mt-1">
                                        AI clients connected via OAuth (e.g. Claude on claude.ai). They authorize through your login — no token paste needed.
                                    </p>
                                    <div className="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                                    {/* TODO: port @forelse */}
                                    <div className="flex items-center justify-between gap-4 py-3">
                                    <div className="min-w-0">
                                    <p className="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{app.client.name ?? __('Unknown')}</p>
                                    <p className="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                    {__('Connected :when', .when' => app.created_at.diffForHumans()])}
                                    </p>
                                    </div>
                                    </div>
                                    {/* TODO: port @empty */}
                                    <p className="text-sm text-muted-foreground block py-4 text-zinc-500 dark:text-zinc-400">
                                        No connected apps. In Claude, add a custom connector pointing to the MCP endpoint and authorize it.
                                    </p>
                                    {/* TODO: port @endforelse */}
                                    </div>
                                    </div>
                                    </div>
                                    </div>
        </AppLayout>
    );
}