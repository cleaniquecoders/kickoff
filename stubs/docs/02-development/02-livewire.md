# Livewire Components

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

## Datatable Actions

Using Datatable Actions:

```php
public function columns(): array
{
    return [
        Column::make('Name', 'name')
            ->sortable(),
        Column::make('Actions', 'uuid')
            ->format(
                fn ($value, $row, Column $column) => view('livewire.datatable-actions', ['form' => 'resource-form', 'value' => $value, 'row' => $row, 'column' => $column])
            ),
    ];
}
```

## Form Components

To create a form to edit / update, you need to create Livewire component first:

```bash
php artisan make:livewire Device
```

Then use the `InteractsWithLivewireForm` trait. All the properties defined below are required.

```php
<?php

namespace App\Http\Livewire;

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
