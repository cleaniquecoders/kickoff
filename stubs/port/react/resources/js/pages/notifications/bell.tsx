import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge, Button, DropdownMenu, DropdownMenuItem, Separator } from '@/components/ui/*'; // split per shadcn convention
import { Bell, ChevronRight } from 'lucide-react';
import { useState } from 'react';

type Props = Record<string, never>;

export default function NotificationsBell(props: Props) {

    function handleMarkAsRead(...args: any[]) {
        router.post(route('port.markAsRead'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }

    return (
        <AppLayout>
            <Head title="NotificationsBell" />
<div>
<DropdownMenu>
    <Button variant="ghost" className="relative"><Bell className="size-4" />
        {/* TODO: port @if($this->unreadCount > 0) */}
        <span className="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-medium text-white">
        {unreadCount > 99 ? '99+' : unreadCount}
        </span>
        {/* TODO: port @endif */}
    </Button>
    <DropdownMenuContent>
        <DropdownMenuLabel>
            <span>Notifications</span>
            {/* TODO: port @if($this->unreadCount > 0) */}
            <Badge variant="outline" data-color="amber">
                {unreadCount} unread
            </Badge>
            {/* TODO: port @endif */}
        </DropdownMenuLabel>
        <Separator /* DropdownMenuSeparator */ />
        {/* TODO: port @forelse */}
        <DropdownMenuItem onClick={() => handleMarkAsRead('{{ $notification->id }}')}>
            <div className="flex items-center gap-2">
            <span className="h-2 w-2 rounded-full bg-blue-500"></span>
            <span className="font-medium text-zinc-900 dark:text-white">
            {data_get(notification.data, 'subject', class_basename(notification.type))}
            </span>
            </div>
            {/* TODO: port @if($message = data_get($notification->data, 'message')) */}
            <span className="ml-4 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">
            {message}
            </span>
            {/* TODO: port @endif */}
            <span className="ml-4 text-xs text-zinc-400 dark:text-zinc-500">
            {notification.created_at.diffForHumans()}
            </span>
        </DropdownMenuItem>
        {/* TODO: port @empty */}
        <div className="py-6 text-center">
        {/* TODO: port <x-icon> */}
        <p className="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
        No unread notifications
        </p>
        </div>
        {/* TODO: port @endforelse */}
        <Separator /* DropdownMenuSeparator */ />
        <DropdownMenuItem asChild>
            {/* TODO: port route() in menu.item href */}
            <Link href="#"><ChevronRight className="mr-2 size-4" />
                View All Notifications
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
    </div>
        </AppLayout>
    );
}