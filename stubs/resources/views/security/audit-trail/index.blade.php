@php
    $audits = \App\Models\Audit::with('user')
        ->latest()
        ->paginate(20);
@endphp

<x-layouts.app title="Audit Trail">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <flux:heading size="xl">Audit Trail</flux:heading>
                <flux:subheading>{{ $sub ?? 'View activity logs and changes' }}</flux:subheading>
            </div>
        </div>

        {{-- Stats --}}
        <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="text-xs text-zinc-500 dark:text-zinc-400">Total Logs</div>
                <div class="mt-1 text-2xl font-bold">{{ \App\Models\Audit::count() }}</div>
            </div>
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700/50 dark:bg-green-900/20">
                <div class="text-xs text-green-700 dark:text-green-300">Created</div>
                <div class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ \App\Models\Audit::where('event', 'created')->count() }}
                </div>
            </div>
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700/50 dark:bg-blue-900/20">
                <div class="text-xs text-blue-700 dark:text-blue-300">Updated</div>
                <div class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ \App\Models\Audit::where('event', 'updated')->count() }}
                </div>
            </div>
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-700/50 dark:bg-red-900/20">
                <div class="text-xs text-red-700 dark:text-red-300">Deleted</div>
                <div class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ \App\Models\Audit::where('event', 'deleted')->count() }}
                </div>
            </div>
        </div>

        {{-- Audit Logs --}}
        <div class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800 overflow-hidden">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($audits as $audit)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4">
                                @switch($audit->event)
                                    @case('created')
                                        <flux:badge color="green">Created</flux:badge>
                                        @break
                                    @case('updated')
                                        <flux:badge color="blue">Updated</flux:badge>
                                        @break
                                    @case('deleted')
                                        <flux:badge color="red">Deleted</flux:badge>
                                        @break
                                    @default
                                        <flux:badge>{{ ucfirst($audit->event) }}</flux:badge>
                                @endswitch
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ class_basename($audit->auditable_type) }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    ID: {{ $audit->auditable_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($audit->user)
                                    <div class="flex items-center gap-2">
                                        <div class="h-8 w-8 flex-shrink-0 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                            <span class="text-xs font-medium text-zinc-600 dark:text-zinc-300">
                                                {{ strtoupper(substr($audit->user->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $audit->user->name }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-zinc-400 dark:text-zinc-500 italic">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $audit->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $audit->created_at->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <flux:button size="sm" variant="ghost" :href="route('security.audit-trail.show', $audit->uuid)" class="cursor-pointer">
                                    View
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                No audit logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $audits->links() }}
        </div>
    </div>
</x-layouts.app>
