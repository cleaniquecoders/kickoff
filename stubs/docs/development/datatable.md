# Datatable

Adding Delete Bulk Action by simply import the `InteractsWithDestroy` trait then add in the datatable class:

```php
public array $bulkActions = [
    'destroyConfirmation' => 'Delete',
];
protected $listeners = [
    'destroyRecord' => 'destroy',
];
```

Adding Action by using `ActionColumn` class:

```php
ActionColumn::make('Actions', 'uuid')
```

By default it use `$model->getResourceUrl()` to generate the URL for actions. But you can ovewrite by providing `setView('path.to.view')`.
