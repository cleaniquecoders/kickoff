---
applyTo: '**'
---

# Frontend Development Instructions

This document defines frontend development standards, patterns, and best practices using Alpine.js, Livewire 3.x, and Tailwind CSS.

## Core Principles

1. **Progressive Enhancement**: Start with functional HTML, enhance with Alpine.js/Livewire
2. **Component-Based**: Build reusable Blade components
3. **Utility-First CSS**: Use Tailwind CSS classes directly
4. **Accessibility First**: ARIA labels, keyboard navigation, semantic HTML
5. **Mobile-First**: Design for mobile, enhance for desktop

## Tech Stack

- **UI Framework**: Tailwind CSS 4.x
- **Interactivity**: Alpine.js 3.x (for simple interactions)
- **Dynamic Components**: Livewire 3.x (for complex forms, lists)
- **Icons**: Blade Icons (multiple icon sets)
- **Forms**: Livewire form objects + Tailwind styling

## Blade Component Standards

### Component Structure

**Place components in `resources/views/components/`:**

```
resources/views/components/
├── alert.blade.php
├── button.blade.php
├── card.blade.php
├── form/
│   ├── input.blade.php
│   ├── select.blade.php
│   └── textarea.blade.php
└── layouts/
    ├── app.blade.php
    └── guest.blade.php
```

### Basic Component Template

```blade
{{-- resources/views/components/card.blade.php --}}
@props([
    'title' => null,
    'padding' => 'p-6',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow ' . $padding]) }}>
    @if($title)
        <h3 class="text-lg font-semibold mb-4">{{ $title }}</h3>
    @endif

    {{ $slot }}
</div>
```

**Usage:**

```blade
<x-card title="User Information" class="mb-4">
    <p>Content goes here</p>
</x-card>
```

### Form Components

#### Input Component

```blade
{{-- resources/views/components/form/input.blade.php --}}
@props([
    'label' => null,
    'name',
    'type' => 'text',
    'error' => null,
    'required' => false,
])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes->except('class')->merge([
            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'
        ]) }}
    >

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
```

**Usage:**

```blade
<x-form.input
    label="Name"
    name="name"
    required
    :error="$errors->first('name')"
/>
```

## Alpine.js Standards

### When to Use Alpine.js

Use Alpine for:
- Toggle visibility (dropdowns, modals)
- Form interactions (show/hide fields)
- Client-side validation feedback
- Simple state management
- UI animations/transitions

**Do NOT use Alpine for:**
- Server-side data fetching (use Livewire)
- Complex form submissions (use Livewire)
- Data persistence (use Livewire)

### Alpine Component Pattern

```blade
<div
    x-data="{
        open: false,
        selected: null
    }"
    class="relative"
>
    <!-- Trigger -->
    <button
        @click="open = !open"
        type="button"
        class="px-4 py-2 bg-blue-500 text-white rounded"
    >
        Toggle Menu
    </button>

    <!-- Dropdown -->
    <div
        x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute mt-2 w-48 bg-white rounded-md shadow-lg"
    >
        <a href="#" class="block px-4 py-2 hover:bg-gray-100">Item 1</a>
        <a href="#" class="block px-4 py-2 hover:bg-gray-100">Item 2</a>
    </div>
</div>
```

### Alpine Best Practices

**✅ DO:**
- Keep Alpine logic simple and readable
- Use `@click.prevent` to prevent form submission
- Use `@click.away` for closing dropdowns/modals
- Use `x-cloak` to prevent flash of unstyled content
- Use `x-transition` for smooth animations

**❌ DON'T:**
- Fetch data from server (use Livewire instead)
- Manage complex state (use Livewire instead)
- Perform calculations (do in backend/Livewire)

### Common Alpine Patterns

**Modal:**

