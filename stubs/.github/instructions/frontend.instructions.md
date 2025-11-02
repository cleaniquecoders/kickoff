---
applyTo: '**'
---

# Temula Frontend Development Instructions

This document defines frontend development standards, patterns, and best practices for the Temula event management platform.

## Core Technology Stack

### Required Technologies (ONLY)

**JavaScript Framework:**
- **Alpine.js** - For reactive UI interactions and component behavior
- **Livewire 3.x** - For server-side rendering and reactive components
- **Axios** - For HTTP requests (when AJAX is needed outside Livewire)

**Styling:**
- **Tailwind CSS** - Utility-first CSS framework
- **Livewire Flux** - UI component library for consistent design

**Build Tools:**
- **Vite** - Asset bundling and compilation

**Translation:**
- **Laravel's `@lang()` directive** - For all user-facing text internationalization

### ❌ DO NOT USE

- React, Vue.js, Angular, or any other JavaScript frameworks
- jQuery
- Bootstrap or other CSS frameworks
- Inline JavaScript in Blade templates (use Alpine.js instead)
- Direct DOM manipulation (use Alpine.js or Livewire)

## Alpine.js Guidelines

### When to Use Alpine.js

Use Alpine.js for:
- Client-side interactivity (dropdowns, modals, toggles)
- Form validation feedback
- UI state management (tabs, accordions, tooltips)
- Simple animations and transitions
- Dark mode toggling
- Mobile menu interactions

### Alpine.js Patterns

**Component State Management:**

```blade
<div x-data="{ open: false, selected: null }">
    <button @click="open = !open">
        @lang('common.toggle')
    </button>

    <div x-show="open"
         x-transition
         @click.away="open = false">
        @lang('common.content')
    </div>
</div>
```

**Dark Mode Toggle:**

```blade
<div x-data="{
    darkMode: localStorage.getItem('theme') === 'dark',
    toggle() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', this.darkMode);
    }
}"
x-init="document.documentElement.classList.toggle('dark', darkMode)">
    <button @click="toggle()">
        <span x-show="!darkMode">🌙</span>
        <span x-show="darkMode">☀️</span>
    </button>
</div>
```

**Form Validation Feedback:**

```blade
<div x-data="{
    email: '',
    isValid: false,
    validate() {
        this.isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email);
    }
}">
    <input
        type="email"
        x-model="email"
        @input="validate()"
        :class="{ 'border-red-500': email && !isValid }"
        placeholder="@lang('forms.email')"
    >
    <p x-show="email && !isValid" class="text-red-500 text-sm">
        @lang('validation.email')
    </p>
</div>
```

**Tabs Component:**

```blade
<div x-data="{ activeTab: 'overview' }">
    <nav class="flex gap-4 border-b">
        <button
            @click="activeTab = 'overview'"
            :class="{ 'border-b-2 border-brand-600': activeTab === 'overview' }">
            @lang('events.tabs.overview')
        </button>
        <button
            @click="activeTab = 'tickets'"
            :class="{ 'border-b-2 border-brand-600': activeTab === 'tickets' }">
            @lang('events.tabs.tickets')
        </button>
    </nav>

    <div x-show="activeTab === 'overview'" x-transition>
        {{-- Overview content --}}
    </div>

    <div x-show="activeTab === 'tickets'" x-transition>
        {{-- Tickets content --}}
    </div>
</div>
```

### Alpine.js Best Practices

1. **Keep logic simple** - Complex logic belongs in Livewire components or backend
2. **Use `x-cloak`** to prevent flash of unstyled content
3. **Leverage `x-transition`** for smooth UI transitions
4. **Use `@click.away`** for closing dropdowns/modals
5. **Store persistent state** in `localStorage` when appropriate
6. **Initialize with `x-init`** for setup logic

## Livewire Guidelines

### When to Use Livewire

Use Livewire for:
- Server-side rendered components with state
- Form submissions and validation
- Real-time updates (polling, events)
- CRUD operations
- Complex business logic
- Database interactions
- File uploads

