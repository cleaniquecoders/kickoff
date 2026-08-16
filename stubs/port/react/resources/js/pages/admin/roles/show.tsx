import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = {
    role?: any;
    selectedPermissions?: any;
};

export default function AdminRolesShow(props: Props) {
    const [role, setRole] = useState<any>(props.role);
    const [selectedPermissions, setSelectedPermissions] = useState<any>(props.selectedPermissions);
    function handleUpdatePermissions(...args: any[]) {
        router.post(route('port.updatePermissions'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }

    return (
        <AppLayout>
            <Head title="AdminRolesShow" />
<div>
{/* TODO: port <x-card> */}
{/* TODO: port <x-card.header> */}
<h2 className="text-xl font-semibold">
    {/* TODO: port <x-card.body> */}
    <dl className="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
    <div>
    <dt className="text-sm font-medium text-zinc-700 dark:text-zinc-300">Name</dt>
    <dd className="mt-1 text-sm text-zinc-900 dark:text-white">{role.name}</dd>
    </div>
    <div>
    <dt className="text-sm font-medium text-zinc-700 dark:text-zinc-300">Display Name</dt>
    <dd className="mt-1 text-sm text-zinc-900 dark:text-white">{role.display_name}</dd>
    </div>
    <div className="sm:col-span-2">
    <dt className="text-sm font-medium text-zinc-700 dark:text-zinc-300">Description</dt>
    <dd className="mt-1 text-sm text-zinc-900 dark:text-white">{role.description ?? 'No description'}</dd>
    </div>
    </dl>
    {/* TODO: port <x-card> */}
    {/* TODO: port <x-card.header> */}
    <h2 className="text-xl font-semibold">
        <p className="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
        Manage what this role can access and perform in the system
        </p>
        {/* TODO: port <x-card.body> */}
        <form onSubmit={handleUpdatePermissions}>
            <div className="space-y-8">
            {/* TODO: port @foreach */}
            <div>
            <h3 className="mb-4 flex items-center gap-2 text-base font-semibold text-zinc-900 dark:text-white">
            <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-100 dark:bg-brand-900/30">
            {/* TODO: port @if($module === 'Admin') */}
            {/* TODO: port <x-lucide-shield-check> */}
            {/* TODO: port @elseif */}
            {/* TODO: port <x-lucide-users> */}
            {/* TODO: port @elseif */}
            {/* TODO: port <x-lucide-shield> */}
            {/* TODO: port @elseif */}
            {/* TODO: port <x-lucide-lock> */}
            {/* TODO: port @elseif */}
            {/* TODO: port <x-lucide-user> */}
            {/* TODO: port @elseif */}
            {/* TODO: port <x-lucide-bell> */}
            {/* TODO: port @elseif */}
            {/* TODO: port <x-lucide-layout-dashboard> */}
            {/* TODO: port @else */}
            {/* TODO: port <x-lucide-box> */}
            {/* TODO: port @endif */}
            </span>
            {module}
            </h3>
            <div className="grid grid-cols-1 gap-4 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4 md:grid-cols-2 lg:grid-cols-3">
            {/* TODO: port @foreach */}
            <label
            for="permission-{permission.id}"
            className="group relative flex cursor-pointer rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 transition-all hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-sm"
            :class="selectedPermissions.includes({permission.id}) ? 'border-brand-500 dark:border-brand-600 bg-brand-50 dark:bg-brand-950/30' : ''"
            >
            <div className="flex h-full w-full items-start gap-3">
            <input
            type="checkbox"
            id="permission-{permission.id}"
            wire:model="selectedPermissions"
            value="{permission.id}"
            className="mt-0.5 h-4 w-4 rounded border-zinc-300 dark:border-zinc-600 text-brand-600 focus:ring-brand-600 dark:bg-zinc-800 dark:focus:ring-brand-500"
            >
            <div className="flex-1 min-w-0">
            <span className="block text-sm font-medium text-zinc-900 dark:text-zinc-100">
            {permission.function}
            </span>
            <span className="mt-1 block text-xs text-zinc-500 dark:text-zinc-400 font-mono">
            {permission.name}
            </span>
            </div>
            </div>
            </label>
            {/* TODO: port @endforeach */}
            </div>
            </div>
            {/* TODO: port @endforeach */}
            </div>
            <div className="mt-8 flex items-center justify-between gap-4 border-t border-zinc-200 dark:border-zinc-700 pt-6">
            <div className="text-sm text-zinc-600 dark:text-zinc-400">
            <span className="font-medium">{count(selectedPermissions)}</span> permissions selected
            </div>
            <div className="flex gap-2">
            <Button variant="ghost">
                Cancel
            </Button>
            <Button>
                <span>Update Permissions</span>
                <span className="flex items-center gap-2">
                <svg className="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Saving...
                </span>
            </Button>
            </div>
            </div>
            </form>
            </div>
        </AppLayout>
    );
}