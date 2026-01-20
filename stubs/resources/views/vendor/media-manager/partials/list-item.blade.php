<tr wire:key="media-{{ $item['id'] }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
    <td class="px-4 py-3">
        <flux:checkbox
            wire:click="toggleSelect({{ $item['id'] }})"
            :checked="in_array($item['id'], $selected)"
        />
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                @if($item['type'] === 'image' && $item['thumbnail_url'])
                    <img src="{{ $item['thumbnail_url'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                @elseif($item['type'] === 'video')
                    <flux:icon name="video-camera" class="w-5 h-5 text-zinc-400" />
                @elseif($item['type'] === 'audio')
                    <flux:icon name="musical-note" class="w-5 h-5 text-zinc-400" />
                @elseif($item['type'] === 'pdf')
                    <flux:icon name="document-text" class="w-5 h-5 text-red-400" />
                @elseif($item['type'] === 'document')
                    <flux:icon name="document-text" class="w-5 h-5 text-blue-400" />
                @elseif($item['type'] === 'spreadsheet')
                    <flux:icon name="table-cells" class="w-5 h-5 text-green-400" />
                @else
                    <flux:icon name="document" class="w-5 h-5 text-zinc-400" />
                @endif
            </div>
            <div class="min-w-0">
                <p class="font-medium truncate text-zinc-900 dark:text-zinc-100">{{ $item['name'] }}</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 truncate">{{ $item['file_name'] }}</p>
            </div>
        </div>
    </td>
    <td class="px-4 py-3">
        <flux:badge size="sm" variant="outline">{{ $item['collection'] }}</flux:badge>
    </td>
    <td class="px-4 py-3">
        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $item['mime_type'] }}</span>
    </td>
    <td class="px-4 py-3">
        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $item['size_formatted'] }}</span>
    </td>
    <td class="px-4 py-3">
        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $item['created_at'] }}</span>
    </td>
    <td class="px-4 py-3">
        <flux:button
            wire:click="openPreview({{ $item['id'] }})"
            variant="ghost"
            size="sm"
            icon="eye"
        />
    </td>
</tr>
