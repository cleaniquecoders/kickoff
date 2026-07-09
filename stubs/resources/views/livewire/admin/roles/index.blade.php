<div>
    <div class="flex justify-end mb-4">
        {{ $roles->links() }}
    </div>

    <div class="mt-4 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden outline-1 -outline-offset-1 outline-zinc-200 dark:outline-zinc-700 sm:rounded-lg">
                    <table class="relative min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 sm:pl-6">
                                    {{ __('Role') }}
                                </th>
                                <th scope="col"
                                    class="hidden px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 md:table-cell dark:text-zinc-100">
                                    {{ __('Description') }}
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ __('Status') }}
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ __('Users') }}
                                </th>
                                <th scope="col" class="py-3.5 pr-4 pl-3 sm:pr-6">
                                    <span class="sr-only">{{ __('Actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                            @forelse ($roles as $role)
                                <tr wire:key="role-{{ $role->uuid }}"
                                    wire:click="openDetail('{{ $role->uuid }}')"
                                    class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                    <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-zinc-900 dark:text-white sm:pl-6">
                                        {{ $role->display_name }}
                                        @if ($role->isProtected())
                                            <flux:badge size="sm" color="zinc" class="ml-1">{{ __('Protected') }}</flux:badge>
                                        @endif
                                    </td>
                                    <td class="hidden px-3 py-4 text-sm text-zinc-700 md:table-cell dark:text-zinc-300">
                                        {{ $role->description ?? '-' }}
                                    </td>
                                    <td class="px-3 py-4 text-sm">
                                        <flux:badge size="sm" color="{{ $role->is_enabled ? 'green' : 'zinc' }}">
                                            {{ $role->is_enabled ? __('Enabled') : __('Disabled') }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                        {{ $role->users_count }}
                                    </td>
                                    <td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6"
                                        @click.stop>
                                        <flux:dropdown>
                                            <flux:button variant="ghost" size="sm" icon="ellipsis" class="cursor-pointer" />
                                            <flux:menu>
                                                <flux:menu.item icon="shield-check"
                                                    wire:click="openDetail('{{ $role->uuid }}')">
                                                    {{ __('Manage Permissions') }}
                                                </flux:menu.item>
                                                @can('update', $role)
                                                    <flux:menu.item icon="pencil"
                                                        x-on:click="$dispatch('open-role-form', { uuid: '{{ $role->uuid }}' })">
                                                        {{ __('Edit') }}
                                                    </flux:menu.item>
                                                @endcan
                                                @unless ($role->isProtected())
                                                    @can('update', $role)
                                                        <flux:menu.item icon="{{ $role->is_enabled ? 'x' : 'check' }}"
                                                            wire:click="toggleEnabled('{{ $role->uuid }}')">
                                                            {{ $role->is_enabled ? __('Disable') : __('Enable') }}
                                                        </flux:menu.item>
                                                    @endcan
                                                    @can('delete', $role)
                                                        <flux:menu.item icon="trash-2" variant="danger"
                                                            wire:click="delete('{{ $role->uuid }}')"
                                                            wire:confirm="{{ __('Are you sure you want to delete :role?', ['role' => $role->display_name]) }}">
                                                            {{ __('Delete') }}
                                                        </flux:menu.item>
                                                    @endcan
                                                @endunless
                                            </flux:menu>
                                        </flux:dropdown>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <x-empty-state
                                            title="No roles found"
                                            description="Get started by creating a new role."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $roles->links() }}
    </div>

    {{-- Role detail flyout (opened by row click / "Manage Permissions") --}}
    <x-flyout name="role-detail" size="xl" wire:model="showDetail">
        @if ($detailUuid)
            <livewire:admin.roles.show :uuid="$detailUuid" :key="'role-detail-'.$detailKey" />
        @endif
    </x-flyout>
</div>
