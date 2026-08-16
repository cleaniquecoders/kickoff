import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge, Button } from '@/components/ui/*'; // split per shadcn convention
import { Check, MinusCircle, Trash } from 'lucide-react';
import { useState } from 'react';

type Props = {
    filter?: any;
    sortBy?: any;
    sortDirection?: any;
};

export default function NotificationsIndex(props: Props) {
    const [filter, setFilter] = useState<any>(props.filter);
    const [sortBy, setSortBy] = useState<any>(props.sortBy);
    const [sortDirection, setSortDirection] = useState<any>(props.sortDirection);
    function handleDelete(...args: any[]) {
        router.post(route('port.delete'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleMarkAllAsRead(...args: any[]) {
        router.post(route('port.markAllAsRead'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleMarkAsRead(...args: any[]) {
        router.post(route('port.markAsRead'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleMarkAsUnread(...args: any[]) {
        router.post(route('port.markAsUnread'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleSetFilter(...args: any[]) {
        router.post(route('port.setFilter'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleSetSorting(...args: any[]) {
        router.post(route('port.setSorting'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function confirmAction(fn: () => void, message: string) {
        // TODO: replace with AlertDialog per shadcn conventions
        if (window.confirm(message)) fn();
    }

    return (
        <AppLayout>
            <Head title="NotificationsIndex" />
<div>
{/*Stats and Actions */}
<div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
<div className="flex items-center gap-4">
<Badge variant="outline" data-color="zinc">
    Total: {totalCount}
</Badge>
<Badge variant="outline" data-color="amber">
    Unread: {unreadCount}
</Badge>
</div>
{/* TODO: port @if($this->unreadCount > 0) */}
<Button variant="ghost" onClick={() => handleMarkAllAsRead()}><Check className="size-4" />
    Mark All as Read
</Button>
{/* TODO: port @endif */}
</div>
{/*Filter Buttons */}
<div className="mb-6 flex gap-2">
<Button onClick={() => handleSetFilter('all')}>
    All
</Button>
<Button onClick={() => handleSetFilter('unread')}>
    Unread
</Button>
<Button onClick={() => handleSetFilter('read')}>
    Read
</Button>
</div>
{/*Pagination Top */}
<div className="mb-4 flex justify-end">
{notifications.links()}
</div>
{/*Notifications Table */}
<div className="flow-root">
<div className="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
<div className="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
<div className="overflow-hidden outline-1 -outline-offset-1 outline-zinc-200 dark:outline-zinc-700 sm:rounded-lg">
<table className="relative min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
<thead className="bg-zinc-50 dark:bg-zinc-800">
<tr>
<th scope="col"
className="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 sm:pl-6">
Status
</th>
<th scope="col"
className="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">
Notification
</th>
<th scope="col"
className="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 cursor-pointer hover:text-zinc-600 dark:hover:text-zinc-300"
wire:click="setSorting('created_at')">
<div className="flex items-center gap-1">
Date
{/* TODO: port @if($sortBy === 'created_at') */}
{/* TODO: port <x-icon> */}
{/* TODO: port @endif */}
</div>
</th>
<th scope="col" className="py-3.5 pr-4 pl-3 sm:pr-6">
<span className="sr-only">Actions</span>
</th>
</tr>
</thead>
<tbody className="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
{/* TODO: port @forelse */}
<tr className="{is_null(notification.read_at) ? 'bg-blue-50 dark:bg-blue-900/20' : ''}">
<td className="py-4 pr-3 pl-4 text-sm whitespace-nowrap sm:pl-6">
{/* TODO: port @if(is_null($notification->read_at)) */}
<Badge variant="outline" data-color="blue">
    Unread
</Badge>
{/* TODO: port @else */}
<Badge variant="outline" data-color="zinc">
    Read
</Badge>
{/* TODO: port @endif */}
</td>
<td className="px-3 py-4 text-sm text-zinc-700 dark:text-zinc-300">
<div className="font-medium text-zinc-900 dark:text-white">
{data_get(notification.data, 'subject', class_basename(notification.type))}
</div>
{/* TODO: port @if($message = data_get($notification->data, 'message')) */}
<div className="mt-1 text-zinc-500 dark:text-zinc-400">
{message}
</div>
{/* TODO: port @endif */}
</td>
<td className="px-3 py-4 text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
{notification.created_at.diffForHumans()}
</td>
<td className="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
<div className="flex items-center justify-end gap-2">
{/* TODO: port @if(is_null($notification->read_at)) */}
<Button variant="ghost" onClick={() => handleMarkAsRead('{{ $notification->id }}')}><Check className="size-4" />
    {/* TODO: port @else */}
    <Button variant="ghost" onClick={() => handleMarkAsUnread('{{ $notification->id }}')}><MinusCircle className="size-4" />
        {/* TODO: port @endif */}
        <Button variant="ghost" onClick={() => confirmAction(() => handleDelete('{{ $notification->id }}'), '{{ __(\'Are you sure you want to delete this notification?\') }}')}><Trash className="size-4" />
            </div>
            </td>
            </tr>
            {/* TODO: port @empty */}
            <tr>
            <td colspan="4" className="py-12 text-center">
            <div className="flex flex-col items-center justify-center gap-3">
            {/* TODO: port <x-icon> */}
            <p className="text-sm text-zinc-500 dark:text-zinc-400 italic">
            No notifications found.
            </p>
            </div>
            </td>
            </tr>
            {/* TODO: port @endforelse */}
            </tbody>
            </table>
            </div>
            </div>
            </div>
            </div>
            {/*Pagination Bottom */}
            <div className="mt-4">
            {notifications.links()}
            </div>
            </div>
        </AppLayout>
    );
}