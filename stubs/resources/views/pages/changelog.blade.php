<x-layouts.app :title="__('Changelog')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <x-icon name="newspaper" class="inline-block h-8 w-8 mr-2" />
                {{ __('Changelog') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('Stay up to date with the latest updates, improvements, and bug fixes') }}
            </p>
        </div>

        <!-- Latest Version -->
        <div class="rounded-lg border-2 border-blue-200 bg-blue-50 p-6 shadow-sm dark:border-blue-800 dark:bg-blue-900/20">
            <div class="mb-4 flex items-start justify-between">
                <div>
                    <div class="mb-2 flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Version 1.0.0</h2>
                        <span class="inline-flex items-center rounded-full bg-blue-600 px-2.5 py-0.5 text-xs font-medium text-white">
                            {{ __('Latest') }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Released on November 8, 2025') }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <!-- New Features -->
                <div>
                    <h3 class="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                        <x-icon name="sparkles" class="h-5 w-5 text-green-600 dark:text-green-400" />
                        {{ __('New Features') }}
                    </h3>
                    <ul class="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                        <li>• {{ __('Added static pages for Documentation, Support, and Changelog') }}</li>
                        <li>• {{ __('Enhanced sidebar navigation with quick action menu items') }}</li>
                        <li>• {{ __('Implemented responsive design for all static pages') }}</li>
                    </ul>
                </div>

                <!-- Improvements -->
                <div>
                    <h3 class="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                        <x-icon name="trending-up" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        {{ __('Improvements') }}
                    </h3>
                    <ul class="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                        <li>• {{ __('Improved menu builder architecture with better separation of concerns') }}</li>
                        <li>• {{ __('Enhanced user interface with consistent styling across pages') }}</li>
                        <li>• {{ __('Optimized page load performance') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Previous Versions -->
        <div class="space-y-4">
            <!-- Version 0.9.0 -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4">
                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Version 0.9.0</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Released on October 15, 2025') }}</p>
                </div>

                <div class="space-y-4">
                    <!-- New Features -->
                    <div>
                        <h3 class="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                            <x-icon name="sparkles" class="h-5 w-5 text-green-600 dark:text-green-400" />
                            {{ __('New Features') }}
                        </h3>
                        <ul class="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <li>• {{ __('Initial release of the application') }}</li>
                            <li>• {{ __('User authentication and authorization system') }}</li>
                            <li>• {{ __('Dashboard with basic analytics') }}</li>
                            <li>• {{ __('Role-based access control') }}</li>
                        </ul>
                    </div>

                    <!-- Bug Fixes -->
                    <div>
                        <h3 class="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                            <x-icon name="wrench" class="h-5 w-5 text-red-600 dark:text-red-400" />
                            {{ __('Bug Fixes') }}
                        </h3>
                        <ul class="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <li>• {{ __('Fixed login redirect issues') }}</li>
                            <li>• {{ __('Resolved dark mode inconsistencies') }}</li>
                            <li>• {{ __('Corrected responsive layout issues on mobile devices') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Version 0.8.0 -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4">
                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Version 0.8.0</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Released on September 20, 2025') }}</p>
                </div>

                <div class="space-y-4">
                    <!-- Improvements -->
                    <div>
                        <h3 class="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                            <x-icon name="trending-up" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                            {{ __('Improvements') }}
                        </h3>
                        <ul class="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <li>• {{ __('Beta release for testing') }}</li>
                            <li>• {{ __('Performance optimizations') }}</li>
                            <li>• {{ __('UI/UX refinements based on feedback') }}</li>
                        </ul>
                    </div>

                    <!-- Known Issues -->
                    <div>
                        <h3 class="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                            <x-icon name="alert-triangle" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                            {{ __('Known Issues') }}
                        </h3>
                        <ul class="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <li>• {{ __('Some features still in development') }}</li>
                            <li>• {{ __('Documentation incomplete') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Change Types') }}</h2>
            <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-4">
                <div class="flex items-center gap-2">
                    <x-icon name="sparkles" class="h-5 w-5 text-green-600 dark:text-green-400" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('New Features') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="trending-up" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Improvements') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="wrench" class="h-5 w-5 text-red-600 dark:text-red-400" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Bug Fixes') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-icon name="alert-triangle" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Known Issues') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
