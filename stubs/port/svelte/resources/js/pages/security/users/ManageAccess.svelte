<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Manage Access</title></svelte:head>

<AppLayout>

<!--
TODO (5):
 - @can: authorization check — move to controller-provided `can` prop
 - @unless: conditional — port to template if/else with server-provided prop
 - @php: inline PHP — move to computed/derived value
 - deeply nested loops (3) — verify generated each-blocks against blade source
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<Dialog variant="flyout" bind:value={showing} class="max-w-lg">

        <div class="space-y-6" x-data="{ tab: 'roles' }">
            
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                        {$this->user->initials()}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <Heading size="lg" class="truncate">{$this->user->name}</Heading>
                    <div class="truncate text-sm text-zinc-500 dark:text-zinc-400">{$this->user->email}</div>
                </div>
                <Badge size="sm" color="{$this->user->status()->badgeColor()}">
                    {$this->user->status()->label()}
                </Badge>
            </div>

            
            <div class="border-b border-zinc-200 dark:border-zinc-700">
                <nav class="-mb-px flex gap-6">
                    <button type="button" x-on:click="tab = 'roles'"
                        :class="tab === 'roles'
                            ? 'border-accent text-accent-content'
                            : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                        class="cursor-pointer border-b-2 px-1 pb-3 text-sm font-medium">
                        {__('Roles')}
                    </button>

                        <button type="button" x-on:click="tab = 'permissions'"
                            :class="tab === 'permissions'
                                ? 'border-accent text-accent-content'
                                : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            class="cursor-pointer border-b-2 px-1 pb-3 text-sm font-medium">
                            {__('Permissions')}
                        </button>

                </nav>
            </div>

            
            <div x-show="tab === 'roles'" class="space-y-2">

                    <div onclick={toggleRole('{$role->name}')} /* TODO: wire:key — use a real each-block key */
                        class="flex cursor-pointer items-center justify-between rounded-lg border border-zinc-200 px-4 py-3 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                        <div>
                            <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                {$role->display_name ?? $role->name}
                            </div>

                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{$role->description}</div>

                        </div>

                            <div class="flex h-5 w-5 items-center justify-center rounded bg-blue-500 text-white">
                                <Check class="size-4" />
                            </div>

                            <div class="h-5 w-5 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>

                    </div>

            </div>

                <div x-show="tab === 'permissions'" x-cloak class="space-y-4">
                    <Text class="text-sm">
                        {__('Direct permissions apply on top of role permissions. Role-inherited permissions are managed on the role.')}
                    </Text>

                        <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700"
                            /* TODO: wire:key — use a real each-block key */>
                            <div class="bg-zinc-50 px-4 py-2 font-medium capitalize text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
                                {str_replace(['-', '_'], ' ', $module)}
                            </div>
                            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">

                                        $inherited = $this->inheritedPermissionIds->contains($permission->id);
                                        $direct = $this->user->permissions->contains('id', $permission->id);

                                    <div /* TODO: wire:key — use a real each-block key */

                                        class="flex items-center justify-between px-4 py-3 {$inherited ? 'opacity-60' : 'cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800'}">
                                        <div>
                                            <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                                {$permission->display_name ?? $permission->name}
                                            </div>

                                                <div class="text-xs italic text-zinc-500 dark:text-zinc-400">
                                                    {__('via :roles', ['roles' => $this->inheritedVia->get($permission->id)])}
                                                </div>

                                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {$permission->description}
                                                </div>

                                        </div>

                                            <div
                                                class="flex h-5 w-5 items-center justify-center rounded {$inherited ? 'bg-zinc-400 dark:bg-zinc-600' : 'bg-blue-500'} text-white">
                                                <Check class="size-4" />
                                            </div>

                                            <div class="h-5 w-5 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>

                                    </div>

                            </div>
                        </div>

                </div>


            <div class="flex justify-end">
                <Button variant="ghost" onclick={close} class="cursor-pointer">
                    {__('Close')}
                </Button>
            </div>
        </div>

</Dialog>
</AppLayout>