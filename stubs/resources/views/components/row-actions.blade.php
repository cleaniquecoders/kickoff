@props(['align' => 'end'])

{{--
    Standard table row action menu (3-dot / kebab).

    Use this for ALL table row actions across the app — never render inline
    action buttons (Edit, Delete, View) as separate buttons in a row.

    Slot accepts <flux:menu.item> / <flux:menu.separator> elements.
    Convention: positive actions → separator → edit/state → separator →
    destructive (variant="danger") last.

    <x-row-actions>
        <flux:menu.item wire:click="view(...)" icon="eye">View</flux:menu.item>
        <flux:menu.item wire:click="edit(...)" icon="pencil">Edit</flux:menu.item>
        <flux:menu.separator />
        <flux:menu.item wire:click="delete(...)" icon="trash-2" variant="danger">Delete</flux:menu.item>
    </x-row-actions>
--}}
<flux:dropdown :align="$align">
    <flux:button variant="ghost" size="sm" icon="ellipsis" inset="top bottom" aria-label="Actions" />
    <flux:menu>
        {{ $slot }}
    </flux:menu>
</flux:dropdown>
