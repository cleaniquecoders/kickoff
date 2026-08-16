<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Show</title></svelte:head>

<AppLayout>

<!--
TODO (12):
 - x-card blade component — port to kit equivalent or drop
 - x-card.header blade component — port to kit equivalent or drop
 - x-card.body blade component — port to kit equivalent or drop
 - x-lucide-shield-check blade component — port to kit equivalent or drop
 - x-lucide-users blade component — port to kit equivalent or drop
 - x-lucide-shield blade component — port to kit equivalent or drop
 - x-lucide-lock blade component — port to kit equivalent or drop
 - x-lucide-user blade component — port to kit equivalent or drop
 - x-lucide-bell blade component — port to kit equivalent or drop
 - x-lucide-layout-dashboard blade component — port to kit equivalent or drop
 - x-lucide-box blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<div>
    <!-- TODO: x-card -->
        <!-- TODO: x-card.header -->
            <Heading size="lg">Role Information</Heading>
        
        <!-- TODO: x-card.body -->
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Name</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{$role->name}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Display Name</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{$role->display_name}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Description</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{$role->description ?? 'No description'}</dd>
                </div>
            </dl>
        
    

    <!-- TODO: x-card -->
        <!-- TODO: x-card.header -->
            <Heading size="lg">Permissions</Heading>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                Manage what this role can access and perform in the system
            </p>
        
        <!-- TODO: x-card.body -->
            <form onsubmit={(e) => { e.preventDefault(); updatePermissions; }}>
                <div class="space-y-8">

                        <div>
                            <h3 class="mb-4 flex items-center gap-2 text-base font-semibold text-zinc-900 dark:text-white">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-100 dark:bg-brand-900/30">

                                        <!-- TODO: x-lucide-shield-check -->

                                        <!-- TODO: x-lucide-users -->

                                        <!-- TODO: x-lucide-shield -->

                                        <!-- TODO: x-lucide-lock -->

                                        <!-- TODO: x-lucide-user -->

                                        <!-- TODO: x-lucide-bell -->

                                        <!-- TODO: x-lucide-layout-dashboard -->

                                        <!-- TODO: x-lucide-box -->

                                </span>
                                {$module}
                            </h3>

                            <div class="grid grid-cols-1 gap-4 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4 md:grid-cols-2 lg:grid-cols-3">

                                    <label
                                        for="permission-{$permission->id}"
                                        class="group relative flex cursor-pointer rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 transition-all hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-sm"
                                        :class="selectedPermissions.includes({$permission->id}) ? 'border-brand-500 dark:border-brand-600 bg-brand-50 dark:bg-brand-950/30' : ''"
                                    >
                                        <div class="flex h-full w-full items-start gap-3">
                                            <input
                                                type="checkbox"
                                                id="permission-{$permission->id}"
                                                bind:value={selectedPermissions}
                                                value="{$permission->id}"
                                                class="mt-0.5 h-4 w-4 rounded border-zinc-300 dark:border-zinc-600 text-brand-600 focus:ring-brand-600 dark:bg-zinc-800 dark:focus:ring-brand-500"
                                            >
                                            <div class="flex-1 min-w-0">
                                                <span class="block text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {$permission->function}
                                                </span>
                                                <span class="mt-1 block text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                                    {$permission->name}
                                                </span>
                                            </div>
                                        </div>
                                    </label>

                            </div>
                        </div>

                </div>

                <div class="mt-8 flex items-center justify-between gap-4 border-t border-zinc-200 dark:border-zinc-700 pt-6">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        <span class="font-medium">{count($selectedPermissions)}</span> permissions selected
                    </div>
                    <div class="flex gap-2">
                        <Button variant="ghost" href={route('admin.roles.index')}>
                            Cancel
                        </Button>
                        <Button type="submit" variant="primary" /* TODO: wire:loading.attr — use form.processing / inertia progress */>
                            <span /* TODO: wire:loading.remove — use form.processing / inertia progress */>Update Permissions</span>
                            <span /* TODO: wire:loading — use form.processing / inertia progress */ class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </Button>
                    </div>
                </div>
            </form>
        
    
</div>
</AppLayout>