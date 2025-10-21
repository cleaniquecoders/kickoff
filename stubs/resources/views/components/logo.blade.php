<div class="flex-shrink-0 flex items-center px-4">
    <a href="{{ auth()->user() ? route('dashboard') : url('/') }}"
       class="flex items-center justify-center font-bold text-lg text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200 w-full">
        @if (file_exists(public_path('storage/logo.png')))
            <img class="h-8 w-auto" src="{{ url('storage/logo.png') }}" alt="{{ config('app.name') }}">
        @else
            {{ config('app.name') }}
        @endif
    </a>
</div>