```blade
<div x-data="{ open: false }">
    <!-- Trigger -->
    <button @click="open = true" class="btn-primary">
        Open Modal
    </button>

    <!-- Modal -->
    <div
        x-show="open"
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-cloak
    >
        <!-- Backdrop -->
        <div
            @click="open = false"
            class="fixed inset-0 bg-black bg-opacity-50"
        ></div>

        <!-- Modal Content -->
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                <h3 class="text-lg font-semibold mb-4">Modal Title</h3>
                <p class="text-gray-600 mb-4">Modal content goes here.</p>

                <button @click="open = false" class="btn-secondary">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
```

**Tabs:**

```blade
<div x-data="{ tab: 'tab1' }">
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button
                @click="tab = 'tab1'"
                :class="tab === 'tab1' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                class="border-b-2 py-4 px-1 font-medium"
            >
                Tab 1
            </button>
            <button
                @click="tab = 'tab2'"
                :class="tab === 'tab2' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                class="border-b-2 py-4 px-1 font-medium"
            >
                Tab 2
            </button>
        </nav>
    </div>

    <!-- Tab Panels -->
    <div class="mt-4">
        <div x-show="tab === 'tab1'">
            <p>Content for Tab 1</p>
        </div>
        <div x-show="tab === 'tab2'">
            <p>Content for Tab 2</p>
        </div>
    </div>
</div>
```

## Livewire 3.x Standards

### When to Use Livewire

Use Livewire for:
- Forms with server-side validation
- Data tables with sorting/filtering/pagination
- Dynamic lists (create/update/delete)
- Real-time updates
- Complex user interactions requiring server data

### Livewire Component Structure

```php
<?php

namespace App\Livewire\Resources;

use App\Models\Resource;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    // Query string bindings
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $resources = Resource::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.resources.index', [
            'resources' => $resources,
        ]);
    }
}
```

**Blade template:**

```blade
<div>
    <!-- Filters -->
    <div class="mb-6 flex gap-4">
        <x-form.input
            wire:model.live="search"
            placeholder="Search resources..."
            class="flex-1"
        />

        <select wire:model.live="status" class="rounded-md border-gray-300">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <!-- Results -->
    <div class="space-y-4">
        @forelse($resources as $resource)
            <x-card>
                <h3 class="font-semibold">{{ $resource->name }}</h3>
                <p class="text-sm text-gray-600">{{ $resource->status->label() }}</p>
            </x-card>
        @empty
            <p class="text-gray-500 text-center py-8">No resources found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $resources->links() }}
    </div>
</div>
```

### Livewire Form Objects

**Use Livewire Form objects for complex forms:**

```php
<?php

namespace App\Livewire\Forms;

use App\Enums\Status;
use App\Models\Resource;
use Livewire\Form;

class ResourceForm extends Form
{
    public ?Resource $resource = null;

    public string $name = '';
    public string $status = 'draft';
    public array $meta = [];

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:draft,active,inactive'],
        ];
    }

    public function setResource(Resource $resource): void
    {
        $this->resource = $resource;
        $this->name = $resource->name;
        $this->status = $resource->status->value;
    }

    public function save(): Resource
    {
        $this->validate();

        if ($this->resource) {
            $this->resource->update($this->all());
            return $this->resource;
        }

        return Resource::create($this->all());
    }
}
```

**Component using form object:**

```php
<?php

namespace App\Livewire\Resources;

use App\Livewire\Forms\ResourceForm;
use Livewire\Component;

class Create extends Component
{
    public ResourceForm $form;

    public function save()
    {
        $this->authorize('create', Resource::class);

        $resource = $this->form->save();

        session()->flash('success', 'Resource created successfully!');

        return redirect()->route('resources.show', $resource);
    }

    public function render()
    {
        return view('livewire.resources.create');
    }
}
```

### Livewire Best Practices

**✅ DO:**
- Use `wire:model.live` for real-time updates
- Use `wire:loading` to show loading states
- Reset pagination when filters change
- Use query strings for shareable URLs
- Validate on server-side always

**❌ DON'T:**
- Put business logic in Livewire components (use Actions)
- Fetch large datasets without pagination
- Use `wire:model.live` for every input (performance)
- Skip authorization checks

