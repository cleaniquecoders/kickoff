<x-layouts.app :title="__('Documentation')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <x-icon name="book-open" class="inline-block h-8 w-8 mr-2" />
                {{ __('Documentation') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('Comprehensive guides and documentation for using the application') }}
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Getting Started -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900">
                        <x-icon name="rocket" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Getting Started') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Learn the basics and get up and running quickly with our step-by-step guide.') }}
                </p>
            </div>

            <!-- User Guide -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-green-100 p-2 dark:bg-green-900">
                        <x-icon name="user" class="h-6 w-6 text-green-600 dark:text-green-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('User Guide') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Detailed instructions on how to use all features of the application.') }}
                </p>
            </div>

            <!-- API Reference -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-purple-100 p-2 dark:bg-purple-900">
                        <x-icon name="code" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('API Reference') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Complete API documentation for developers and integrators.') }}
                </p>
            </div>

            <!-- Best Practices -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-yellow-100 p-2 dark:bg-yellow-900">
                        <x-icon name="lightbulb" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Best Practices') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Tips and tricks to get the most out of the application.') }}
                </p>
            </div>

            <!-- Troubleshooting -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-red-100 p-2 dark:bg-red-900">
                        <x-icon name="alert-circle" class="h-6 w-6 text-red-600 dark:text-red-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Troubleshooting') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Common issues and solutions to help you resolve problems quickly.') }}
                </p>
            </div>

            <!-- FAQs -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-3">
                    <div class="rounded-lg bg-indigo-100 p-2 dark:bg-indigo-900">
                        <x-icon name="help-circle" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('FAQs') }}</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Frequently asked questions and answers from our community.') }}
                </p>
            </div>
        </div>

        <!-- Additional Resources -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">{{ __('Additional Resources') }}</h2>
            <div class="space-y-3">
                <a href="#" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    <x-icon name="external-link" class="h-4 w-4" />
                    <span>{{ __('Video Tutorials') }}</span>
                </a>
                <a href="#" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    <x-icon name="external-link" class="h-4 w-4" />
                    <span>{{ __('Community Forum') }}</span>
                </a>
                <a href="#" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    <x-icon name="external-link" class="h-4 w-4" />
                    <span>{{ __('Blog Articles') }}</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
