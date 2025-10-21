<div
    class="fixed bottom-4 md:bottom-8 left-0 right-0 mx-auto px-4 md:px-0 md:left-auto md:right-8 w-full md:w-96 z-50 space-y-2"
    x-data="{ messages: [] }"
    x-init="
        Livewire.on('flashMessage', message => {
            let classType = '';
            let text = '';

            // Check if the message contains the '|' character
            if (message.includes('|')) {
                [classType, text] = message.split('|');
            } else {
                text = message; // Assign the entire message to text if no '|' is present
            }

            let messageId = Date.now(); // Unique identifier for each message
            messages.push({ id: messageId, class: classType, text: text, show: true });

            setTimeout(() => {
                // Remove the message with the specified id from the queue
                messages = messages.filter(msg => msg.id !== messageId);
            }, 7500);
        });
    "
>
    <template x-for="(message, index) in messages" :key="message.id">
        <div
            x-show="message.show"
            x-transition:enter="transform ease-out duration-300"
            x-transition:enter-start="translate-y-2 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transform ease-in duration-200"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-2 opacity-0"
            class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 shadow-lg rounded-xl py-3 px-4 flex items-center gap-3 backdrop-blur-sm"
        >
            <div class="bg-indigo-100 dark:bg-indigo-800 h-9 w-9 rounded-full inline-flex items-center justify-center flex-shrink-0">
                <x-icon name="o-exclamation-circle" class="text-indigo-600 dark:text-indigo-400 w-5 h-5"></x-icon>
            </div>

            <span class="flex-1 text-sm font-medium text-gray-900 dark:text-gray-100" x-text="message.text"></span>

            <button
                x-on:click="message.show = false"
                type="button"
                class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1.5 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800 transition-colors duration-200"
                aria-label="Close">
                <x-icon name="o-x-mark" class="w-5 h-5"></x-icon>
            </button>
        </div>
    </template>
</div>
