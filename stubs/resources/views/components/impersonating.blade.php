@impersonating
    <div class="bg-gradient-to-r from-red-600 to-red-700 dark:from-red-700 dark:to-red-800 py-3 text-white text-center shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-center gap-2 flex-wrap">
                @svg('lucide-circle-alert', 'w-5 h-5 text-white hidden md:inline-block')
                <span class="text-sm font-medium">{{ __('You\'re currently impersonating') }}</span>
                <span class="font-semibold">{{ auth()->user()->name }}</span>
                <a class="inline-flex items-center gap-1 text-sm font-semibold text-white hover:text-red-100 underline underline-offset-2 transition-colors cursor-pointer"
                   href="{{ route('impersonate.leave') }}">
                    {{ __('Leave Impersonation') }}
                    @svg('lucide-log-out', 'w-4 h-4')
                </a>
            </div>
        </div>
    </div>
@endImpersonating
