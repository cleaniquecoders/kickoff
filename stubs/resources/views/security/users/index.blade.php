@php
    $users = \App\Models\User::with('roles')
        ->orderBy('name')
        ->paginate(15);
@endphp

<x-layouts.app title="User Management">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <flux:heading size="xl">User Management</flux:heading>
                <flux:subheading>{{ $sub ?? 'Manage users in the application' }}</flux:subheading>
            </div>
        </div>

        {{-- Stats --}}
        <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="text-xs text-zinc-500 dark:text-zinc-400">Total Users</div>
                <div class="mt-1 text-2xl font-bold">{{ \App\Models\User::count() }}</div>
            </div>
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700/50 dark:bg-green-900/20">
                <div class="text-xs text-green-700 dark:text-green-300">Active Today</div>
                <div class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ \App\Models\User::whereDate('updated_at', today())->count() }}
                </div>
            </div>
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700/50 dark:bg-blue-900/20">
                <div class="text-xs text-blue-700 dark:text-blue-300">With Roles</div>
                <div class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ \App\Models\User::has('roles')->count() }}
                </div>
            </div>
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-700/50 dark:bg-amber-900/20">
                <div class="text-xs text-amber-700 dark:text-amber-300">New This Month</div>
                <div class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">
                    {{ \App\Models\User::whereMonth('created_at', now()->month)->count() }}
                </div>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800 overflow-hidden">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Roles</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Joined</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <flux:badge size="sm" color="{{ $role->name === 'Superadmin' ? 'red' : ($role->name === 'Admin' ? 'amber' : 'zinc') }}">
                                            {{ $role->display_name ?? $role->name }}
                                        </flux:badge>
                                    @empty
                                        <span class="text-sm text-zinc-400 dark:text-zinc-500 italic">No roles</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <flux:button size="sm" variant="ghost" :href="route('security.users.show', $user->uuid)" class="cursor-pointer">
                                    Manage
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.app>
