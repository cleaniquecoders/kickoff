<flux:modal variant="flyout" wire:model="showing" class="max-w-lg">
    @if ($this->user)
        <div class="space-y-6" x-data="{ tab: 'roles' }">
            {{-- User Info --}}
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                        {{ $this->user->initials() }}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <flux:heading size="lg" class="truncate">{{ $this->user->name }}</flux:heading>
                    <div class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ $this->user->email }}</div>
                </div>
                <flux:badge size="sm" color="{{ $this->user->status()->color() }}">
                    {{ $this->user->status()->label() }}
                </flux:badge>
            </div>

            {{-- Tabs (Alpine — flux:tabs is Pro-only) --}}
            <div class="border-b border-zinc-200 dark:border-zinc-700">
                <nav class="-mb-px flex gap-6">
                    <button type="button" x-on:click="tab = 'roles'"
                        :class="tab === 'roles'
                            ? 'border-accent text-accent-content'
                            : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                        class="cursor-pointer border-b-2 px-1 pb-3 text-sm font-medium">
                        {{ __('Roles') }}
                    </button>
                    @can('assignPermissions', $this->user)
                        <button type="button" x-on:click="tab = 'permissions'"
                            :class="tab === 'permissions'
                                ? 'border-accent text-accent-content'
                                : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            class="cursor-pointer border-b-2 px-1 pb-3 text-sm font-medium">
                            {{ __('Permissions') }}
                        </button>
                    @endcan
                </nav>
            </div>

            {{-- Roles Tab --}}
            <div x-show="tab === 'roles'" class="space-y-2">
                @foreach ($this->assignableRoles as $role)
                    @php $hasRole = $this->user->hasRole($role->name); @endphp
                    <div wire:click="toggleRole('{{ $role->name }}')" wire:key="role-{{ $role->uuid }}"
                        class="flex cursor-pointer items-center justify-between rounded-lg border border-zinc-200 px-4 py-3 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                        <div>
                            <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                {{ $role->display_name ?? $role->name }}
                            </div>
                            @if ($role->description)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $role->description }}</div>
                            @endif
                        </div>
                        @if ($hasRole)
                            <div class="flex h-5 w-5 items-center justify-center rounded bg-blue-500 text-white">
                                <flux:icon.check class="size-3!" />
                            </div>
                        @else
                            <div class="h-5 w-5 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Permissions Tab --}}
            @can('assignPermissions', $this->user)
                <div x-show="tab === 'permissions'" x-cloak class="space-y-4">
                    <flux:text class="text-sm">
                        {{ __('Direct permissions apply on top of role permissions. Role-inherited permissions are managed on the role.') }}
                    </flux:text>

                    @foreach ($this->permissions as $module => $modulePermissions)
                        <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700"
                            wire:key="module-{{ $module }}">
                            <div class="bg-zinc-50 px-4 py-2 font-medium capitalize text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
                                {{ str_replace(['-', '_'], ' ', $module) }}
                            </div>
                            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach ($modulePermissions as $permission)
                                    @php
                                        $inherited = $this->inheritedPermissionIds->contains($permission->id);
                                        $direct = $this->user->permissions->contains('id', $permission->id);
                                    @endphp
                                    <div wire:key="permission-{{ $permission->uuid }}"
                                        @unless ($inherited) wire:click="togglePermission({{ $permission->id }})" @endunless
                                        class="flex items-center justify-between px-4 py-3 {{ $inherited ? 'opacity-60' : 'cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                                        <div>
                                            <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                                {{ $permission->display_name ?? $permission->name }}
                                            </div>
                                            @if ($inherited)
                                                <div class="text-xs italic text-zinc-500 dark:text-zinc-400">
                                                    {{ __('via :roles', ['roles' => $this->inheritedVia->get($permission->id)]) }}
                                                </div>
                                            @elseif ($permission->description)
                                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ $permission->description }}
                                                </div>
                                            @endif
                                        </div>
                                        @if ($inherited || $direct)
                                            <div
                                                class="flex h-5 w-5 items-center justify-center rounded {{ $inherited ? 'bg-zinc-400 dark:bg-zinc-600' : 'bg-blue-500' }} text-white">
                                                <flux:icon.check class="size-3!" />
                                            </div>
                                        @else
                                            <div class="h-5 w-5 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endcan

            <div class="flex justify-end">
                <flux:button variant="ghost" wire:click="close" class="cursor-pointer">
                    {{ __('Close') }}
                </flux:button>
            </div>
        </div>
    @endif
</flux:modal>
