<flux:modal wire:model="previewMediaId" class="max-w-4xl" variant="flyout">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <flux:heading size="lg" class="truncate">{{ $media['name'] }}</flux:heading>
            <flux:button wire:click="closePreview" variant="ghost" size="sm" icon="x-mark" />
        </div>

        {{-- Content --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Preview --}}
            <div class="bg-zinc-100 dark:bg-zinc-800 rounded-lg overflow-hidden flex items-center justify-center min-h-[300px]">
                @if($media['type'] === 'image')
                    <img src="{{ $media['url'] }}" alt="{{ $media['name'] }}" class="max-w-full max-h-[400px] object-contain">
                @elseif($media['type'] === 'video')
                    <video controls class="max-w-full max-h-[400px]">
                        <source src="{{ $media['url'] }}" type="{{ $media['mime_type'] }}">
                        {{ __('Your browser does not support the video tag.') }}
                    </video>
                @elseif($media['type'] === 'audio')
                    <div class="p-8 text-center w-full">
                        <flux:icon name="musical-note" class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                        <audio controls class="w-full">
                            <source src="{{ $media['url'] }}" type="{{ $media['mime_type'] }}">
                            {{ __('Your browser does not support the audio tag.') }}
                        </audio>
                    </div>
                @elseif($media['type'] === 'pdf')
                    <iframe src="{{ $media['url'] }}" class="w-full h-[400px]"></iframe>
                @else
                    <div class="text-center p-8">
                        <flux:icon name="document" class="w-16 h-16 text-zinc-400 mx-auto" />
                        <p class="mt-4 text-zinc-500">{{ __('Preview not available') }}</p>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="space-y-4">
                <flux:heading size="sm">{{ __('File Details') }}</flux:heading>

                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 space-y-3">
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('File Name') }}</p>
                        <p class="text-zinc-900 dark:text-zinc-100 break-all">{{ $media['file_name'] }}</p>
                    </div>

                    <hr class="border-zinc-200 dark:border-zinc-700" />

                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Collection') }}</p>
                        <flux:badge size="sm" variant="outline">{{ $media['collection'] }}</flux:badge>
                    </div>

                    <hr class="border-zinc-200 dark:border-zinc-700" />

                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Type') }}</p>
                        <p class="text-zinc-900 dark:text-zinc-100">{{ $media['mime_type'] }}</p>
                    </div>

                    <hr class="border-zinc-200 dark:border-zinc-700" />

                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Size') }}</p>
                        <p class="text-zinc-900 dark:text-zinc-100">{{ $media['size_formatted'] }}</p>
                    </div>

                    <hr class="border-zinc-200 dark:border-zinc-700" />

                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Created') }}</p>
                        <p class="text-zinc-900 dark:text-zinc-100">{{ $media['created_at'] }}</p>
                    </div>

                    <hr class="border-zinc-200 dark:border-zinc-700" />

                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Updated') }}</p>
                        <p class="text-zinc-900 dark:text-zinc-100">{{ $media['updated_at'] }}</p>
                    </div>

                    @if(!empty($media['custom_properties']))
                        <hr class="border-zinc-200 dark:border-zinc-700" />
                        <div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-2">{{ __('Custom Properties') }}</p>
                            @foreach($media['custom_properties'] as $key => $value)
                                <div class="flex justify-between py-1">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $key }}</span>
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button
                wire:click="deleteSingle({{ $media['id'] }})"
                wire:confirm="{{ __('Are you sure you want to delete this file?') }}"
                variant="ghost"
                class="text-red-600 hover:text-red-700"
            >
                {{ __('Delete') }}
            </flux:button>
            <flux:button
                href="{{ $media['url'] }}"
                target="_blank"
                variant="primary"
                icon="arrow-down-tray"
            >
                {{ __('Download') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
