<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Dashboard</title></svelte:head>

<AppLayout>

<!--
TODO (20):
 - @can: authorization check — move to controller-provided `can` prop
 - @php: inline PHP — move to computed/derived value
 - x-layouts.app blade component — port to kit equivalent or drop
 - x-card blade component — port to kit equivalent or drop
 - x-card.body blade component — port to kit equivalent or drop
 - x-lucide-users blade component — port to kit equivalent or drop
 - x-lucide-shield blade component — port to kit equivalent or drop
 - x-lucide-key blade component — port to kit equivalent or drop
 - x-lucide-bell blade component — port to kit equivalent or drop
 - x-card.header blade component — port to kit equivalent or drop
 - x-lucide-zap blade component — port to kit equivalent or drop
 - x-lucide-user blade component — port to kit equivalent or drop
 - x-lucide-lock blade component — port to kit equivalent or drop
 - x-lucide-settings blade component — port to kit equivalent or drop
 - x-lucide-activity blade component — port to kit equivalent or drop
 - x-lucide-inbox blade component — port to kit equivalent or drop
 - x-card.footer blade component — port to kit equivalent or drop
 - x-lucide-arrow-right blade component — port to kit equivalent or drop
 - x-lucide-info blade component — port to kit equivalent or drop
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<!-- TODO: x-layouts.app -->
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {__('Welcome back, :name', ['name' => auth()->user()->name])}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {__("Here's what's happening with your account today.")}
            </p>
        </div>

        
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            
            <!-- TODO: x-card -->
                <!-- TODO: x-card.body -->
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{__('Total Users')}</p>
                            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{\App\Models\User::count()}</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/50">
                            <!-- TODO: x-lucide-users -->
                        </div>
                    </div>
                
            

            
            <!-- TODO: x-card -->
                <!-- TODO: x-card.body -->
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{__('Active Roles')}</p>
                            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{\Spatie\Permission\Models\Role::count()}</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/50">
                            <!-- TODO: x-lucide-shield -->
                        </div>
                    </div>
                
            

            
            <!-- TODO: x-card -->
                <!-- TODO: x-card.body -->
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{__('Permissions')}</p>
                            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{\Spatie\Permission\Models\Permission::count()}</p>
                        </div>
                        <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/50">
                            <!-- TODO: x-lucide-key -->
                        </div>
                    </div>
                
            

            
            <!-- TODO: x-card -->
                <!-- TODO: x-card.body -->
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{__('Unread Notifications')}</p>
                            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{auth()->user()->unreadNotifications()->count()}</p>
                        </div>
                        <div class="rounded-full bg-amber-100 p-3 dark:bg-amber-900/50">
                            <!-- TODO: x-lucide-bell -->
                        </div>
                    </div>
                
            
        </div>

        
        <div class="grid gap-6 lg:grid-cols-2">
            
            <!-- TODO: x-card -->
                <!-- TODO: x-card.header -->
                    <div class="flex items-center">
                        <!-- TODO: x-lucide-zap -->
                        <Heading size="lg">{__('Quick Actions')}</Heading>
                    </div>
                
                <!-- TODO: x-card.body -->
                    <div class="grid gap-3 sm:grid-cols-2">
                        <Button variant="primary" href={route('settings.profile.edit')} class="justify-start">
                            <!-- TODO: x-lucide-user -->
                            {__('Edit Profile')}
                        </Button>

                        <Button variant="primary" href={route('settings.user-password.edit')} class="justify-start">
                            <!-- TODO: x-lucide-lock -->
                            {__('Change Password')}
                        </Button>

                        <Button variant="primary" href={route('notifications.index')} class="justify-start">
                            <!-- TODO: x-lucide-bell -->
                            {__('View Notifications')}
                        </Button>

                            <Button variant="filled" href={route('security.users.index')} class="justify-start">
                                <!-- TODO: x-lucide-users -->
                                {__('Manage Users')}
                            </Button>

                            <Button variant="filled" href={route('admin.index')} class="justify-start">
                                <!-- TODO: x-lucide-settings -->
                                {__('Administration')}
                            </Button>

                    </div>
                
            

            
            <!-- TODO: x-card -->
                <!-- TODO: x-card.header -->
                    <div class="flex items-center">
                        <!-- TODO: x-lucide-activity -->
                        <Heading size="lg">{__('Recent Activity')}</Heading>
                    </div>
                
                <!-- TODO: x-card.body -->

                        $activities = \Spatie\Activitylog\Models\Activity::query()
                            ->where('causer_id', auth()->id())
                            ->latest()
                            ->take(5)
                            ->get();

                        <div class="px-6 py-8 text-center">
                            <!-- TODO: x-lucide-inbox -->
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{__('No recent activity')}</p>
                        </div>

                        <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">

                                <li class="flex items-center gap-4 px-6 py-3">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-full bg-zinc-100 p-2 dark:bg-zinc-700">
                                            <!-- TODO: x-lucide-activity -->
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">
                                            {$activity->description}
                                        </p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {$activity->created_at->diffForHumans()}
                                        </p>
                                    </div>
                                </li>

                        </ul>

                

                    <!-- TODO: x-card.footer -->
                        <Button variant="ghost" href={route('security.audit-trail.index')}>
                            {__('View All Activity')}
                            <!-- TODO: x-lucide-arrow-right -->
                        </Button>
                    

            
        </div>

        
        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-bell -->
                    <Heading size="lg">{__('Recent Notifications')}</Heading>
                </div>
            
            <!-- TODO: x-card.body -->

                    $notifications = auth()->user()->notifications()->latest()->take(5)->get();

                    <div class="px-6 py-8 text-center">
                        <!-- TODO: x-lucide-bell -->
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{__('No notifications yet')}</p>
                    </div>

                    <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">

                            <li class="flex items-center gap-4 px-6 py-3 {is_null($notification->read_at) ? 'bg-blue-50 dark:bg-blue-900/20' : ''}">
                                <div class="flex-shrink-0">
                                    <div class="rounded-full {is_null($notification->read_at) ? 'bg-blue-100 dark:bg-blue-800' : 'bg-zinc-100 dark:bg-zinc-700'} p-2">
                                        <!-- TODO: x-lucide-bell -->
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">
                                        {data_get($notification->data, 'title', class_basename($notification->type))}
                                    </p>

                                        <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                                            {$message}
                                        </p>

                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">
                                        {$notification->created_at->diffForHumans()}
                                    </p>
                                </div>

                                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>

                            </li>

                    </ul>

            
            <!-- TODO: x-card.footer -->
                <Button variant="ghost" href={route('notifications.index')}>
                    {__('View All Notifications')}
                    <!-- TODO: x-lucide-arrow-right -->
                </Button>
            
        

        
        <!-- TODO: x-card -->
            <!-- TODO: x-card.header -->
                <div class="flex items-center">
                    <!-- TODO: x-lucide-info -->
                    <Heading size="lg">{__('System Information')}</Heading>
                </div>
            
            <!-- TODO: x-card.body -->
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{__('Laravel Version')}</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{app()->version()}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{__('PHP Version')}</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{PHP_VERSION}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{__('Environment')}</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{ucfirst(app()->environment())}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{__('Timezone')}</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{config('app.timezone')}</p>
                    </div>
                </div>
            
        
    </div>
</AppLayout>