<div class="flex-shrink-0 flex items-center px-4">
    <a href="{{ auth()->user() ? route('dashboard') : url('/') }}"
       class="flex items-center gap-2 font-bold text-lg text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">
        @if (file_exists(public_path('storage/logo.png')))
            <img class="h-8 w-auto" src="{{ url('storage/logo.png') }}" alt="{{ config('app.name') }}">
        @else
            <x-kickoff-logo class="h-8 w-8" />
            <span>{{ config('app.name') }}</span>
        @endif
    </a>
</div>