### Livewire Loading States

```blade
<div>
    <button
        wire:click="save"
        wire:loading.attr="disabled"
        class="btn-primary"
    >
        <span wire:loading.remove>Save</span>
        <span wire:loading>Saving...</span>
    </button>

    <div wire:loading class="mt-2 text-sm text-gray-600">
        Processing...
    </div>
</div>
```

## Tailwind CSS Standards

### Layout Patterns

**Container:**

```blade
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Content -->
</div>
```

**Grid Layout:**

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">Item 1</div>
    <div class="bg-white rounded-lg shadow p-6">Item 2</div>
    <div class="bg-white rounded-lg shadow p-6">Item 3</div>
</div>
```

**Responsive Stack:**

```blade
<div class="flex flex-col md:flex-row gap-4">
    <div class="md:w-1/3">Sidebar</div>
    <div class="md:w-2/3">Main content</div>
</div>
```

### Typography

```blade
<!-- Headings -->
<h1 class="text-3xl font-bold text-gray-900">Heading 1</h1>
<h2 class="text-2xl font-semibold text-gray-900">Heading 2</h2>
<h3 class="text-xl font-semibold text-gray-900">Heading 3</h3>

<!-- Body Text -->
<p class="text-base text-gray-700">Regular paragraph text.</p>
<p class="text-sm text-gray-600">Small text or captions.</p>
<p class="text-xs text-gray-500">Extra small text or labels.</p>
```

### Buttons

```blade
<!-- Primary Button -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    Primary Action
</button>

<!-- Secondary Button -->
<button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    Secondary Action
</button>

<!-- Danger Button -->
<button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
    Delete
</button>
```

**Create reusable button component:**

```blade
{{-- resources/views/components/button.blade.php --}}
@props([
    'variant' => 'primary', // primary, secondary, danger
    'type' => 'button',
])

@php
$classes = match($variant) {
    'primary' => 'px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
    'secondary' => 'px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
    'danger' => 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2',
};
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
```

### Form Elements

```blade
<!-- Text Input -->
<input
    type="text"
    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
>

<!-- Select -->
<select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    <option>Option 1</option>
    <option>Option 2</option>
</select>

<!-- Textarea -->
<textarea
    rows="4"
    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
></textarea>

<!-- Checkbox -->
<label class="flex items-center">
    <input
        type="checkbox"
        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
    >
    <span class="ml-2 text-sm text-gray-700">Remember me</span>
</label>
```

### Cards

```blade
<!-- Basic Card -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-2">Card Title</h3>
    <p class="text-gray-600">Card content goes here.</p>
</div>

<!-- Card with Header and Footer -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Card Header</h3>
    </div>
    <div class="px-6 py-4">
        <p class="text-gray-600">Card content goes here.</p>
    </div>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <button class="btn-primary">Action</button>
    </div>
</div>
```

### Status Badges

```blade
@php
$statusClasses = match($status) {
    'draft' => 'bg-gray-100 text-gray-800',
    'active' => 'bg-green-100 text-green-800',
    'inactive' => 'bg-red-100 text-red-800',
    default => 'bg-gray-100 text-gray-800',
};
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
    {{ $status }}
