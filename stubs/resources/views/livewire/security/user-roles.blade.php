<div>
    @if($this->roles->isEmpty())
        <p class="text-zinc-500 dark:text-zinc-400 italic">No roles available to assign.</p>
    @else
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($this->roles as $role)
                <div
                    wire:click="toggleRole({{ $role->id }})"
                    class="cursor-pointer rounded-lg border-2 p-4 transition-all
                        {{ in_array($role->id, $selectedRoles)
                            ? 'border-blue-500 bg-blue-50 dark:border-blue-400 dark:bg-blue-900/20'
                            : 'border-zinc-200 bg-white hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-zinc-600' }}"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $role->display_name ?? $role->name }}
                            </div>
                            @if($role->description)
                                <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $role->description }}
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            @if(in_array($role->id, $selectedRoles))
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
