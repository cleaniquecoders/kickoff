<x-layouts.app title="Audit Details">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <flux:heading size="xl">Audit Details</flux:heading>
                <flux:subheading>{{ $sub ?? 'View audit log details' }}</flux:subheading>
            </div>
            <flux:button variant="ghost" icon="arrow-left" href="{{ route('security.audit-trail.index') }}" class="cursor-pointer">
                Back
            </flux:button>
        </div>

        {{-- Event Info --}}
        <div class="mb-6 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    @switch($audit->event)
                        @case('created')
                            <flux:badge color="green" size="lg">Created</flux:badge>
                            @break
                        @case('updated')
                            <flux:badge color="blue" size="lg">Updated</flux:badge>
                            @break
                        @case('deleted')
                            <flux:badge color="red" size="lg">Deleted</flux:badge>
                            @break
                        @default
                            <flux:badge size="lg">{{ ucfirst($audit->event) }}</flux:badge>
                    @endswitch
                    <span class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ class_basename($audit->auditable_type) }}
                    </span>
                </div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $audit->created_at->format('F d, Y \a\t g:i A') }}
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-zinc-500 dark:text-zinc-400">Model ID:</span>
                    <span class="ml-2 font-medium text-zinc-900 dark:text-zinc-100">{{ $audit->auditable_id }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 dark:text-zinc-400">User:</span>
                    <span class="ml-2 font-medium text-zinc-900 dark:text-zinc-100">
                        {{ $audit->user?->name ?? 'System' }}
                    </span>
                </div>
                <div>
                    <span class="text-zinc-500 dark:text-zinc-400">IP Address:</span>
                    <span class="ml-2 font-medium text-zinc-900 dark:text-zinc-100">{{ $audit->ip_address ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 dark:text-zinc-400">User Agent:</span>
                    <span class="ml-2 font-medium text-zinc-900 dark:text-zinc-100 truncate block max-w-xs">
                        {{ Str::limit($audit->user_agent ?? 'N/A', 40) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Old Values --}}
        @if(!empty($audit->old_values))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-6 dark:border-red-700/50 dark:bg-red-900/20">
                <flux:heading size="lg" class="mb-4 text-red-700 dark:text-red-300">Old Values</flux:heading>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr>
                                <th class="text-left py-2 pr-4 font-medium text-red-700 dark:text-red-300">Field</th>
                                <th class="text-left py-2 font-medium text-red-700 dark:text-red-300">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audit->old_values as $key => $value)
                                <tr class="border-t border-red-200 dark:border-red-700/50">
                                    <td class="py-2 pr-4 font-medium text-red-800 dark:text-red-200">{{ $key }}</td>
                                    <td class="py-2 text-red-700 dark:text-red-300">
                                        @if(is_array($value))
                                            <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            {{ $value ?? 'null' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- New Values --}}
        @if(!empty($audit->new_values))
            <div class="rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-700/50 dark:bg-green-900/20">
                <flux:heading size="lg" class="mb-4 text-green-700 dark:text-green-300">New Values</flux:heading>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr>
                                <th class="text-left py-2 pr-4 font-medium text-green-700 dark:text-green-300">Field</th>
                                <th class="text-left py-2 font-medium text-green-700 dark:text-green-300">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audit->new_values as $key => $value)
                                <tr class="border-t border-green-200 dark:border-green-700/50">
                                    <td class="py-2 pr-4 font-medium text-green-800 dark:text-green-200">{{ $key }}</td>
                                    <td class="py-2 text-green-700 dark:text-green-300">
                                        @if(is_array($value))
                                            <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            {{ $value ?? 'null' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
