<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Index</title></svelte:head>

<AppLayout>

<!--
TODO (7):
 - @can: authorization check — move to controller-provided `can` prop
 - @unless: conditional — port to template if/else with server-provided prop
 - x-empty-state blade component — port to kit equivalent or drop
 - x-flyout blade component — port to kit equivalent or drop
 - Alpine $dispatch/$wire usage — port to Inertia router visit or emitted component event
 - verify blade echos against server-provided Inertia props (routes, translations)
 - livewire:admin.roles.show nested component — extract to child page component
-->

<div>
    <div class="flex justify-end mb-4">
        {$roles->links()}
    </div>

    <div class="mt-4 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden outline-1 -outline-offset-1 outline-zinc-200 dark:outline-zinc-700 sm:rounded-lg">
                    <table class="relative min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 sm:pl-6">
                                    {__('Role')}
                                </th>
                                <th scope="col"
                                    class="hidden px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 md:table-cell dark:text-zinc-100">
                                    {__('Description')}
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {__('Status')}
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {__('Users')}
                                </th>
                                <th scope="col" class="py-3.5 pr-4 pl-3 sm:pr-6">
                                    <span class="sr-only">{__('Actions')}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">

                                <tr /* TODO: wire:key — use a real each-block key */
                                    onclick={openDetail('{$role->uuid}')}
                                    class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                    <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-zinc-900 dark:text-white sm:pl-6">
                                        {$role->display_name}

                                            <Badge size="sm" color="zinc" class="ml-1">{__('Protected')}</Badge>

                                    </td>
                                    <td class="hidden px-3 py-4 text-sm text-zinc-700 md:table-cell dark:text-zinc-300">
                                        {$role->description ?? '-'}
                                    </td>
                                    <td class="px-3 py-4 text-sm">
                                        <Badge size="sm" color="{$role->is_enabled ? 'green' : 'zinc'}">
                                            {$role->is_enabled ? __('Enabled') : __('Disabled')}
                                        </Badge>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                        {$role->users_count}
                                    </td>
                                    <td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6"
                                        @click.stop>
                                        <DropdownMenu>
                                            <Button variant="ghost" size="sm" icon="ellipsis" class="cursor-pointer" />
                                            <DropdownMenuContent>
                                                <DropdownMenuItem icon="shield-check"
                                                    onclick={openDetail('{$role->uuid}')}>
                                                    {__('Manage Permissions')}
                                                </DropdownMenuItem>

                                                    <DropdownMenuItem icon="pencil"
                                                        /* TODO: alpine handler */>
                                                        {__('Edit')}
                                                    </DropdownMenuItem>

                                                        <DropdownMenuItem icon="{$role->is_enabled ? 'x' : 'check'}"
                                                            onclick={toggleEnabled('{$role->uuid}')}>
                                                            {$role->is_enabled ? __('Disable') : __('Enable')}
                                                        </DropdownMenuItem>

                                                        <DropdownMenuItem icon="trash-2" variant="danger"
                                                            onclick={delete('{$role->uuid}')}
                                                            data-confirm="{__(&#039;Are you sure you want to delete :role?&#039;, [&#039;role&#039; =&gt; $role-&gt;display_name])}" /* TODO: wire confirm() into click handler */>
                                                            {__('Delete')}
                                                        </DropdownMenuItem>

                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <!-- TODO: x-empty-state -->
                                    </td>
                                </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {$roles->links()}
    </div>

    
    <!-- TODO: x-flyout -->

            <!-- TODO: livewire:admin.roles.show -->

    
</div>
</AppLayout>