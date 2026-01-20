<div
    wire:key="media-{{ $item['id'] }}"
    class="group relative overflow-hidden cursor-pointer bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:ring-2 hover:ring-accent transition-all"
>
    {{-- Selection Checkbox --}}
    <div class="absolute top-2 left-2 z-10">
        <flux:checkbox
            wire:click="toggleSelect({{ $item['id'] }})"
            :checked="in_array($item['id'], $selected)"
            class="opacity-0 group-hover:opacity-100 transition-opacity {{ in_array($item['id'], $selected) ? '!opacity-100' : '' }}"
        />
    </div>

    {{-- Thumbnail --}}
    <div
        wire:click="openPreview({{ $item['id'] }})"
        class="aspect-square bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden"
    >
        @if($item['type'] === 'image' && $item['thumbnail_url'])
            <img
                src="{{ $item['thumbnail_url'] }}"
                alt="{{ $item['name'] }}"
                class="w-full h-full object-cover"
            >
        @elseif($item['type'] === 'video')
            <flux:icon name="video-camera" class="w-12 h-12 text-zinc-400" />
        @elseif($item['type'] === 'audio')
            <flux:icon name="musical-note" class="w-12 h-12 text-zinc-400" />
        @elseif($item['type'] === 'pdf')
            <flux:icon name="document-text" class="w-12 h-12 text-red-400" />
        @elseif($item['type'] === 'document')
            <flux:icon name="document-text" class="w-12 h-12 text-blue-400" />
        @elseif($item['type'] === 'spreadsheet')
            <flux:icon name="table-cells" class="w-12 h-12 text-green-400" />
        @else
            <flux:icon name="document" class="w-12 h-12 text-zinc-400" />
        @endif
    </div>

    {{-- Info --}}
    <div class="p-3">
        <p class="font-medium truncate text-zinc-900 dark:text-zinc-100" title="{{ $item['file_name'] }}">
            {{ $item['name'] }}
        </p>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
            {{ $item['size_formatted'] }}
        </p>
    </div>
</div>
