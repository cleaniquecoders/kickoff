<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title> Detail</title></svelte:head>

<AppLayout>

<div class="mb-6 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">

                    <Badge color="green" size="lg">Created</Badge>

                    <Badge color="blue" size="lg">Updated</Badge>

                    <Badge color="red" size="lg">Deleted</Badge>

                    <Badge size="lg">{ucfirst($audit->event)}</Badge>

            <span class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                {class_basename($audit->auditable_type)}
            </span>
        </div>
        <div class="text-sm text-zinc-500 dark:text-zinc-400">
            {$audit->created_at->format('F d, Y \a\t g:i A')}
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="text-zinc-500 dark:text-zinc-400">Model ID:</span>
            <span class="ml-2 font-medium text-zinc-900 dark:text-zinc-100">{$audit->auditable_id}</span>
        </div>
        <div>
            <span class="text-zinc-500 dark:text-zinc-400">User:</span>
            <span class="ml-2 font-medium text-zinc-900 dark:text-zinc-100">
                {$audit->user?->name ?? 'System'}
            </span>
        </div>
        <div>
            <span class="text-zinc-500 dark:text-zinc-400">IP Address:</span>
            <span class="ml-2 font-medium text-zinc-900 dark:text-zinc-100">{$audit->ip_address ?? 'N/A'}</span>
        </div>
        <div>
            <span class="text-zinc-500 dark:text-zinc-400">User Agent:</span>
            <span class="ml-2 font-medium text-zinc-900 dark:text-zinc-100 truncate block max-w-xs">
                {Str::limit($audit->user_agent ?? 'N/A', 40)}
            </span>
        </div>
    </div>
</div>

    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-6 dark:border-red-700/50 dark:bg-red-900/20">
        <Heading size="lg" class="mb-4 text-red-700 dark:text-red-300">Old Values</Heading>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left py-2 pr-4 font-medium text-red-700 dark:text-red-300">Field</th>
                        <th class="text-left py-2 font-medium text-red-700 dark:text-red-300">Value</th>
                    </tr>
                </thead>
                <tbody>

                        <tr class="border-t border-red-200 dark:border-red-700/50">
                            <td class="py-2 pr-4 font-medium text-red-800 dark:text-red-200">{$key}</td>
                            <td class="py-2 text-red-700 dark:text-red-300">

                                    <pre class="text-xs">{json_encode($value, JSON_PRETTY_PRINT)}</pre>

                                    {$value ?? 'null'}

                            </td>
                        </tr>

                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-700/50 dark:bg-green-900/20">
        <Heading size="lg" class="mb-4 text-green-700 dark:text-green-300">New Values</Heading>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left py-2 pr-4 font-medium text-green-700 dark:text-green-300">Field</th>
                        <th class="text-left py-2 font-medium text-green-700 dark:text-green-300">Value</th>
                    </tr>
                </thead>
                <tbody>

                        <tr class="border-t border-green-200 dark:border-green-700/50">
                            <td class="py-2 pr-4 font-medium text-green-800 dark:text-green-200">{$key}</td>
                            <td class="py-2 text-green-700 dark:text-green-300">

                                    <pre class="text-xs">{json_encode($value, JSON_PRETTY_PRINT)}</pre>

                                    {$value ?? 'null'}

                            </td>
                        </tr>

                </tbody>
            </table>
        </div>
    </div>
</AppLayout>