### Livewire Component Structure

**Full Class Component (`app/Livewire/`):**

```php
<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Events')]
class EventsList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.events.events-list', [
            'events' => Event::query()
                ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->when($this->status, fn($q) => $q->where('status', $this->status))
                ->paginate(12),
        ]);
    }
}
```

**Volt Component (`resources/views/livewire/`):**

```blade
<?php

use App\Models\Event;
use Livewire\Volt\Component;

new class extends Component {
    public string $search = '';

    public function with(): array
    {
        return [
            'events' => Event::where('title', 'like', "%{$this->search}%")
                ->latest()
                ->take(10)
                ->get(),
        ];
    }
}; ?>

<div>
    <input
        type="text"
        wire:model.live.debounce.300ms="search"
        placeholder="@lang('common.search')"
    >

    <div wire:loading.delay>
        @lang('common.loading')
    </div>

    @foreach($events as $event)
        <div>{{ $event->title }}</div>
    @endforeach
</div>
```

### Livewire Best Practices

1. **Use `wire:model.live.debounce`** for search inputs to reduce server requests
2. **Show loading states** with `wire:loading` for better UX
3. **Implement `wire:loading.delay`** to prevent flickering on fast responses
4. **Use `wire:poll`** sparingly - only for real-time data
5. **Validate on the server** - never trust client-side validation alone
6. **Use events** for component communication: `$dispatch()` and `@eventName`
7. **Optimize with lazy loading** using `wire:init` for heavy components
8. **Cache computed properties** with Livewire's computed properties

### Livewire + Alpine.js Integration

```blade
<div x-data="{ confirmDelete: false }">
    <button @click="confirmDelete = true">
        @lang('common.delete')
    </button>

    {{-- Confirmation Modal --}}
    <div x-show="confirmDelete"
         x-transition
         @click.away="confirmDelete = false">
        <p>@lang('events.confirm_delete')</p>
        <button wire:click="delete" @click="confirmDelete = false">
            @lang('common.confirm')
        </button>
        <button @click="confirmDelete = false">
            @lang('common.cancel')
        </button>
    </div>
</div>
```

## Axios Guidelines

### When to Use Axios

Use Axios for:
- External API calls (not Livewire endpoints)
- File downloads
- Custom AJAX requests outside Livewire lifecycle
- Third-party integrations (payment gateways, analytics)

### Axios Patterns

**Basic Request:**

```javascript
import axios from 'axios';

axios.get('/api/events')
    .then(response => {
        console.log(response.data);
    })
    .catch(error => {
        console.error(error);
    });
```

**With CSRF Token:**

```javascript
// Set default CSRF token (in app.js)
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Make request
axios.post('/api/events', {
    title: 'New Event',
    status: 'draft'
})
.then(response => {
    // Handle success
})
.catch(error => {
    // Handle error
});
```

**File Download:**

```javascript
axios.get('/api/events/export', { responseType: 'blob' })
    .then(response => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'events.csv');
        document.body.appendChild(link);
        link.click();
        link.remove();
    });
```

### Axios Best Practices

1. **Set defaults in `resources/js/app.js`** for CSRF token and headers
2. **Use interceptors** for global error handling
3. **Handle loading states** in Alpine.js or Livewire
4. **Prefer Livewire** over Axios for internal API calls
5. **Use async/await** for cleaner code

## Translation Guidelines

### Using @lang() Directive

**ALWAYS use `@lang()` for user-facing text:**

```blade
{{-- ✅ CORRECT --}}
<h1>@lang('events.title')</h1>
<button>@lang('common.save')</button>
<p>@lang('events.description')</p>

{{-- ❌ WRONG --}}
<h1>Events</h1>
<button>Save</button>
<p>Event description</p>
```

**With Parameters:**

```blade
<p>@lang('events.attendees_count', ['count' => $event->attendees_count])</p>
<p>@lang('common.welcome', ['name' => $user->name])</p>
```

