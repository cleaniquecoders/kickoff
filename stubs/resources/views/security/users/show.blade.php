<x-layouts.app title="Manage User - {{ $user->name }}">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <flux:heading size="xl">Manage User</flux:heading>
                <flux:subheading>{{ $sub ?? 'Manage roles for user' }}</flux:subheading>
            </div>
            <flux:button variant="ghost" icon="arrow-left" href="{{ route('security.users.index') }}" class="cursor-pointer">
                Back
            </flux:button>
        </div>

        {{-- User Info Card --}}
        <div class="mb-6 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 flex-shrink-0 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                    <span class="text-xl font-medium text-zinc-600 dark:text-zinc-300">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </span>
                </div>
                <div>
                    <div class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                    <div class="text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                    <div class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">
                        Joined {{ $user->created_at->format('F d, Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Current Roles --}}
        <div class="mb-6 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Current Roles</flux:heading>

            @if($user->roles->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach($user->roles as $role)
                        <flux:badge size="lg" color="{{ $role->name === 'Superadmin' ? 'red' : ($role->name === 'Admin' ? 'amber' : 'blue') }}">
                            {{ $role->display_name ?? $role->name }}
                        </flux:badge>
                    @endforeach
                </div>
            @else
                <p class="text-zinc-500 dark:text-zinc-400 italic">No roles assigned to this user.</p>
            @endif
        </div>

        {{-- Assign Roles --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Available Roles</flux:heading>
            <flux:subheading class="mb-4">Select roles to assign to this user</flux:subheading>

            @livewire('security.user-roles', ['user' => $user])
        </div>
    </div>
</x-layouts.app>
