@if (session()->has('message'))
    @php
        $message = session('message');
        $parts = explode('|', $message, 2);
        $classType = json_decode($parts[0], JSON_OBJECT_AS_ARRAY) ?? [
            'border' => 'border-gray-300 dark:border-zinc-600',
            'bg' => 'bg-gray-100 dark:bg-gray-800',
            'text' => 'text-gray-600 dark:text-gray-400',
        ];
        $text = $parts[1] ?? $parts[0];
    @endphp

    <div class="fixed bottom-4 md:bottom-8 left-0 right-0 mx-auto px-4 md:px-0 md:left-auto md:right-8 w-full md:w-96 z-50 transition-all duration-200"
        x-data="{ showToastr: true }"
        x-init="setTimeout(() => showToastr = false, 7500)"
        x-show="showToastr"
        x-transition:enter="transform ease-out duration-300"
        x-transition:enter-start="translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transform ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-2 opacity-0">

        <div class="bg-white dark:bg-zinc-800 border {{ $classType['border'] }} shadow-lg rounded-xl py-3 px-4 flex items-center gap-3 backdrop-blur-sm">
            <div class="{{ $classType['bg'] }} h-9 w-9 rounded-full inline-flex items-center justify-center flex-shrink-0">
                <x-icon name="o-exclamation-circle" class="{{ $classType['text'] }} w-5 h-5"></x-icon>
            </div>
            <span class="flex-1 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $text }}</span>
            <button
                x-on:click="showToastr = false"
                type="button"
                class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors duration-200"
                aria-label="Close">
                <x-icon name="o-x-mark" class="w-5 h-5"></x-icon>
            </button>
        </div>
    </div>
@endif