**In Alpine.js:**

```blade
<div x-data="{
    message: '@lang('common.confirm_action')',
    confirm() {
        if (confirm(this.message)) {
            // Action
        }
    }
}">
```

**In JavaScript:**

```javascript
// Pass translations from Blade to JS
<script>
    const translations = {
        confirm: '@lang('common.confirm')',
        cancel: '@lang('common.cancel')',
        success: '@lang('common.success')',
    };
</script>
```

**Choice Translations:**

```blade
@lang('events.tickets_available', ['count' => $tickets])
```

```php
// In lang/en/events.php
'tickets_available' => '{0} No tickets|{1} One ticket|[2,*] :count tickets',
```

### Translation File Organization

```
lang/
├── en/
│   ├── auth.php
│   ├── common.php
│   ├── events.php
│   ├── tickets.php
│   ├── validation.php
│   └── ...
└── ms/
    ├── auth.php
    ├── common.php
    ├── events.php
    └── ...
```

### Translation Best Practices

1. **Use dot notation** for nested keys: `events.create.title`
2. **Group by feature** not by page: `events.php`, `tickets.php`, not `dashboard.php`
3. **Use `common.php`** for shared strings: buttons, actions, status labels
4. **Provide context** in key names: `events.empty_state` not `no_data`
5. **Use parameters** for dynamic content
6. **Keep keys semantic** not literal: `events.create_success` not `event_created_message`

## Tailwind CSS Guidelines

### Utility-First Approach

**✅ CORRECT:**

```blade
<div class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-100 dark:bg-slate-900 dark:ring-slate-800">
    <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">
        @lang('events.title')
    </h2>
</div>
```

**❌ WRONG:**

```blade
<style>
.custom-card {
    border-radius: 1rem;
    background: white;
    padding: 1.5rem;
}
</style>

<div class="custom-card">
    <h2>Events</h2>
</div>
```

### Responsive Design

**Mobile-first approach (required for all components):**

Temula must work seamlessly across all device sizes:
- **Mobile**: 320px - 639px (sm breakpoint)
- **Tablet**: 640px - 1023px (sm to lg breakpoints)
- **Desktop**: 1024px+ (lg breakpoint and above)

**Tailwind Breakpoints:**

```
sm: 640px   (tablet portrait)
md: 768px   (tablet landscape)
lg: 1024px  (desktop)
xl: 1280px  (large desktop)
2xl: 1536px (extra large)
```

**Grid Layouts:**

```blade
{{-- Mobile: 1 col, Tablet: 2 cols, Desktop: 3 cols --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
    {{-- Content --}}
</div>

{{-- Mobile: 1 col, Tablet: 2 cols, Desktop: 4 cols --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
    {{-- Cards --}}
</div>
```

**Responsive Typography:**

```blade
{{-- Mobile: text-2xl, Tablet: text-3xl, Desktop: text-4xl --}}
<h1 class="text-2xl md:text-3xl lg:text-4xl font-bold">
    @lang('events.title')
</h1>

{{-- Mobile: text-sm, Desktop: text-base --}}
<p class="text-sm lg:text-base text-slate-600">
    @lang('events.description')
</p>
```

**Responsive Spacing:**

```blade
{{-- Mobile: px-4, Tablet: px-6, Desktop: px-8 --}}
<div class="px-4 sm:px-6 lg:px-8 py-6 lg:py-12">
    @lang('content.body')
</div>

{{-- Mobile: gap-4, Desktop: gap-6 --}}
<div class="flex flex-col gap-4 lg:gap-6">
    {{-- Items --}}
</div>
```

**Responsive Buttons:**

```blade
{{-- Mobile: small, Desktop: normal size --}}
<button class="px-3 py-2 text-sm md:px-5 md:py-3 md:text-base rounded-lg bg-brand-600 text-white">
    @lang('common.submit')
</button>
```

**Mobile Navigation:**

