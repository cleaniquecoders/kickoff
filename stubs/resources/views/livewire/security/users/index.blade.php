<div>
    {{-- Stats --}}
    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Total Users') }}</div>
            <div class="mt-1 text-2xl font-bold">{{ $this->stats['total'] }}</div>
        </div>
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700/50 dark:bg-green-900/20">
            <div class="text-xs text-green-700 dark:text-green-300">{{ __('Active') }}</div>
            <div class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{{ $this->stats['active'] }}</div>
        </div>
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-700/50 dark:bg-amber-900/20">
            <div class="text-xs text-amber-700 dark:text-amber-300">{{ __('Suspended') }}</div>
            <div class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $this->stats['suspended'] }}</div>
        </div>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-700/50 dark:bg-red-900/20">
            <div class="text-xs text-red-700 dark:text-red-300">{{ __('Deleted') }}</div>
            <div class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{{ $this->stats['deleted'] }}</div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                placeholder="{{ __('Search by name or email...') }}" clearable />
        </div>
        <div class="flex gap-3">
            <flux:select wire:model.live="roleFilter" class="sm:w-44">
                <flux:select.option value="">{{ __('All Roles') }}</flux:select.option>
                @foreach ($this->roles as $role)
                    <flux:select.option value="{{ $role->name }}">{{ $role->display_name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="statusFilter" class="sm:w-44">
                <flux:select.option value="">{{ __('All Statuses') }}</flux:select.option>
                @foreach (App\Enums\UserStatus::cases() as $status)
                    <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Bulk Action Bar --}}
    @if (count($selected) > 0)
        <div
            class="mb-4 flex flex-col gap-3 rounded-lg border border-zinc-200 bg-zinc-50 p-3 sm:flex-row sm:items-center dark:border-zinc-700 dark:bg-zinc-800/50">
            <flux:text class="font-medium">
                {{ trans_choice(':count user selected|:count users selected', count($selected), ['count' => count($selected)]) }}
            </flux:text>
            <flux:spacer />
            <div class="flex flex-wrap items-center gap-2">
                @can('users.assign.roles')
                    <flux:select wire:model="bulkRole" size="sm" class="w-40">
                        <flux:select.option value="">{{ __('Assign role...') }}</flux:select.option>
                        @foreach ($this->assignableRoles as $role)
                            <flux:select.option value="{{ $role->name }}">{{ $role->display_name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:button size="sm" wire:click="bulkAssignRole" class="cursor-pointer">
                        {{ __('Apply') }}
                    </flux:button>
                @endcan
                @can('users.delete.account')
                    <flux:button size="sm" variant="danger" icon="trash-2" wire:click="bulkDelete" class="cursor-pointer">
                        {{ __('Delete') }}
                    </flux:button>
                @endcan
                <flux:button size="sm" variant="ghost" wire:click="clearSelection" class="cursor-pointer">
                    {{ __('Clear') }}
                </flux:button>
            </div>
        </div>
    @endif

    {{-- Users Table --}}
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th class="w-10 px-4 py-3">
                        <flux:checkbox wire:model.live="selectPage" />
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        {{ __('User') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        {{ __('Roles') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        {{ __('Status') }}</th>
                    <th class="hidden px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 md:table-cell dark:text-zinc-300">
                        {{ __('Joined') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        {{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->users as $user)
                    <tr wire:key="user-{{ $user->uuid }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                        <td class="px-4 py-4">
                            <flux:checkbox wire:model.live="selected" value="{{ $user->uuid }}" />
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                        {{ $user->initials() }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <div class="truncate font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}
                                    </div>
                                    <div class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->roles as $role)
                                    <flux:badge size="sm"
                                        color="{{ $role->name === 'superadmin' ? 'red' : ($role->name === 'administrator' ? 'amber' : 'zinc') }}">
                                        {{ $role->display_name ?? $role->name }}
                                    </flux:badge>
                                @empty
                                    <span class="text-sm italic text-zinc-400 dark:text-zinc-500">{{ __('No roles') }}</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <flux:badge size="sm" color="{{ $user->status()->color() }}">
                                {{ $user->status()->label() }}
                            </flux:badge>
                        </td>
                        <td class="hidden px-4 py-4 text-sm text-zinc-500 md:table-cell dark:text-zinc-400">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis" class="cursor-pointer" />
                                <flux:menu>
                                    @if ($user->trashed())
                                        @can('restore', $user)
                                            <flux:menu.item icon="rotate-ccw" wire:click="restore('{{ $user->uuid }}')">
                                                {{ __('Restore') }}
                                            </flux:menu.item>
                                        @endcan
                                    @else
                                        @can('update', $user)
                                            <flux:menu.item icon="pencil"
                                                x-on:click="$dispatch('open-user-form', { uuid: '{{ $user->uuid }}' })">
                                                {{ __('Edit') }}
                                            </flux:menu.item>
                                        @endcan
                                        @can('assignRoles', $user)
                                            <flux:menu.item icon="shield-check"
                                                x-on:click="$dispatch('open-user-access', { uuid: '{{ $user->uuid }}' })">
                                                {{ __('Manage Access') }}
                                            </flux:menu.item>
                                        @endcan
                                        @if (auth()->user()->canImpersonate() && $user->canBeImpersonated() && auth()->id() !== $user->id)
                                            <flux:menu.item icon="user-check" :href="route('impersonate', $user->id)">
                                                {{ __('Impersonate') }}
                                            </flux:menu.item>
                                        @endif
                                        @can('sendPasswordReset', $user)
                                            <flux:menu.item icon="key-round"
                                                wire:click="sendPasswordResetLink('{{ $user->uuid }}')">
                                                {{ __('Send Password Reset') }}
                                            </flux:menu.item>
                                        @endcan
                                        @if (! $user->hasVerifiedEmail())
                                            @can('sendVerification', $user)
                                                <flux:menu.item icon="mail-check"
                                                    wire:click="resendVerification('{{ $user->uuid }}')">
                                                    {{ __('Resend Verification') }}
                                                </flux:menu.item>
                                            @endcan
                                        @endif
                                        @can('suspend', $user)
                                            <flux:menu.separator />
                                            @if ($user->isSuspended())
                                                <flux:menu.item icon="user-check" wire:click="activate('{{ $user->uuid }}')">
                                                    {{ __('Activate') }}
                                                </flux:menu.item>
                                            @else
                                                <flux:menu.item icon="user-x" wire:click="suspend('{{ $user->uuid }}')">
                                                    {{ __('Suspend') }}
                                                </flux:menu.item>
                                            @endif
                                        @endcan
                                        @can('delete', $user)
                                            <flux:menu.item icon="trash-2" variant="danger"
                                                wire:click="delete('{{ $user->uuid }}')">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        @endcan
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No users found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $this->users->links() }}
    </div>
</div>
