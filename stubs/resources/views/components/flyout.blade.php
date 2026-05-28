@props([
    'name',
    'size' => 'default',
])

@php
$sizeClass = match ($size) {
    'sm' => 'md:!w-[28rem] lg:!w-[32rem]',
    'lg' => 'md:!w-[52rem] lg:!w-[64rem]',
    'xl' => 'md:!w-[64rem] lg:!w-[80rem]',
    default => 'md:!w-[42rem] lg:!w-[52rem]',
};
@endphp

<flux:modal
    :name="$name"
    variant="flyout"
    {{ $attributes->class($sizeClass) }}
>
    {{ $slot }}
</flux:modal>
