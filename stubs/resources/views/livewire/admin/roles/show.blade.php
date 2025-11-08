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
    return Permission::orderBy('name')->get();
});

$updatePermissions = function () {
    $this->role->syncPermissions($this->selectedPermissions);

    flash()->success('Permissions updated successfully.');
};

?>

<div>
    <flux:card class="mb-6">
        <flux:card.header>
            <flux:heading size="lg">Role Information</flux:heading>
        </flux:card.header>
        <flux:card.body>
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
        </flux:card.body>
    </flux:card>

    <flux:card>
        <flux:card.header>
            <flux:heading size="lg">Permissions</flux:heading>
        </flux:card.header>
        <flux:card.body>
            <form wire:submit="updatePermissions">
                <div class="space-y-4">
                    @foreach ($this->permissions as $permission)
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="permission-{{ $permission->id }}"
                                wire:model="selectedPermissions"
                                value="{{ $permission->id }}"
                                class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                            >
                            <label for="permission-{{ $permission->id }}" class="ml-3">
                                <span class="block text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $permission->display_name }}
                                </span>
                                @if($permission->description)
                                    <span class="block text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $permission->description }}
                                    </span>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <flux:button variant="ghost" :href="route('admin.roles.index')" wire:navigate>
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Update Permissions
                    </flux:button>
                </div>
            </form>
        </flux:card.body>
    </flux:card>
</div>