```blade
<nav x-data="{ mobileMenuOpen: false }">
    {{-- Mobile menu button (hidden on desktop) --}}
    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden">
        <flux:icon.menu x-show="!mobileMenuOpen" />
        <flux:icon.x x-show="mobileMenuOpen" />
    </button>

    {{-- Desktop menu (hidden on mobile) --}}
    <div class="hidden lg:flex lg:gap-6">
        <a href="{{ route('events.index') }}">@lang('navigation.events')</a>
        <a href="{{ route('tickets.index') }}">@lang('navigation.tickets')</a>
    </div>

    {{-- Mobile menu (shown when open) --}}
    <div x-show="mobileMenuOpen"
         x-transition
         class="lg:hidden fixed inset-0 z-50 bg-white dark:bg-slate-900">
        <a href="{{ route('events.index') }}" class="block px-4 py-3">
            @lang('navigation.events')
        </a>
        <a href="{{ route('tickets.index') }}" class="block px-4 py-3">
            @lang('navigation.tickets')
        </a>
    </div>
</nav>
```

**Responsive Tables:**

```blade
{{-- Desktop: table, Mobile: stacked cards --}}
<div class="hidden lg:block">
    {{-- Standard table for desktop --}}
    <table class="w-full">
        <thead>
            <tr>
                <th>@lang('events.name')</th>
                <th>@lang('events.date')</th>
                <th>@lang('events.status')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                <tr>
                    <td>{{ $event->name }}</td>
                    <td>{{ $event->start_date->format('M d, Y') }}</td>
                    <td>{{ $event->status->label() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Mobile: card layout --}}
<div class="lg:hidden space-y-4">
    @foreach($events as $event)
        <div class="rounded-lg border border-slate-200 p-4 dark:border-slate-800">
            <h3 class="font-semibold">{{ $event->name }}</h3>
            <p class="text-sm text-slate-600">{{ $event->start_date->format('M d, Y') }}</p>
            <flux:badge variant="success">{{ $event->status->label() }}</flux:badge>
        </div>
    @endforeach
</div>
```

**Responsive Images:**

```blade
{{-- Adjust image size based on screen --}}
<img
    src="{{ $event->image_url }}"
    alt="{{ $event->name }}"
    class="w-full h-48 sm:h-64 lg:h-80 object-cover rounded-lg"
    loading="lazy"
>
```

### Dark Mode

**Dark mode is mandatory for all UI components.**

Temula provides light and dark mode support using Tailwind's `dark:` variants. The theme preference is stored in `localStorage` and applied via Alpine.js.

**Dark Mode Toggle Component:**

Create a reusable component at `resources/views/components/theme-toggle.blade.php`:

```blade
<div x-data="{
    darkMode: localStorage.getItem('theme') === 'dark' ||
              (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    init() {
        this.$watch('darkMode', value => {
            localStorage.setItem('theme', value ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', value);
        });
        document.documentElement.classList.toggle('dark', this.darkMode);
    }
}"
x-init="init()">
    <button
        @click="darkMode = !darkMode"
        class="rounded-lg p-2 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
        :aria-label="darkMode ? '@lang('theme.switch_to_light')' : '@lang('theme.switch_to_dark')'"
    >
        {{-- Sun icon (show in dark mode) --}}
        <flux:icon.sun
            x-show="darkMode"
            x-transition
            class="w-5 h-5 text-amber-500"
        />

        {{-- Moon icon (show in light mode) --}}
        <flux:icon.moon
            x-show="!darkMode"
            x-transition
            class="w-5 h-5 text-slate-700"
        />
    </button>
</div>
```

**Usage:**

```blade
{{-- In navigation or header --}}
<nav class="flex items-center justify-between">
    <div>@lang('app.name')</div>
    <x-theme-toggle />
</nav>
```

**Dark Mode Patterns:**

