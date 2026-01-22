# Datatables with Livewire 4

This project uses native Livewire 4 single-file components for creating reactive datatables with pagination, sorting, and filtering.

## Basic Datatable with Pagination

Create a single-file component in `resources/views/livewire/`:

```php
<?php

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function render()
    {
        return view('livewire.roles-table', [
            'roles' => Role::paginate(10),
        ]);
    }
}; ?>

<div>
    <div class="flex justify-end mb-4">
        {{ $roles->links() }}
    </div>

    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
        <thead class="bg-zinc-50 dark:bg-zinc-800">
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse ($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->description }}</td>
                    <td>
                        <flux:button
                            :href="route('roles.show', $role->uuid)"
                            wire:navigate>
                            View
                        </flux:button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $roles->links() }}
    </div>
</div>
```

## Datatable with Search

```php
<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, fn($query) =>
                $query->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->paginate(10);
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:input
            wire:model.live="search"
            placeholder="Search users..."
            type="search"
        />
    </div>

    <table>
        <!-- table content using $this->users -->
    </table>

    <div class="mt-4">
        {{ $this->users->links() }}
    </div>
</div>
```

## Datatable with Sorting

```php
<?php

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    #[Computed]
    public function products()
    {
        return Product::query()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
}; ?>

<div>
    <table>
        <thead>
            <tr>
                <th wire:click="sortBy('name')" class="cursor-pointer">
                    Name
                    @if($sortField === 'name')
                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
                <th wire:click="sortBy('price')" class="cursor-pointer">
                    Price
                    @if($sortField === 'price')
                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $this->products->links() }}
    </div>
</div>
```

## Bulk Actions with Delete

```php
<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public array $selected = [];

    #[Computed]
    public function users()
    {
        return User::paginate(10);
    }

    public function deleteSelected(): void
    {
        User::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->dispatch('toast', type: 'success', message: 'Users deleted successfully');
    }
}; ?>

<div>
    @if(count($selected) > 0)
        <div class="mb-4">
            <flux:button variant="danger" wire:click="deleteSelected">
                Delete {{ count($selected) }} selected
            </flux:button>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>
                    <input type="checkbox" wire:model.live="selectAll" />
                </th>
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->users as $user)
                <tr>
                    <td>
                        <input
                            type="checkbox"
                            wire:model.live="selected"
                            value="{{ $user->id }}"
                        />
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $this->users->links() }}
</div>
```

## Features

- **Pagination**: Built-in Laravel pagination with `usesPagination()` and `paginate()`
- **Search**: Real-time search with `wire:model.live`
- **Sorting**: Click to sort with custom sort logic
- **Bulk Actions**: Checkbox selection with bulk operations
- **Loading States**: Use `wire:loading` for better UX
- **Navigation**: Use `wire:navigate` for SPA-like navigation

## See Also

- [Livewire Components](02-livewire.md)
- [Single-File Components](https://livewire.laravel.com/docs/components#single-file-components)
