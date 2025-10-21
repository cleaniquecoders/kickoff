@props([
    'name' => 'file',
    'label' => 'File',
    'accept' => false,
    'multiple' => false,
    'required' => false,
])

<div>
    <input wire:model.live="{{ $name }}"
        class="block w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-900 text-gray-900 dark:text-gray-100 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition-all duration-200"
        type="file"
        @if($accept) accept="{{ $accept }}" @endif
        @if($multiple) multiple @endif
        @if($required) required @endif
    >
    <x-input-error for="{{ $name }}" class="mt-2" />
</div>
