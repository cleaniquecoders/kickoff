<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Media Library') }}</flux:heading>
        <div class="flex items-center gap-2">
            {{-- View Toggle --}}
            <flux:button.group>
                <flux:button
                    wire:click="setView('grid')"
                    :variant="$view === 'grid' ? 'primary' : 'ghost'"
                    size="sm"
                    icon="squares-2x2"
                />
                <flux:button
                    wire:click="setView('list')"
                    :variant="$view === 'list' ? 'primary' : 'ghost'"
                    size="sm"
                    icon="list-bullet"
                />
            </flux:button.group>
        </div>
    </div>

    <div class="flex gap-6">
        {{-- Sidebar Filters --}}
        <aside class="w-72 flex-shrink-0">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 space-y-4">
                <flux:heading size="sm">{{ __('Filters') }}</flux:heading>

                {{-- Search --}}
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search files...') }}"
                    icon="magnifying-glass"
                    clearable
                />

                {{-- Collection Filter --}}
                <flux:select wire:model.live="collection" placeholder="{{ __('All Collections') }}">
                    <flux:select.option value="">{{ __('All Collections') }}</flux:select.option>
                    @foreach($this->collections as $col)
                        <flux:select.option value="{{ $col }}">{{ $col }}</flux:select.option>
                    @endforeach
                </flux:select>

                {{-- Type Filter --}}
                <flux:select wire:model.live="type" placeholder="{{ __('All Types') }}">
                    <flux:select.option value="">{{ __('All Types') }}</flux:select.option>
                    @foreach($this->typeOptions as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                {{-- Date Range --}}
                <div class="space-y-2">
                    <flux:input
                        type="date"
                        wire:model.live="dateFrom"
                        label="{{ __('Date From') }}"
                    />
                    <flux:input
                        type="date"
                        wire:model.live="dateTo"
                        label="{{ __('Date To') }}"
                    />
                </div>

                {{-- Clear Filters --}}
                @if($search || $collection || $type || $dateFrom || $dateTo)
                    <flux:button
                        wire:click="clearFilters"
                        variant="ghost"
                        size="sm"
                        class="w-full"
                    >
                        {{ __('Clear all filters') }}
                    </flux:button>
                @endif
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 space-y-4">
            {{-- Bulk Actions --}}
            @if(count($selected) > 0)
                <flux:callout variant="info" class="flex items-center justify-between">
                    <span>{{ count($selected) }} {{ __('item(s) selected') }}</span>
                    <div class="flex items-center gap-2">
                        <flux:button wire:click="deselectAll" variant="ghost" size="sm">
                            {{ __('Deselect All') }}
                        </flux:button>
                        <flux:button wire:click="confirmDelete" variant="danger" size="sm" icon="trash">
                            {{ __('Delete Selected') }}
                        </flux:button>
                    </div>
                </flux:callout>
            @endif

            {{-- Media Grid/List --}}
            @if($this->media->isEmpty())
                <div class="bg-white dark:bg-zinc-800 rounded-xl border-2 border-dashed border-zinc-300 dark:border-zinc-600 p-12 lg:p-16">
                    <div class="text-center max-w-md mx-auto">
                        @if($search || $collection || $type || $dateFrom || $dateTo)
                            {{-- Filtered empty state --}}
                            <div class="mx-auto size-16 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                <flux:icon name="magnifying-glass" class="size-8 text-zinc-400" />
                            </div>
                            <flux:heading size="lg" class="mt-6">{{ __('No results found') }}</flux:heading>
                            <p class="mt-2 text-zinc-500 dark:text-zinc-400">
                                {{ __('No media files match your current filters. Try adjusting your search criteria or clear the filters.') }}
                            </p>
                            <div class="mt-6">
                                <flux:button wire:click="clearFilters" variant="primary">
                                    {{ __('Clear all filters') }}
                                </flux:button>
                            </div>
                        @else
                            {{-- Initial empty state --}}
                            <div class="mx-auto size-20 rounded-full bg-gradient-to-br from-accent/20 to-accent/5 dark:from-accent/30 dark:to-accent/10 flex items-center justify-center">
                                <flux:icon name="cloud-arrow-up" class="size-10 text-accent" />
                            </div>
                            <flux:heading size="lg" class="mt-6">{{ __('No media files yet') }}</flux:heading>
                            <p class="mt-2 text-zinc-500 dark:text-zinc-400">
                                {{ __('Your media library is empty. Start by uploading images, videos, documents, or other files to organize and manage them here.') }}
                            </p>
                        @endif
                    </div>
                </div>
            @else
                @if($view === 'grid')
                    {{-- Grid View --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-{{ $gridColumns }} gap-4">
                        @foreach($this->media as $item)
                            @include('media-manager::partials.grid-item', ['item' => $item])
                        @endforeach
                    </div>
                @else
                    {{-- List View --}}
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-900">
                                <tr>
                                    <th class="w-12 px-4 py-3"></th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('File') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Collection') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Type') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Size') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">{{ __('Date') }}</th>
                                    <th class="w-20 px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($this->media as $item)
                                    @include('media-manager::partials.list-item', ['item' => $item])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $this->media->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Preview Modal --}}
    @if($previewMediaId && $this->previewMedia)
        @include('media-manager::partials.preview-panel', ['media' => $this->previewMedia])
    @endif

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="showDeleteConfirm" class="max-w-md">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Confirm Delete') }}</flux:heading>
            <p class="text-zinc-600 dark:text-zinc-400">
                {{ __('Are you sure you want to delete :count item(s)? This action cannot be undone.', ['count' => count($selected)]) }}
            </p>
            <div class="flex justify-end gap-3">
                <flux:button wire:click="cancelDelete" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button wire:click="deleteSelected" variant="danger">
                    {{ __('Delete') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
