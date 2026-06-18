@props(['class' => 'h-8 w-8'])

{{-- Kickoff logo — a clean, bold "K" in a gradient squircle. --}}
<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="kickoff-gradient" x1="0%" y1="100%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#2563eb"/>
            <stop offset="100%" style="stop-color:#06b6d4"/>
        </linearGradient>
    </defs>
    <rect width="48" height="48" rx="13" fill="url(#kickoff-gradient)"/>
    <path d="M17 13V35M17 24L31 13M17 24L31 35" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
