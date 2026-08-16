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
TODO (2):
 - x-icon blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<div>
    
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <Badge color="zinc">
                {__('Total')}: {$this->totalCount}
            </Badge>
            <Badge color="amber">
                {__('Unread')}: {$this->unreadCount}
            </Badge>
        </div>

            <Button variant="ghost" size="sm" onclick={markAllAsRead} icon="check">
                {__('Mark All as Read')}
            </Button>

    </div>

    
    <div class="mb-6 flex gap-2">
        <Button
            size="sm"
            variant={$filter === 'all' ? 'primary' : 'ghost'}
            onclick={setFilter('all')}>
            {__('All')}
        </Button>
        <Button
            size="sm"
            variant={$filter === 'unread' ? 'primary' : 'ghost'}
            onclick={setFilter('unread')}>
            {__('Unread')}
        </Button>
        <Button
            size="sm"
            variant={$filter === 'read' ? 'primary' : 'ghost'}
            onclick={setFilter('read')}>
            {__('Read')}
        </Button>
    </div>

    
    <div class="mb-4 flex justify-end">
        {$notifications->links()}
    </div>

    
    <div class="flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden outline-1 -outline-offset-1 outline-zinc-200 dark:outline-zinc-700 sm:rounded-lg">
                    <table class="relative min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 sm:pl-6">
                                    {__('Status')}
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {__('Notification')}
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 cursor-pointer hover:text-zinc-600 dark:hover:text-zinc-300"
                                    onclick={setSorting('created_at')}>
                                    <div class="flex items-center gap-1">
                                        {__('Date')}

                                            <!-- TODO: x-icon -->

                                    </div>
                                </th>
                                <th scope="col" class="py-3.5 pr-4 pl-3 sm:pr-6">
                                    <span class="sr-only">{__('Actions')}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">

                                <tr class="{is_null($notification->read_at) ? 'bg-blue-50 dark:bg-blue-900/20' : ''}">
                                    <td class="py-4 pr-3 pl-4 text-sm whitespace-nowrap sm:pl-6">

                                            <Badge color="blue" size="sm">
                                                {__('Unread')}
                                            </Badge>

                                            <Badge color="zinc" size="sm">
                                                {__('Read')}
                                            </Badge>

                                    </td>
                                    <td class="px-3 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                        <div class="font-medium text-zinc-900 dark:text-white">
                                            {data_get($notification->data, 'subject', class_basename($notification->type))}
                                        </div>

                                            <div class="mt-1 text-zinc-500 dark:text-zinc-400">
                                                {$message}
                                            </div>

                                    </td>
                                    <td class="px-3 py-4 text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                        {$notification->created_at->diffForHumans()}
                                    </td>
                                    <td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                        <div class="flex items-center justify-end gap-2">

                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="check"
                                                    onclick={markAsRead('{$notification->id}')}
                                                    title="{__('Mark as Read')}" />

                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="minus-circle"
                                                    onclick={markAsUnread('{$notification->id}')}
                                                    title="{__('Mark as Unread')}" />

                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                icon="trash"
                                                onclick={delete('{$notification->id}')}
                                                data-confirm="{__(&#039;Are you sure you want to delete this notification?&#039;)}" /* TODO: wire confirm() into click handler */
                                                title="{__('Delete')}" />
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="4" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <!-- TODO: x-icon -->
                                            <p class="text-sm text-zinc-500 dark:text-zinc-400 italic">
                                                {__('No notifications found.')}
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <div class="mt-4">
        {$notifications->links()}
    </div>
</div>
</AppLayout>