# Livewire Components

> This project uses Livewire 4 with built-in single-file component support for building reactive components.

## Alert Component

Using Alert component:

```php
$this->dispatch('alert', 'displayAlert',  __('Connection'), __('Connection succesfully deleted'));
```

## Confirm Component

Using Confirm component:

```php
<div class="cursor-pointer" class="bg-red-500"
    wire:click="$dispatch('confirm', 'displayConfirmation', 'Delete Connection', 'Are you sure?', 'connection-form', 'destroyConnection', '{{ $uuid }}')">
    {{ __('Remove') }}
</div>
```

Both of the Alert & Confirm modal are using the Laravel Jetstream modal.

For datatables, see the [Datatable documentation](05-datatable.md) for examples using native Livewire 4 with pagination, sorting, and filtering.

## Form Components

To create a form to edit / update, you need to create Livewire component first:

```bash
php artisan make:livewire Device
```

Then use the `InteractsWithLivewireForm` trait. All the properties defined below are required.

```php
<?php

namespace App\Livewire;

use App\Actions\Sensor\CreateNewDevice;
use App\Concerns\InteractsWithLivewireForm;
use App\Models\Device;
use Livewire\Component;

class DeviceForm extends Component
{
    use InteractsWithLivewireForm;

    public string $model = Device::class;
    public string $action = CreateNewDevice::class;
    public string $formTitle = 'Device';
    public string $view = 'livewire.device-form';
    protected $listeners = [
        'showRecord' => 'show',
        'destroyRecord' => 'destroy',
    ];
    public $state = [
        'name' => '',
    ];
}
```

## Single-File Components

Livewire 4 has built-in support for single-file components (no separate Volt package needed). Create single-file components in `resources/views/livewire/`:

```php
<?php

use Livewire\Component;

new class extends Component {
    public string $name = '';

    public function save(): void
    {
        $this->validate(['name' => 'required']);
        // Save logic
    }
}; ?>

<div>
    <input wire:model="name" type="text" />
    <button wire:click="save">Save</button>
</div>
```

## Wire Directives

Livewire 4 supports the following directives:

- `wire:model` - Bind data (updates on blur/change)
- `wire:model.live` - Real-time binding with immediate updates
- `wire:model.blur` - Update on blur event
- `wire:model.debounce.500ms` - Debounce updates
- `wire:click` - Handle click events
- `wire:submit` - Handle form submissions
- `wire:loading` - Show/hide during requests
- `wire:navigate` - Enhanced page navigation
