<div class="space-y-6">
    {{-- User header --}}
    <div class="flex items-center gap-4">
        <div class="h-16 w-16 flex-shrink-0 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
            <span class="text-xl font-medium text-zinc-600 dark:text-zinc-300">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </span>
        </div>
        <div class="min-w-0 flex-1">
            <div class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ $user->name }}</div>
            <div class="text-sm text-zinc-500 dark:text-zinc-400 truncate">{{ $user->email }}</div>
            <div class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                Joined {{ $user->created_at->format('F d, Y') }}
            </div>
        </div>
        @if(auth()->user()->canImpersonate() && $user->canBeImpersonated() && auth()->id() !== $user->id)
            <flux:button variant="subtle" size="sm" icon="user-check" :href="route('impersonate', $user->id)" class="cursor-pointer">
                Impersonate
            </flux:button>
        @endif
    </div>

    <flux:separator variant="subtle" />

    {{-- Details form --}}
    @if($this->canUpdate())
        <form wire:submit="save" class="space-y-4">
            <flux:heading size="lg">Details</flux:heading>

            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model.blur="name" required maxlength="255" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input type="email" wire:model.blur="email" required maxlength="255" />
                <flux:error name="email" />
            </flux:field>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" class="cursor-pointer">Save details</flux:button>
            </div>
        </form>
    @else
        <div class="space-y-3">
            <flux:heading size="lg">Details</flux:heading>
            <div class="text-sm text-zinc-700 dark:text-zinc-300">
                <div><span class="text-zinc-500 dark:text-zinc-400">Name:</span> {{ $user->name }}</div>
                <div><span class="text-zinc-500 dark:text-zinc-400">Email:</span> {{ $user->email }}</div>
            </div>
        </div>
    @endif

    <flux:separator variant="subtle" />

    {{-- Current roles --}}
    <div class="space-y-3">
        <flux:heading size="lg">Current Roles</flux:heading>
        @if($user->roles->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach($user->roles as $role)
                    <flux:badge size="lg" color="{{ $role->name === 'Superadmin' ? 'red' : ($role->name === 'Admin' ? 'amber' : 'blue') }}">
                        {{ $role->display_name ?? $role->name }}
                    </flux:badge>
                @endforeach
            </div>
        @else
            <p class="text-sm text-zinc-500 dark:text-zinc-400 italic">No roles assigned.</p>
        @endif
    </div>

    {{-- Assignable roles --}}
    @if($this->canUpdate())
        <flux:separator variant="subtle" />

        <div class="space-y-3">
            <div>
                <flux:heading size="lg">Available Roles</flux:heading>
                <flux:subheading>Click a role to toggle assignment</flux:subheading>
            </div>

            @if($this->roles->isEmpty())
                <p class="text-sm text-zinc-500 dark:text-zinc-400 italic">No roles available to assign.</p>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach($this->roles as $role)
                        <div
                            wire:click="toggleRole({{ $role->id }})"
                            class="cursor-pointer rounded-lg border-2 p-4 transition-all
                                {{ in_array($role->id, $selectedRoles, true)
                                    ? 'border-blue-500 bg-blue-50 dark:border-blue-400 dark:bg-blue-900/20'
                                    : 'border-zinc-200 bg-white hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-zinc-600' }}"
                        >
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $role->display_name ?? $role->name }}
                                    </div>
                                    @if($role->description)
                                        <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $role->description }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    @if(in_array($role->id, $selectedRoles, true))
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-white">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="h-6 w-6 rounded-full border-2 border-zinc-300 dark:border-zinc-600"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