```blade
{{-- Background and text colors --}}
<div class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
    @lang('content.text')
</div>

{{-- Borders --}}
<div class="border border-slate-200 dark:border-slate-800">
    @lang('content.text')
</div>

{{-- Cards --}}
<div class="rounded-xl bg-white dark:bg-slate-900 shadow-lg ring-1 ring-slate-100 dark:ring-slate-800">
    @lang('card.content')
</div>

{{-- Buttons (maintain brand colors in both modes) --}}
<button class="bg-brand-600 hover:bg-brand-700 text-white dark:bg-brand-600 dark:hover:bg-brand-700">
    @lang('common.action')
</button>

{{-- Secondary buttons --}}
<button class="border border-slate-300 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
    @lang('common.cancel')
</button>

{{-- Input fields --}}
<input
    type="text"
    class="rounded-lg border border-slate-300 bg-white text-slate-900 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
    placeholder="@lang('forms.placeholder')"
>

{{-- Icons with adaptive colors --}}
<flux:icon.calendar class="w-5 h-5 text-slate-600 dark:text-slate-400" />
```

**Dark Mode Best Practices:**

1. **Test both modes** - Always preview in light and dark mode
2. **Maintain contrast** - Ensure WCAG AA compliance in both modes
3. **Use semantic colors** - Let Tailwind handle color inversion
4. **Avoid absolute colors** - Use slate-* instead of gray-*, use brand-* for primary colors
5. **Images** - Provide dark mode alternatives where needed
6. **System preference** - Initialize based on `prefers-color-scheme` media query
7. **Persistence** - Store preference in `localStorage` for consistency

**Dark Mode Color Guidance:**

```
Light Mode             Dark Mode
-----------            -----------
bg-white          →    dark:bg-slate-900
bg-slate-50       →    dark:bg-slate-950
text-slate-900    →    dark:text-white
text-slate-600    →    dark:text-slate-300
border-slate-200  →    dark:border-slate-800
ring-slate-100    →    dark:ring-slate-800
```

### Component Classes (Reusable Patterns)

**Buttons:**

```blade
{{-- Primary --}}
<button class="rounded-lg bg-brand-600 px-5 py-3 font-semibold text-white hover:bg-brand-700 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
    @lang('common.submit')
</button>

{{-- Secondary --}}
<button class="rounded-lg border border-slate-300 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
    @lang('common.cancel')
````
```

### Dark Mode

**Always include dark mode variants:**

```blade
<div class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
    <button class="bg-brand-600 hover:bg-brand-700 text-white dark:bg-brand-600 dark:hover:bg-brand-700">
        @lang('common.action')
    </button>
</div>
```

### Component Classes (Reusable Patterns)

**Buttons:**

```blade
{{-- Primary --}}
<button class="rounded-lg bg-brand-600 px-5 py-3 font-semibold text-white hover:bg-brand-700 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
    @lang('common.submit')
</button>

{{-- Secondary --}}
<button class="rounded-lg border border-slate-300 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
    @lang('common.cancel')
</button>
```

**Cards:**

```blade
<div class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-100 dark:bg-slate-900 dark:ring-slate-800">
    @lang('events.card_content')
</div>
```

**Form Inputs:**

```blade
<input
    type="text"
    class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900"
    placeholder="@lang('forms.placeholder')"
>
```

## Livewire Flux Components

### Using Flux Components

**Prefer Flux components over custom HTML:**

```blade
{{-- ✅ CORRECT --}}
<flux:button>@lang('common.submit')</flux:button>
<flux:card>@lang('events.content')</flux:card>
<flux:input type="text" placeholder="@lang('forms.email')" />
<flux:badge variant="success">@lang('status.active')</flux:badge>

{{-- ❌ AVOID (unless customization needed) --}}
<button class="...">Submit</button>
<div class="...">Content</div>
```

### Available Flux Components

- `<flux:button>` - Buttons with variants
- `<flux:card>` - Content cards
- `<flux:input>` - Form inputs
- `<flux:badge>` - Status badges
- `<flux:icon>` - Lucide icons (must be imported first)
- `<flux:modal>` - Modals and dialogs
- `<flux:dropdown>` - Dropdown menus
- `<flux:table>` - Data tables

