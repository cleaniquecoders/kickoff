<div>
    @php
        $statusColor = fn (string $status) => match ($status) {
            'Delivered', 'Opened', 'Clicked' => 'green',
            'Sent' => 'blue',
            'Sending' => 'zinc',
            'Bounced', 'Complained', 'Failed' => 'red',
            default => 'zinc',
        };
        $mh = \App\Livewire\Admin\MailHistory\Index::class;
    @endphp

    {{-- Filters Toolbar --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="flex-1">
            <flux:input type="search" wire:model.live.debounce.300ms="search" placeholder="Search by recipient or subject..." icon="magnifying-glass" />
        </div>
        <div class="grid grid-cols-1 gap-3 sm:flex sm:items-center">
            <flux:select wire:model.live="status">
                <flux:select.option value="">All Status</flux:select.option>
                @foreach ($statuses as $s)
                    <flux:select.option value="{{ $s }}">{{ $s }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Summary + Per-page --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3 text-sm text-zinc-500 dark:text-zinc-400">
            <span>Showing <strong class="text-zinc-900 dark:text-white">{{ $mails->total() }}</strong> messages</span>
            @if ($search || $status)
                <flux:button variant="ghost" size="sm" wire:click="clearFilters">Clear filters</flux:button>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm text-zinc-500 dark:text-zinc-400">Per page</label>
            <flux:select wire:model.live="perPage" class="!w-24">
                <flux:select.option value="10">10</flux:select.option>
                <flux:select.option value="25">25</flux:select.option>
                <flux:select.option value="50">50</flux:select.option>
                <flux:select.option value="100">100</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- Table --}}
    <div class="flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden outline-1 -outline-offset-1 outline-zinc-200 dark:outline-zinc-700 sm:rounded-lg">
                    <table class="relative min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 sm:pl-6">Subject &amp; Recipient</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">Sent</th>
                                <th scope="col" class="py-3.5 pr-4 pl-3 sm:pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                            @forelse ($mails as $mail)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <td class="py-4 pr-3 pl-4 text-sm sm:pl-6">
                                        <div class="font-medium text-zinc-900 dark:text-white">{{ $mh::header($mail, 'Subject') ?? '(no subject)' }}</div>
                                        <div class="text-zinc-500 dark:text-zinc-400">{{ $mh::header($mail, 'To') ?? '—' }}</div>
                                    </td>
                                    <td class="px-3 py-4 text-sm">
                                        <flux:badge :color="$statusColor($mail->status)" size="sm">{{ $mail->status }}</flux:badge>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                        <div>{{ $mail->created_at->format('M d, Y H:i') }}</div>
                                        <div class="text-xs text-zinc-400 dark:text-zinc-500">{{ $mail->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                        <x-row-actions>
                                            <flux:menu.item icon="eye" wire:click="view('{{ $mail->uuid }}')">View</flux:menu.item>
                                        </x-row-actions>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center">
                                        <x-empty-state
                                            title="No mail history"
                                            description="No emails have been sent yet, or none match your filters."
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
        {{ $mails->links() }}
    </div>

    {{-- Detail Modal --}}
    <flux:modal name="mail-detail-modal" wire:model="showDetail" class="md:max-w-2xl">
        @if ($viewing)
            <div class="space-y-5">
                <div>
                    <flux:heading size="lg">{{ $mh::header($viewing, 'Subject') ?? '(no subject)' }}</flux:heading>
                    <flux:text class="mt-1">
                        <flux:badge :color="$statusColor($viewing->status)" size="sm">{{ $viewing->status }}</flux:badge>
                    </flux:text>
                </div>

                <dl class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">To</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $mh::header($viewing, 'To') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">From</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $mh::header($viewing, 'From') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">Sent</dt>
                        <dd class="text-zinc-900 dark:text-white">{{ $viewing->created_at->format('M d, Y H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">Reference</dt>
                        <dd class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $viewing->uuid }}</dd>
                    </div>
                </dl>

                {{-- Event timeline --}}
                <div>
                    <flux:heading size="sm" class="mb-3">Delivery Timeline</flux:heading>
                    @php $timeline = $viewing->getTimeline(); @endphp
                    @if ($timeline->isNotEmpty())
                        <ol class="relative ml-1 space-y-4 border-l border-zinc-200 pl-5 dark:border-zinc-700">
                            @foreach ($timeline as $event)
                                @php
                                    $dot = match ($event->type) {
                                        'delivered' => 'bg-blue-500',
                                        'opened' => 'bg-green-500',
                                        'clicked' => 'bg-indigo-500',
                                        'bounced', 'complained', 'failed' => 'bg-red-500',
                                        default => 'bg-zinc-400',
                                    };
                                @endphp
                                <li class="relative">
                                    <span class="absolute top-1 -left-[1.65rem] h-2.5 w-2.5 rounded-full ring-4 ring-white dark:ring-zinc-900 {{ $dot }}"></span>
                                    <div class="flex items-baseline justify-between gap-3">
                                        <span class="font-medium text-zinc-900 capitalize dark:text-white">{{ $event->type }}</span>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ \Illuminate\Support\Carbon::parse($event->occurred_at)->format('M d, Y H:i:s') }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">No opens or clicks recorded yet. (Delivered/bounced tracking needs a webhook-capable mail provider.)</p>
                    @endif
                </div>

                {{-- Attachments --}}
                @php $attachments = $mh::attachments($viewing); @endphp
                @if (! empty($attachments))
                    <div>
                        <flux:heading size="sm" class="mb-2">Attachments</flux:heading>
                        <ul class="flex flex-wrap gap-2">
                            @foreach ($attachments as $attachment)
                                <li class="inline-flex items-center gap-1.5 rounded-md bg-zinc-100 px-2.5 py-1 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    @svg('lucide-paperclip', 'h-3.5 w-3.5')
                                    <span class="font-mono">{{ $attachment }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Body --}}
                @php
                    $bodyHtml = $viewing->content['html'] ?? null;
                    $bodyText = $viewing->content['text'] ?? null;
                    $bodyRaw = (! $bodyHtml && ! $bodyText) ? $viewing->body : null;
                    $defaultTab = $bodyHtml ? 'preview' : ($bodyText ? 'text' : 'raw');
                @endphp
                @if ($bodyHtml || $bodyText || $bodyRaw)
                    <div x-data="{ bodyTab: '{{ $defaultTab }}' }">
                        <div class="mb-2 flex items-center gap-1">
                            <flux:heading size="sm" class="mr-2">Body</flux:heading>
                            @if ($bodyHtml)
                                <button type="button" @click="bodyTab = 'preview'" :class="bodyTab === 'preview' ? 'bg-zinc-200 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'" class="cursor-pointer rounded-md px-2.5 py-1 text-xs font-medium">Preview</button>
                                <button type="button" @click="bodyTab = 'source'" :class="bodyTab === 'source' ? 'bg-zinc-200 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'" class="cursor-pointer rounded-md px-2.5 py-1 text-xs font-medium">HTML source</button>
                            @endif
                            @if ($bodyText)
                                <button type="button" @click="bodyTab = 'text'" :class="bodyTab === 'text' ? 'bg-zinc-200 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'" class="cursor-pointer rounded-md px-2.5 py-1 text-xs font-medium">Text</button>
                            @endif
                        </div>

                        @if ($bodyHtml)
                            <div x-show="bodyTab === 'preview'">
                                <iframe
                                    sandbox=""
                                    title="Email preview"
                                    srcdoc="{{ $bodyHtml }}"
                                    class="h-96 w-full rounded-md border border-zinc-200 bg-white dark:border-zinc-700"
                                ></iframe>
                            </div>
                            <div x-show="bodyTab === 'source'" x-cloak>
                                <pre class="max-h-96 overflow-auto rounded-md border border-zinc-200 bg-zinc-50 p-3 text-xs text-zinc-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">{{ $bodyHtml }}</pre>
                            </div>
                        @endif
                        @if ($bodyText)
                            <div x-show="bodyTab === 'text'" @if ($bodyHtml) x-cloak @endif>
                                <pre class="max-h-96 overflow-auto rounded-md border border-zinc-200 bg-white p-3 text-sm whitespace-pre-wrap break-words font-sans text-zinc-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">{{ $bodyText }}</pre>
                            </div>
                        @endif
                        @if ($bodyRaw)
                            <pre class="max-h-96 overflow-auto rounded-md border border-zinc-200 bg-white p-3 text-xs whitespace-pre-wrap break-words text-zinc-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">{{ \Illuminate\Support\Str::limit($bodyRaw, 8000) }}</pre>
                        @endif
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end">
                <flux:button wire:click="closeDetail" variant="ghost">Close</flux:button>
            </div>
        @endif
    </flux:modal>
</div>
