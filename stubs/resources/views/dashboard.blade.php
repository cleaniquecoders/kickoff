<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        {{-- Welcome Header --}}
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __("Here's what's happening with your account today.") }}
            </p>
        </div>

        {{-- Stats Overview --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Stat Card 1 --}}
            <x-card>
                <x-card.body>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Users') }}</p>
                            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ \App\Models\User::count() }}</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/50">
                            <x-lucide-users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </x-card.body>
            </x-card>

            {{-- Stat Card 2 --}}
            <x-card>
                <x-card.body>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Active Roles') }}</p>
                            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ \Spatie\Permission\Models\Role::count() }}</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/50">
                            <x-lucide-shield class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                </x-card.body>
            </x-card>

            {{-- Stat Card 3 --}}
            <x-card>
                <x-card.body>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Permissions') }}</p>
                            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ \Spatie\Permission\Models\Permission::count() }}</p>
                        </div>
                        <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/50">
                            <x-lucide-key class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                </x-card.body>
            </x-card>

            {{-- Stat Card 4 --}}
            <x-card>
                <x-card.body>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Audit Logs') }}</p>
                            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ \Spatie\Activitylog\Models\Activity::count() }}</p>
                        </div>
                        <div class="rounded-full bg-amber-100 p-3 dark:bg-amber-900/50">
                            <x-lucide-scroll-text class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                        </div>
                    </div>
                </x-card.body>
            </x-card>
        </div>

        {{-- Quick Actions & Recent Activity --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Quick Actions --}}
            <x-card>
                <x-card.header>
                    <div class="flex items-center">
                        <x-lucide-zap class="mr-2 h-5 w-5 text-brand-500" />
                        <flux:heading size="lg">{{ __('Quick Actions') }}</flux:heading>
                    </div>
                </x-card.header>
                <x-card.body>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <flux:button variant="primary" :href="route('settings.profile.edit')" wire:navigate class="justify-start">
                            <x-lucide-user class="mr-2 h-4 w-4" />
                            {{ __('Edit Profile') }}
                        </flux:button>

                        <flux:button variant="primary" :href="route('settings.user-password.edit')" wire:navigate class="justify-start">
                            <x-lucide-lock class="mr-2 h-4 w-4" />
                            {{ __('Change Password') }}
                        </flux:button>

                        @can('access.user-management')
                            <flux:button variant="filled" :href="route('security.users.index')" wire:navigate class="justify-start">
                                <x-lucide-users class="mr-2 h-4 w-4" />
                                {{ __('Manage Users') }}
                            </flux:button>
                        @endcan

                        @can('access.admin-panel')
                            <flux:button variant="filled" :href="route('admin.index')" wire:navigate class="justify-start">
                                <x-lucide-settings class="mr-2 h-4 w-4" />
                                {{ __('Administration') }}
                            </flux:button>
                        @endcan
                    </div>
                </x-card.body>
            </x-card>

            {{-- Recent Activity --}}
            <x-card>
                <x-card.header>
                    <div class="flex items-center">
                        <x-lucide-activity class="mr-2 h-5 w-5 text-brand-500" />
                        <flux:heading size="lg">{{ __('Recent Activity') }}</flux:heading>
                    </div>
                </x-card.header>
                <x-card.body :padding="false">
                    @php
                        $activities = \Spatie\Activitylog\Models\Activity::query()
                            ->where('causer_id', auth()->id())
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp

                    @if($activities->isEmpty())
                        <div class="px-6 py-8 text-center">
                            <x-lucide-inbox class="mx-auto h-12 w-12 text-zinc-400" />
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('No recent activity') }}</p>
                        </div>
                    @else
                        <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($activities as $activity)
                                <li class="flex items-center gap-4 px-6 py-3">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-full bg-zinc-100 p-2 dark:bg-zinc-700">
                                            <x-lucide-activity class="h-4 w-4 text-zinc-600 dark:text-zinc-400" />
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $activity->description }}
                                        </p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-card.body>
                @can('access.audit-monitoring')
                    <x-card.footer>
                        <flux:button variant="ghost" :href="route('security.audit-trail.index')" wire:navigate>
                            {{ __('View All Activity') }}
                            <x-lucide-arrow-right class="ml-2 h-4 w-4" />
                        </flux:button>
                    </x-card.footer>
                @endcan
            </x-card>
        </div>

        {{-- System Info --}}
        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-info class="mr-2 h-5 w-5 text-brand-500" />
                    <flux:heading size="lg">{{ __('System Information') }}</flux:heading>
                </div>
            </x-card.header>
            <x-card.body>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Laravel Version') }}</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{{ app()->version() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('PHP Version') }}</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{{ PHP_VERSION }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Environment') }}</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{{ ucfirst(app()->environment()) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Timezone') }}</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">{{ config('app.timezone') }}</p>
                    </div>
                </div>
            </x-card.body>
        </x-card>
    </div>
</x-layouts.app>
