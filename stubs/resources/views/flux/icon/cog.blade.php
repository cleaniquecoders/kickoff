@pure

{{-- Credit: Lucide (https://lucide.dev) --}}

@props([
    'variant' => 'outline',
])

@php
if ($variant === 'solid') {
    throw new \Exception('The "solid" variant is not supported in Lucide.');
}

$classes = Flux::classes('shrink-0')
    ->add(match($variant) {
        'outline' => '[:where(&)]:size-6',
        'solid' => '[:where(&)]:size-6',
        'mini' => '[:where(&)]:size-5',
        'micro' => '[:where(&)]:size-4',
    });

$strokeWidth = match ($variant) {
    'outline' => 2,
    'mini' => 2.25,
    'micro' => 2.5,
};
@endphp

<svg
    {{ $attributes->class($classes) }}
    data-flux-icon
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $strokeWidth }}"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    data-slot="icon"
>
  <path d="M11 10.27 7 3.34" />
  <path d="m11 13.73-4 6.93" />
  <path d="M12 22v-2" />
  <path d="M12 2v2" />
  <path d="M14 12h8" />
  <path d="m17 20.66-1-1.73" />
  <path d="m17 3.34-1 1.73" />
  <path d="M2 12h2" />
  <path d="m20.66 17-1.73-1" />
  <path d="m20.66 7-1.73 1" />
  <path d="m3.34 17 1.73-1" />
  <path d="m3.34 7 1.73 1" />
  <circle cx="12" cy="12" r="2" />
  <circle cx="12" cy="12" r="8" />
</svg>
