@props([
    'name',
    'heading' => null,
    'subheading' => null,
    'variant' => 'default',
    'maxWidth' => '2xl',
    'size' => 'default',
])

@php
$isFlyout = $variant === 'flyout';

$modalSizeClass = match ($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
    default => 'sm:max-w-2xl',
};
@endphp

@if ($isFlyout)
    <x-flyout :name="$name" :size="$size" {{ $attributes }}>
        <div class="space-y-5">
            @if ($heading)
                <div class="space-y-1">
                    <flux:heading size="lg">{{ $heading }}</flux:heading>
                    @if ($subheading)
                        <flux:subheading>{{ $subheading }}</flux:subheading>
                    @endif
                </div>
            @endif

            {{ $slot }}
        </div>
    </x-flyout>
@else
    <flux:modal :name="$name" {{ $attributes->class($modalSizeClass) }}>
        <div class="space-y-5">
            @if ($heading)
                <div class="space-y-1">
                    <flux:heading size="lg">{{ $heading }}</flux:heading>
                    @if ($subheading)
                        <flux:subheading>{{ $subheading }}</flux:subheading>
                    @endif
                </div>
            @endif

            {{ $slot }}
        </div>
    </flux:modal>
@endif
