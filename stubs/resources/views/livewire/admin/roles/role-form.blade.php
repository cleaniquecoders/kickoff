<flux:modal variant="flyout" wire:model="showing" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">
                {{ $editingUuid ? __('Edit Role') : __('Add Role') }}
            </flux:heading>
            <flux:text class="mt-2">
                {{ $editingUuid
                    ? __('Update the role details below. The internal name cannot be changed.')
                    : __('The internal name is derived from the display name.') }}
            </flux:text>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="displayName" :label="__('Display Name')" required autofocus />

            <flux:textarea wire:model="description" :label="__('Description')" rows="3" />

            @unless ($isProtected)
                <flux:switch wire:model="isEnabled" :label="__('Enabled')" />
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Disabled roles cannot be assigned to users.') }}
                </p>
            @endunless

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="close" class="cursor-pointer">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" class="cursor-pointer">
                    {{ $editingUuid ? __('Save Changes') : __('Create Role') }}
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