### Icons with Flux

Temula uses **Lucide icons** (https://lucide.dev/icons/) via Flux.

**Before using an icon, import it:**

```bash
php artisan flux:icon <icon-name>
```

**Examples:**

```bash
# Import commonly used icons
php artisan flux:icon calendar
php artisan flux:icon users
php artisan flux:icon check-circle
php artisan flux:icon alert-triangle
php artisan flux:icon x
```

**Usage in Blade:**

```blade
{{-- Basic icon --}}
<flux:icon.calendar />

{{-- Icon with classes --}}
<flux:icon.check-circle class="w-5 h-5 text-green-500" />

{{-- Icon in button --}}
<flux:button>
    <flux:icon.plus class="w-4 h-4 mr-2" />
    @lang('common.add')
</flux:button>

{{-- Icon with Alpine.js state --}}
<div x-data="{ open: false }">
    <flux:icon.chevron-down x-show="!open" />
    <flux:icon.chevron-up x-show="open" />
</div>
```

**Icon naming convention:**
- Lucide uses kebab-case: `check-circle`, `alert-triangle`, `chevron-down`
- Flux converts to dot notation: `flux:icon.check-circle`, `flux:icon.alert-triangle`

**Best practices:**
1. Import icons as needed (don't import entire library)
2. Use semantic icon names that match their purpose
3. Apply consistent sizing (`w-4 h-4`, `w-5 h-5`, `w-6 h-6`)
4. Use color classes that match the design system
5. Browse available icons at https://lucide.dev/icons/

### Flux + Translation

```blade
<flux:button>@lang('common.submit')</flux:button>

<flux:badge variant="success">
    {{ $event->status->label() }}
</flux:badge>

<flux:input
    type="email"
    placeholder="@lang('forms.email')"
    wire:model="email"
/>
```

## File Structure

### JavaScript Files

```
resources/js/
├── app.js          # Main entry point, Alpine.js, Axios setup
├── bootstrap.js    # Laravel Echo, Axios defaults
└── components/     # Reusable Alpine components (if needed)
```

### CSS Files

```
resources/css/
├── app.css         # Main Tailwind imports
└── custom.css      # Rare custom styles (avoid)
```

### Livewire Components

```
app/Livewire/
├── Events/
│   ├── EventsList.php
│   ├── CreateEvent.php
│   └── EventDetails.php
├── Tickets/
│   ├── TicketManagement.php
│   └── TicketAnalytics.php
└── ...
```

### Volt Components

```
resources/views/livewire/
├── events/
│   ├── quick-stats.blade.php
│   └── recent-events.blade.php
├── dashboard/
│   └── dashboard-stats.blade.php
└── ...
```

## Performance Best Practices

### Optimize Livewire

1. **Use `wire:key`** in loops to prevent re-rendering issues
2. **Lazy load** heavy components with `wire:init`
3. **Debounce inputs** with `wire:model.live.debounce.300ms`
4. **Use `wire:loading`** for better UX
5. **Paginate large datasets** with `WithPagination`
6. **Cache queries** when possible

### Optimize Alpine.js

1. **Keep `x-data` scope small** - avoid global state
2. **Use `x-cloak`** to prevent flash of unstyled content
3. **Minimize watchers** - use `x-effect` sparingly
4. **Leverage `x-show` vs `x-if`** appropriately

### Optimize Assets

1. **Use Vite for bundling** - configured in `vite.config.js`
2. **Tree-shake unused Tailwind** - automatic with Vite
3. **Lazy load images** with `loading="lazy"`
4. **Minimize custom JavaScript** - prefer Alpine.js and Livewire

## Accessibility Guidelines

### Semantic HTML

```blade
<nav aria-label="@lang('navigation.main')">
    <ul>
        <li><a href="{{ route('events.index') }}">@lang('navigation.events')</a></li>
    </ul>
</nav>

<main>
    <h1>@lang('events.title')</h1>
</main>
```

### ARIA Attributes

```blade
<button
    aria-label="@lang('common.close')"
    aria-expanded="false"
    x-bind:aria-expanded="open.toString()">
    <flux:icon name="x" />
</button>

<div role="alert" aria-live="polite">
    @lang('messages.success')
</div>
```

### Keyboard Navigation

```blade
<div x-data="{ open: false }"
     @keydown.escape.window="open = false"
     @keydown.tab="// handle tab">
    {{-- Content --}}
</div>
```

### Focus Management

```blade
<button class="focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:outline-none">
    @lang('common.action')
</button>
```

## Error Handling

### Livewire Validation

```php
use Livewire\Attributes\Validate;

class CreateEvent extends Component
{
    #[Validate('required|min:3')]
    public string $title = '';

    #[Validate('required|date|after:today')]
    public string $start_date = '';

    public function save()
    {
        $this->validate();

        // Save logic

        session()->flash('success', __('events.created_successfully'));
    }
}
```

```blade
<form wire:submit="save">
    <div>
        <input type="text" wire:model="title">
        @error('title')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit">@lang('common.save')</button>
</form>
```

### Alpine.js Error States

```blade
<div x-data="{
    errors: [],
    hasError(field) {
        return this.errors.includes(field);
    }
}">
    <input
        type="email"
        :class="{ 'border-red-500': hasError('email') }"
    >
</div>
```

## Testing Guidelines

### Component Testing

```php
use Livewire\Livewire;

it('can create event', function () {
    Livewire::test(CreateEvent::class)
        ->set('title', 'Test Event')
        ->set('start_date', now()->addDays(7))
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('events.index'));
});
```

## Code Quality Checklist

Before committing frontend code:

- [ ] All user-facing text uses `@lang()` directive
- [ ] No inline JavaScript (use Alpine.js)
- [ ] Tailwind classes for all styling
- [ ] Dark mode variants included for all components
- [ ] Responsive design implemented (mobile, tablet, desktop)
- [ ] Mobile navigation/menus work properly
- [ ] Tables adapt to mobile (cards or horizontal scroll)
- [ ] Images have responsive sizing
- [ ] Typography scales appropriately
- [ ] Dark mode toggle component available
- [ ] System preference respected for initial theme
- [ ] Livewire loading states implemented
- [ ] Form validation on server-side
- [ ] Accessibility attributes present
- [ ] Alpine.js used for client-side interactivity
- [ ] Axios used only for external APIs
- [ ] Icons imported with `php artisan flux:icon <icon-name>` before use
- [ ] No console.log statements
- [ ] Proper error handling
- [ ] Translation keys are semantic

## Common Patterns Reference

### Search with Debounce

```blade
<input
    type="text"
    wire:model.live.debounce.300ms="search"
    placeholder="@lang('common.search')"
>
```

### Modal Pattern

```blade
<div x-data="{ open: false }">
    <button @click="open = true">@lang('common.open')</button>

    <div x-show="open"
         x-transition
         @click.away="open = false"
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="rounded-xl bg-white p-6 dark:bg-slate-900">
            @lang('modal.content')
            <button @click="open = false">@lang('common.close')</button>
        </div>
    </div>
</div>
```

### Confirmation Pattern

```blade
<button
    wire:click="delete"
    wire:confirm="@lang('events.confirm_delete')">
    @lang('common.delete')
</button>
```

### Polling Pattern

```blade
<div wire:poll.5s>
    @lang('stats.updated'): {{ $stats->updated_at }}
</div>
```

## Summary

Following these standards ensures:
- ✅ Consistent use of Alpine.js, Livewire, Tailwind CSS, and Axios
- ✅ All text is translatable with `@lang()`
- ✅ Enum values displayed using `->label()` method
- ✅ Server-side validation and security
- ✅ Fully responsive design (mobile, tablet, desktop)
- ✅ Complete dark mode support with toggle component
- ✅ Accessible UI with WCAG compliance
- ✅ Maintainable and testable code
- ✅ Optimal performance and UX
