<div>
    @if($this->permissions->isEmpty())
        <p class="text-zinc-500 dark:text-zinc-400 italic">No permissions available.</p>
    @else
        <div class="space-y-6">
            @foreach($this->permissions as $module => $modulePermissions)
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    {{-- Module Header --}}
                    <div
                        wire:click="toggleModule('{{ $module }}')"
                        class="cursor-pointer flex items-center justify-between bg-zinc-50 dark:bg-zinc-900 px-4 py-3 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    >
                        <div class="font-medium text-zinc-900 dark:text-zinc-100 capitalize">
                            {{ str_replace(['-', '_'], ' ', $module) }}
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ collect($modulePermissions)->filter(fn($p) => in_array($p->id, $selectedPermissions))->count() }}
                                / {{ $modulePermissions->count() }}
                            </span>
                            @php
                                $allSelected = collect($modulePermissions)->every(fn($p) => in_array($p->id, $selectedPermissions));
                            @endphp
                            @if($allSelected)
                                <div class="flex h-5 w-5 items-center justify-center rounded bg-blue-500 text-white">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            @else
                                <div class="h-5 w-5 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>
                            @endif
                        </div>
                    </div>

                    {{-- Permissions List --}}
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($modulePermissions as $permission)
                            <div
                                wire:click="togglePermission({{ $permission->id }})"
                                class="cursor-pointer flex items-center justify-between px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800"
                            >
                                <div>
                                    <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ $permission->display_name ?? $permission->name }}
                                    </div>
                                    @if($permission->description)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $permission->description }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    @if(in_array($permission->id, $selectedPermissions))
                                        <div class="flex h-5 w-5 items-center justify-center rounded bg-blue-500 text-white">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="h-5 w-5 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
