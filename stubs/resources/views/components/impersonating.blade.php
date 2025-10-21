@impersonating
    <div class="bg-gradient-to-r from-red-600 to-red-700 dark:from-red-700 dark:to-red-800 py-3 text-white text-center shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-center gap-2 flex-wrap">
                <x-icon name="o-exclamation-circle" class="w-5 h-5 text-white hidden md:inline-block"></x-icon>
                <span class="text-sm font-medium">{{ __('You\'re currently impersonating') }}</span>
                <span class="font-semibold">{{ auth()->user()->name }}</span>
                <a class="inline-flex items-center gap-1 text-sm font-semibold text-white hover:text-red-100 underline underline-offset-2 transition-colors"
                   href="{{ route('impersonate.leave') }}">
                    {{ __('Leave Impersonation') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endImpersonating
