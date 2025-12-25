<?php

use App\Models\Role;
use App\Models\Permission;

use function Livewire\Volt\{state, mount, computed};

state(['role', 'selectedPermissions' => []]);

mount(function ($uuid) {
    $this->role = Role::with('permissions')->where('uuid', $uuid)->firstOrFail();
    $this->selectedPermissions = $this->role->permissions->pluck('id')->toArray();
});

$permissions = computed(function () {
    return Permission::where('is_enabled', true)
        ->orderBy('module')
        ->orderBy('function')
        ->get()
        ->groupBy('module');
});

$updatePermissions = function () {
    // Get the permission models from the selected IDs
    $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();

    $this->role->syncPermissions($permissions);

    // Dispatch toast notification
    $this->dispatch('toast',
        type: 'success',
        message: 'Permissions updated successfully!',
        duration: 3000
    );
};

?>

<div>
    <x-card class="mb-6">
        <x-card.header>
            <flux:heading size="lg">Role Information</flux:heading>
        </x-card.header>
        <x-card.body>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Name</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $role->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Display Name</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $role->display_name }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Description</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $role->description ?? 'No description' }}</dd>
                </div>
            </dl>
        </x-card.body>
    </x-card>

    <x-card>
        <x-card.header>
            <flux:heading size="lg">Permissions</flux:heading>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                Manage what this role can access and perform in the system
            </p>
        </x-card.header>
        <x-card.body>
            <form wire:submit="updatePermissions">
                <div class="space-y-8">
                    @foreach ($this->permissions as $module => $modulePermissions)
                        <div>
                            <h3 class="mb-4 flex items-center gap-2 text-base font-semibold text-zinc-900 dark:text-white">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-100 dark:bg-brand-900/30">
                                    @if($module === 'Admin')
                                        <x-lucide-shield-check class="h-4 w-4 text-brand-600 dark:text-brand-400" />
                                    @elseif($module === 'Users')
                                        <x-lucide-users class="h-4 w-4 text-brand-600 dark:text-brand-400" />
                                    @elseif($module === 'Roles')
                                        <x-lucide-shield class="h-4 w-4 text-brand-600 dark:text-brand-400" />
                                    @elseif($module === 'Security')
                                        <x-lucide-lock class="h-4 w-4 text-brand-600 dark:text-brand-400" />
                                    @elseif($module === 'Profile')
                                        <x-lucide-user class="h-4 w-4 text-brand-600 dark:text-brand-400" />
                                    @elseif($module === 'Notifications')
                                        <x-lucide-bell class="h-4 w-4 text-brand-600 dark:text-brand-400" />
                                    @elseif($module === 'Dashboard')
                                        <x-lucide-layout-dashboard class="h-4 w-4 text-brand-600 dark:text-brand-400" />
                                    @else
                                        <x-lucide-box class="h-4 w-4 text-brand-600 dark:text-brand-400" />
                                    @endif
                                </span>
                                {{ $module }}
                            </h3>

                            <div class="grid grid-cols-1 gap-4 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4 md:grid-cols-2 lg:grid-cols-3">
                                @foreach ($modulePermissions as $permission)
                                    <label
                                        for="permission-{{ $permission->id }}"
                                        class="group relative flex cursor-pointer rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 transition-all hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-sm"
                                        :class="selectedPermissions.includes({{ $permission->id }}) ? 'border-brand-500 dark:border-brand-600 bg-brand-50 dark:bg-brand-950/30' : ''"
                                    >
                                        <div class="flex h-full w-full items-start gap-3">
                                            <input
                                                type="checkbox"
                                                id="permission-{{ $permission->id }}"
                                                wire:model="selectedPermissions"
                                                value="{{ $permission->id }}"
                                                class="mt-0.5 h-4 w-4 rounded border-zinc-300 dark:border-zinc-600 text-brand-600 focus:ring-brand-600 dark:bg-zinc-800 dark:focus:ring-brand-500"
                                            >
                                            <div class="flex-1 min-w-0">
                                                <span class="block text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $permission->function }}
                                                </span>
                                                <span class="mt-1 block text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                                    {{ $permission->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex items-center justify-between gap-4 border-t border-zinc-200 dark:border-zinc-700 pt-6">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        <span class="font-medium">{{ count($selectedPermissions) }}</span> permissions selected
                    </div>
                    <div class="flex gap-2">
                        <flux:button variant="ghost" :href="route('admin.roles.index')" wire:navigate>
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Update Permissions</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </flux:button>
                    </div>
                </div>
            </form>
        </x-card.body>
    </x-card>
</div>
