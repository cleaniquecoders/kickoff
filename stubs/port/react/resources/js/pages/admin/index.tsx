import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function AdminIndex(props: Props) {


    return (
        <AppLayout>
            <Head title="AdminIndex" />
<div className="mx-auto max-w-4xl">
{/* TODO: port <x-breadcrumbs> */}
<h1 className="text-2xl font-semibold tracking-tight mb-6">
    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    {/* TODO: port @can */}
    {/* TODO: port <x-card> */}
    {/* TODO: port <x-card.header> */}
    <div className="flex items-center">
    {/* TODO: port <x-lucide-shield-check> */}
    <h2 className="text-xl font-semibold">
        </div>
        {/* TODO: port <x-card.body> */}
        <p className="text-sm text-zinc-600 dark:text-zinc-400">
        Manage user roles and permissions
        </p>
        {/* TODO: port <x-card.footer> */}
        <Button>
            Manage Roles
        </Button>
        {/* TODO: port @endcan */}
        {/* TODO: port <x-card> */}
        {/* TODO: port <x-card.header> */}
        <div className="flex items-center">
        {/* TODO: port <x-lucide-settings> */}
        <h2 className="text-xl font-semibold">
            </div>
            {/* TODO: port <x-card.body> */}
            <p className="text-sm text-zinc-600 dark:text-zinc-400">
            Configure application settings
            </p>
            {/* TODO: port <x-card.footer> */}
            <Button>
                Manage Settings
            </Button>
            </div>
            </div>
        </AppLayout>
    );
}