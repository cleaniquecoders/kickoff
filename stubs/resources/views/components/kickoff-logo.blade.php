@props(['class' => 'h-8 w-8'])

{{-- Kickoff Logo - A stylized "K" with upward momentum --}}
<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    {{-- Background circle with gradient --}}
    <defs>
        <linearGradient id="kickoff-gradient" x1="0%" y1="100%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#2563eb"/>
            <stop offset="100%" style="stop-color:#06b6d4"/>
        </linearGradient>
    </defs>
    <rect width="48" height="48" rx="12" fill="url(#kickoff-gradient)"/>
    {{-- Stylized "K" letter with upward arrow motif --}}
    <path d="M16 12V36M16 24L28 12M16 24L32 36" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
    {{-- Upward spark/momentum indicator --}}
    <path d="M32 12L32 18M32 12L28 16M32 12L36 16" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
