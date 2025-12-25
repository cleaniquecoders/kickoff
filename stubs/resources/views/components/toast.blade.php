<div
    x-data="{
        notices: [],
        visible: [],
        add(notice) {
            const id = Date.now();
            notice.id = id;
            this.notices.push(notice);
            this.visible.push(id);

            setTimeout(() => {
                this.remove(id);
            }, notice.duration || 3000);
        },
        remove(id) {
            const index = this.visible.indexOf(id);
            if (index > -1) {
                this.visible.splice(index, 1);
            }

            setTimeout(() => {
                const noticeIndex = this.notices.findIndex(n => n.id === id);
                if (noticeIndex > -1) {
                    this.notices.splice(noticeIndex, 1);
                }
            }, 300);
        }
    }"
    @toast.window="add($event.detail)"
    class="pointer-events-none fixed inset-x-0 bottom-0 z-50 px-4 pb-6 sm:px-6 sm:pb-5"
>
    <div class="mx-auto flex w-full max-w-sm flex-col items-center space-y-3 sm:max-w-md">
        <template x-for="notice in notices" :key="notice.id">
            <div
                x-show="visible.includes(notice.id)"
                x-transition:enter="transform ease-out duration-300 transition"
                x-transition:enter-start="translate-y-2 opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="pointer-events-auto w-full overflow-hidden rounded-lg border-2 shadow-xl bg-white dark:bg-zinc-800"
                :class="{
                    'border-green-600 dark:border-green-500 !bg-green-50 dark:!bg-green-900': notice.type === 'success',
                    'border-red-600 dark:border-red-500 !bg-red-50 dark:!bg-red-900': notice.type === 'error',
                    'border-yellow-600 dark:border-yellow-500 !bg-yellow-50 dark:!bg-yellow-900': notice.type === 'warning',
                    'border-blue-600 dark:border-blue-500 !bg-blue-50 dark:!bg-blue-900': notice.type === 'info',
                    'border-zinc-400 dark:border-zinc-600': !notice.type || !['success', 'error', 'warning', 'info'].includes(notice.type)
                }"
            >
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-6 w-6"
                                :class="{
                                    'text-green-700 dark:text-green-300': notice.type === 'success',
                                    'text-red-700 dark:text-red-300': notice.type === 'error',
                                    'text-yellow-700 dark:text-yellow-300': notice.type === 'warning',
                                    'text-blue-700 dark:text-blue-300': notice.type === 'info',
                                    'text-zinc-700 dark:text-zinc-300': !notice.type || !['success', 'error', 'warning', 'info'].includes(notice.type)
                                }"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                            >
                                <path x-show="notice.type === 'success'" stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path x-show="notice.type === 'error'" stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path x-show="notice.type === 'warning'" stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                <path x-show="notice.type === 'info' || !notice.type || !['success', 'error', 'warning', 'info'].includes(notice.type)" stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                        </div>

                        <div class="ml-3 flex-1">
                            <p
                                x-text="notice.message"
                                class="text-sm font-medium"
                                :class="{
                                    'text-green-800 dark:text-green-100': notice.type === 'success',
                                    'text-red-800 dark:text-red-100': notice.type === 'error',
                                    'text-yellow-800 dark:text-yellow-100': notice.type === 'warning',
                                    'text-blue-800 dark:text-blue-100': notice.type === 'info',
                                    'text-zinc-900 dark:text-zinc-100': !notice.type || !['success', 'error', 'warning', 'info'].includes(notice.type)
                                }"
                            ></p>
                        </div>

                        <div class="ml-4 flex flex-shrink-0">
                            <button
                                type="button"
                                @click="remove(notice.id)"
                                class="inline-flex rounded-md text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500"
                                :class="{
                                    '!text-green-700 dark:!text-green-300 hover:!text-green-900 dark:hover:!text-green-100 !focus:ring-green-500': notice.type === 'success',
                                    '!text-red-700 dark:!text-red-300 hover:!text-red-900 dark:hover:!text-red-100 !focus:ring-red-500': notice.type === 'error',
                                    '!text-yellow-700 dark:!text-yellow-300 hover:!text-yellow-900 dark:hover:!text-yellow-100 !focus:ring-yellow-500': notice.type === 'warning',
                                    '!text-blue-700 dark:!text-blue-300 hover:!text-blue-900 dark:hover:!text-blue-100 !focus:ring-blue-500': notice.type === 'info',
                                    '!text-zinc-600 dark:!text-zinc-400 hover:!text-zinc-900 dark:hover:!text-zinc-100 !focus:ring-zinc-500': !notice.type || !['success', 'error', 'warning', 'info'].includes(notice.type)
                                }"
                            >
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
