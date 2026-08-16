import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function Dashboard(props: Props) {


    return (
        <AppLayout>
            <Head title="Dashboard" />
<div className="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
{/*Welcome Header */}
<div className="space-y-2">
<h1 className="text-3xl font-bold text-gray-900 dark:text-white">
{__('Welcome back, :name', .name' => auth().user().name])}
</h1>
<p className="text-gray-600 dark:text-gray-400">
Here's what's happening with your account today.
</p>
</div>
{/*Stats Overview */}
<div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
{/*Stat Card 1 */}
{/* TODO: port <x-card> */}
{/* TODO: port <x-card.body> */}
<div className="flex items-center justify-between">
<div>
<p className="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Users</p>
<p className="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{\App\Models\User::count()}</p>
</div>
<div className="rounded-full bg-blue-100 p-3 dark:bg-blue-900/50">
{/* TODO: port <x-lucide-users> */}
</div>
</div>
{/*Stat Card 2 */}
{/* TODO: port <x-card> */}
{/* TODO: port <x-card.body> */}
<div className="flex items-center justify-between">
<div>
<p className="text-sm font-medium text-zinc-500 dark:text-zinc-400">Active Roles</p>
<p className="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{\Spatie\Permission\Models\Role::count()}</p>
</div>
<div className="rounded-full bg-green-100 p-3 dark:bg-green-900/50">
{/* TODO: port <x-lucide-shield> */}
</div>
</div>
{/*Stat Card 3 */}
{/* TODO: port <x-card> */}
{/* TODO: port <x-card.body> */}
<div className="flex items-center justify-between">
<div>
<p className="text-sm font-medium text-zinc-500 dark:text-zinc-400">Permissions</p>
<p className="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{\Spatie\Permission\Models\Permission::count()}</p>
</div>
<div className="rounded-full bg-purple-100 p-3 dark:bg-purple-900/50">
{/* TODO: port <x-lucide-key> */}
</div>
</div>
{/*Stat Card 4 - Notifications */}
{/* TODO: port <x-card> */}
{/* TODO: port <x-card.body> */}
<div className="flex items-center justify-between">
<div>
<p className="text-sm font-medium text-zinc-500 dark:text-zinc-400">Unread Notifications</p>
<p className="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{auth().user().unreadNotifications().count()}</p>
</div>
<div className="rounded-full bg-amber-100 p-3 dark:bg-amber-900/50">
{/* TODO: port <x-lucide-bell> */}
</div>
</div>
</div>
{/*Quick Actions & Recent Activity */}
<div className="grid gap-6 lg:grid-cols-2">
{/*Quick Actions */}
{/* TODO: port <x-card> */}
{/* TODO: port <x-card.header> */}
<div className="flex items-center">
{/* TODO: port <x-lucide-zap> */}
<h2 className="text-xl font-semibold">
    </div>
    {/* TODO: port <x-card.body> */}
    <div className="grid gap-3 sm:grid-cols-2">
    <Button className="justify-start">
        {/* TODO: port <x-lucide-user> */}
        Edit Profile
    </Button>
    <Button className="justify-start">
        {/* TODO: port <x-lucide-lock> */}
        Change Password
    </Button>
    <Button className="justify-start">
        {/* TODO: port <x-lucide-bell> */}
        View Notifications
    </Button>
    {/* TODO: port @can */}
    <Button variant="filled" className="justify-start">
        {/* TODO: port <x-lucide-users> */}
        Manage Users
    </Button>
    {/* TODO: port @endcan */}
    {/* TODO: port @can */}
    <Button variant="filled" className="justify-start">
        {/* TODO: port <x-lucide-settings> */}
        Administration
    </Button>
    {/* TODO: port @endcan */}
    </div>
    {/*Recent Activity */}
    {/* TODO: port <x-card> */}
    {/* TODO: port <x-card.header> */}
    <div className="flex items-center">
    {/* TODO: port <x-lucide-activity> */}
    <h2 className="text-xl font-semibold">
        </div>
        {/* TODO: port <x-card.body> */}
        {/* TODO: port @php block */}
        $activities = \Spatie\Activitylog\Models\Activity::query()
        ->where('causer_id', auth()->id())
        ->latest()
        ->take(5)
        ->get();
        {/* TODO: port @endphp */}
        {/* TODO: port @if($activities->isEmpty()) */}
        <div className="px-6 py-8 text-center">
        {/* TODO: port <x-lucide-inbox> */}
        <p className="mt-2 text-sm text-zinc-500 dark:text-zinc-400">No recent activity</p>
        </div>
        {/* TODO: port @else */}
        <ul className="divide-y divide-zinc-200 dark:divide-zinc-700">
        {/* TODO: port @foreach */}
        <li className="flex items-center gap-4 px-6 py-3">
        <div className="flex-shrink-0">
        <div className="rounded-full bg-zinc-100 p-2 dark:bg-zinc-700">
        {/* TODO: port <x-lucide-activity> */}
        </div>
        </div>
        <div className="min-w-0 flex-1">
        <p className="truncate text-sm font-medium text-zinc-900 dark:text-white">
        {activity.description}
        </p>
        <p className="text-xs text-zinc-500 dark:text-zinc-400">
        {activity.created_at.diffForHumans()}
        </p>
        </div>
        </li>
        {/* TODO: port @endforeach */}
        </ul>
        {/* TODO: port @endif */}
        {/* TODO: port @can */}
        {/* TODO: port <x-card.footer> */}
        <Button variant="ghost">
            View All Activity
            {/* TODO: port <x-lucide-arrow-right> */}
        </Button>
        {/* TODO: port @endcan */}
        </div>
        {/*Recent Notifications */}
        {/* TODO: port <x-card> */}
        {/* TODO: port <x-card.header> */}
        <div className="flex items-center">
        {/* TODO: port <x-lucide-bell> */}
        <h2 className="text-xl font-semibold">
            </div>
            {/* TODO: port <x-card.body> */}
            {/* TODO: port @php block */}
            $notifications = auth()->user()->notifications()->latest()->take(5)->get();
            {/* TODO: port @endphp */}
            {/* TODO: port @if($notifications->isEmpty()) */}
            <div className="px-6 py-8 text-center">
            {/* TODO: port <x-lucide-bell> */}
            <p className="mt-2 text-sm text-zinc-500 dark:text-zinc-400">No notifications yet</p>
            </div>
            {/* TODO: port @else */}
            <ul className="divide-y divide-zinc-200 dark:divide-zinc-700">
            {/* TODO: port @foreach */}
            <li className="flex items-center gap-4 px-6 py-3 {is_null(notification.read_at) ? 'bg-blue-50 dark:bg-blue-900/20' : ''}">
            <div className="flex-shrink-0">
            <div className="rounded-full {is_null(notification.read_at) ? 'bg-blue-100 dark:bg-blue-800' : 'bg-zinc-100 dark:bg-zinc-700'} p-2">
            {/* TODO: port <x-lucide-bell> */}
            </div>
            </div>
            <div className="min-w-0 flex-1">
            <p className="truncate text-sm font-medium text-zinc-900 dark:text-white">
            {data_get(notification.data, 'title', class_basename(notification.type))}
            </p>
            {/* TODO: port @if($message = data_get($notification->data, 'message')) */}
            <p className="truncate text-xs text-zinc-500 dark:text-zinc-400">
            {message}
            </p>
            {/* TODO: port @endif */}
            <p className="text-xs text-zinc-400 dark:text-zinc-500">
            {notification.created_at.diffForHumans()}
            </p>
            </div>
            {/* TODO: port @if(is_null($notification->read_at)) */}
            <span className="h-2 w-2 rounded-full bg-blue-500"></span>
            {/* TODO: port @endif */}
            </li>
            {/* TODO: port @endforeach */}
            </ul>
            {/* TODO: port @endif */}
            {/* TODO: port <x-card.footer> */}
            <Button variant="ghost">
                View All Notifications
                {/* TODO: port <x-lucide-arrow-right> */}
            </Button>
            {/*System Info */}
            {/* TODO: port <x-card> */}
            {/* TODO: port <x-card.header> */}
            <div className="flex items-center">
            {/* TODO: port <x-lucide-info> */}
            <h2 className="text-xl font-semibold">
                </div>
                {/* TODO: port <x-card.body> */}
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                <p className="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Laravel Version</p>
                <p className="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{app().version()}</p>
                </div>
                <div>
                <p className="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">PHP Version</p>
                <p className="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{PHP_VERSION}</p>
                </div>
                <div>
                <p className="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Environment</p>
                <p className="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{ucfirst(app().environment())}</p>
                </div>
                <div>
                <p className="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Timezone</p>
                <p className="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{/* TODO: config('app.timezone') */}</p>
                </div>
                </div>
                </div>
        </AppLayout>
    );
}