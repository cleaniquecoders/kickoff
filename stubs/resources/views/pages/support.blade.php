<x-layouts.app :title="__('Support')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <x-icon name="life-buoy" class="inline-block h-8 w-8 mr-2" />
                {{ __('Support') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('Get help and support for any issues you\'re experiencing') }}
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <!-- Contact Support -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900">
                        <x-icon name="mail" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Contact Support') }}</h3>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Reach out to our support team for personalized assistance.') }}
                </p>
                <a href="mailto:support@example.com" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    <x-icon name="mail" class="h-4 w-4" />
                    {{ __('Email Support') }}
                </a>
            </div>

            <!-- Live Chat -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-green-100 p-2 dark:bg-green-900">
                        <x-icon name="message-circle" class="h-6 w-6 text-green-600 dark:text-green-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Live Chat') }}</h3>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Get instant help from our support team during business hours.') }}
                </p>
                <button type="button" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
                    <x-icon name="message-circle" class="h-4 w-4" />
                    {{ __('Start Chat') }}
                </button>
            </div>

            <!-- Knowledge Base -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-purple-100 p-2 dark:bg-purple-900">
                        <x-icon name="book-open" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Knowledge Base') }}</h3>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Browse our extensive library of help articles and guides.') }}
                </p>
                <a href="#" class="inline-flex items-center gap-2 rounded-md bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600">
                    <x-icon name="book-open" class="h-4 w-4" />
                    {{ __('Browse Articles') }}
                </a>
            </div>

            <!-- Community Forum -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-yellow-100 p-2 dark:bg-yellow-900">
                        <x-icon name="users" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Community Forum') }}</h3>
                </div>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Connect with other users and share tips and solutions.') }}
                </p>
                <a href="#" class="inline-flex items-center gap-2 rounded-md bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600">
                    <x-icon name="users" class="h-4 w-4" />
                    {{ __('Visit Forum') }}
                </a>
            </div>
        </div>

        <!-- Support Hours -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">{{ __('Support Hours') }}</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <h4 class="mb-2 font-medium text-gray-900 dark:text-white">{{ __('Email Support') }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('24/7 - Response within 24 hours') }}</p>
                </div>
                <div>
                    <h4 class="mb-2 font-medium text-gray-900 dark:text-white">{{ __('Live Chat') }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Monday - Friday, 9:00 AM - 5:00 PM UTC') }}</p>
                </div>
            </div>
        </div>

        <!-- Submit a Ticket -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">{{ __('Submit a Support Ticket') }}</h2>
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Can\'t find what you\'re looking for? Submit a support ticket and we\'ll get back to you as soon as possible.') }}
            </p>
            <button type="button" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                <x-icon name="plus" class="h-4 w-4" />
                {{ __('Create Ticket') }}
            </button>
        </div>
    </div>
</x-layouts.app>
