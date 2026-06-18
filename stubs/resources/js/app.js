import './bootstrap';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

tippy('[data-tippy-content]');

// Tooltips on elements rendered after wire:navigate (skip already-initialized ones)
document.addEventListener('livewire:navigated', () => {
    tippy(Array.from(document.querySelectorAll('[data-tippy-content]')).filter((el) => !el._tippy));
});

// Sidebar collapse state — mirrored in a plain cookie so Blade can render the
// collapsed rail server-side under wire:navigate without flicker.
document.addEventListener('alpine:init', () => {
    Alpine.store('sidebar', {
        collapsed: /(?:^|;\s*)sidebar_collapsed=1/.test(document.cookie),
        toggle() {
            this.collapsed = !this.collapsed;
            document.cookie = `sidebar_collapsed=${this.collapsed ? 1 : 0};path=/;max-age=31536000;SameSite=Lax`;
        },
    });
});