</span>
```

## Flash Messages

### Flash Message Component

```blade
{{-- resources/views/components/flash-message.blade.php --}}
@if(session('success'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        x-init="setTimeout(() => show = false, 5000)"
        class="mb-6 rounded-md bg-green-50 p-4"
    >
        <div class="flex">
            <div class="flex-shrink-0">
                <x-icon name="check-circle" class="h-5 w-5 text-green-400" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </p>
            </div>
            <div class="ml-auto pl-3">
                <button @click="show = false" class="text-green-500 hover:text-green-600">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        class="mb-6 rounded-md bg-red-50 p-4"
    >
        <div class="flex">
            <div class="flex-shrink-0">
                <x-icon name="x-circle" class="h-5 w-5 text-red-400" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    {{ session('error') }}
                </p>
            </div>
            <div class="ml-auto pl-3">
                <button @click="show = false" class="text-red-500 hover:text-red-600">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
@endif
```

## Blade Icons

### Icon Usage

```blade
<!-- Basic icon -->
<x-icon name="user" class="w-5 h-5" />

<!-- Icon with color -->
<x-icon name="check" class="w-5 h-5 text-green-500" />

<!-- Icon in button -->
<button class="btn-primary">
    <x-icon name="plus" class="w-4 h-4 mr-2" />
    Create New
</button>
```

### Common Icons

- `user` - User profile
- `cog` - Settings
- `check` - Success/confirmation
- `x` - Close/delete
- `pencil` - Edit
- `trash` - Delete
- `eye` - View
- `plus` - Add/create
- `search` - Search

## Accessibility Standards

### ARIA Labels

```blade
<!-- Button with icon only -->
<button aria-label="Delete item" class="text-red-600 hover:text-red-800">
    <x-icon name="trash" class="w-5 h-5" />
</button>

<!-- Form label -->
<label for="email" class="block text-sm font-medium text-gray-700">
    Email Address
</label>
<input
    id="email"
    type="email"
    name="email"
    aria-describedby="email-help"
    class="mt-1 block w-full"
>
<p id="email-help" class="mt-1 text-sm text-gray-500">
    We'll never share your email.
</p>
```

### Keyboard Navigation

```blade
<!-- Dropdown with keyboard support -->
<div x-data="{ open: false }">
    <button
        @click="open = !open"
        @keydown.escape="open = false"
        aria-expanded="false"
        :aria-expanded="open.toString()"
        class="btn-primary"
    >
        Options
    </button>

    <div
        x-show="open"
        @keydown.escape.window="open = false"
        role="menu"
    >
        <a href="#" role="menuitem">Item 1</a>
        <a href="#" role="menuitem">Item 2</a>
    </div>
</div>
```

## Performance Best Practices

### Lazy Loading

```blade
<!-- Lazy load images -->
<img
    src="placeholder.jpg"
    data-src="actual-image.jpg"
    loading="lazy"
    alt="Description"
>

<!-- Livewire lazy loading -->
<livewire:resource-list lazy />
```

### Defer JavaScript

```blade
@pushOnce('scripts')
    <script src="/js/app.js" defer></script>
@endPushOnce
```

## Testing Frontend

### Component Testing

```php
use function Pest\Livewire\livewire;

it('filters resources by search term', function () {
    $resource1 = Resource::factory()->create(['name' => 'Alpha Resource']);
    $resource2 = Resource::factory()->create(['name' => 'Beta Resource']);

    livewire(Resources\Index::class)
        ->set('search', 'Alpha')
        ->assertSee('Alpha Resource')
        ->assertDontSee('Beta Resource');
});

it('paginates resources', function () {
    Resource::factory()->count(20)->create();

    livewire(Resources\Index::class)
        ->assertSee('1') // Pagination links
        ->assertSee('2');
});
```

## Quick Reference

### Component Generation

```bash
# Blade component
php artisan make:component Card

# Livewire component
php artisan make:livewire Resources/Index

# Livewire form object
php artisan make:livewire-form ResourceForm
```

### Tailwind Customization

```js
// tailwind.config.js
export default {
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eff6ff',
                    // ... more shades
                    900: '#1e3a8a',
                },
            },
        },
    },
}
```

### Common Pitfalls

1. **Not using x-cloak** → Flash of unstyled Alpine components
2. **Using wire:model.live everywhere** → Performance issues
3. **Forgetting wire:loading** → No user feedback
4. **Inline styles** → Use Tailwind classes
5. **Missing ARIA labels** → Accessibility issues
6. **Not resetting page on filter change** → Confusing UX

## Summary

Following these standards ensures:
- ✅ Consistent UI/UX across application
- ✅ Accessible interfaces
- ✅ Performant interactions
- ✅ Maintainable components
- ✅ Mobile-responsive design
