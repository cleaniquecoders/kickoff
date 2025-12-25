# Toast Notifications

A reusable toast notification component for displaying success, error, warning, and info messages at the bottom center of the screen.

## Features

- ✅ Auto-dismiss after 3 seconds (configurable)
- ✅ Manual dismiss with close button
- ✅ Multiple toasts stacking support
- ✅ 4 types: success, error, warning, info
- ✅ Dark mode support
- ✅ Smooth animations

## Usage

### In Livewire Components (Volt)

```php
<?php

use function Livewire\Volt\{state};

state(['data' => []]);

$save = function () {
    // Your save logic here...

    // Dispatch toast notification
    $this->dispatch('toast',
        type: 'success',
        message: 'Data saved successfully!',
        duration: 3000 // Optional, defaults to 3000ms
    );
};

?>

<div>
    <button wire:click="save">Save</button>
</div>
```

### In Regular Livewire Components

```php
class MyComponent extends Component
{
    public function save()
    {
        // Your save logic here...

        $this->dispatch('toast',
            type: 'success',
            message: 'Data saved successfully!',
            duration: 3000
        );
    }
}
```

### In Blade Templates (Alpine.js)

```html
<button @click="$dispatch('toast', { type: 'info', message: 'Hello from Alpine!' })">
    Show Toast
</button>
```

## Toast Types

### Success

```php
$this->dispatch('toast',
    type: 'success',
    message: 'Operation completed successfully!'
);
```

### Error

```php
$this->dispatch('toast',
    type: 'error',
    message: 'Something went wrong!'
);
```

### Warning

```php
$this->dispatch('toast',
    type: 'warning',
    message: 'Please review your input!'
);
```

### Info

```php
$this->dispatch('toast',
    type: 'info',
    message: 'New feature available!'
);
```

## Examples

### Form Validation

```php
$validate = function () {
    $validated = $this->validate([
        'email' => 'required|email',
    ]);

    $this->dispatch('toast',
        type: 'success',
        message: 'Email validated successfully!'
    );
};
```

### Error Handling

```php
try {
    // Your code here
} catch (\Exception $e) {
    $this->dispatch('toast',
        type: 'error',
        message: 'Failed to process: ' . $e->getMessage()
    );
}
```

### Custom Duration

```php
$this->dispatch('toast',
    type: 'warning',
    message: 'This will stay for 5 seconds',
    duration: 5000 // 5 seconds
);
```

## Component Location

- Component: `resources/views/components/toast.blade.php`
- Layout Integration: `resources/views/components/layouts/app/sidebar.blade.php`
- Helper: `support/toast.php`

## No Installation Required

The toast component is automatically included in the app layout and ready to use in any Livewire component or Alpine.js template.
