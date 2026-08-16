import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Checkbox, Label, Separator } from '@/components/ui/*'; // split per shadcn convention
import { Check } from 'lucide-react';
import { useState } from 'react';

type Props = {
    publicRegistrationEnabled?: any;
};

export default function AdminSettingsAuthentication(props: Props) {
    const [publicRegistrationEnabled, setPublicRegistrationEnabled] = useState<any>(props.publicRegistrationEnabled);
    function handleSave(...args: any[]) {
        router.post(route('port.save'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }

    return (
        <AppLayout>
            <Head title="AdminSettingsAuthentication" />
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
            {/* TODO: port <x-lucide-user-plus> */}
            <h2 className="text-xl font-semibold">
                </div>
                {/* TODO: port <x-card.body> */}
                <div className="grid gap-2">
                    <Checkbox checked={publicRegistrationEnabled} onCheckedChange={setPublicRegistrationEnabled} />
                    <Label>
                    </div>
                    <p className="text-xs text-zinc-500 dark:text-zinc-400">
                    When enabled, anyone can create an account from the public sign-up page.
                    When disabled, the registration page is unavailable and only administrators
                    can add users.
                    </p>
                    {/* TODO: port <x-card.footer> */}
                    <Button className=""><Check className="size-4" />
                        Save changes
                    </Button>
                    </form>
                    </div>
        </AppLayout>
    );
}