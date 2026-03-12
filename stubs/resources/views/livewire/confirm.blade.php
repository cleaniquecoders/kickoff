<div>
    <flux:modal wire:model="displayingModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $state['title'] }}</flux:heading>
            <flux:text>{{ $state['message'] }}</flux:text>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <flux:button variant="ghost" wire:click="cancel">{{ __('Cancel') }}</flux:button>
            <flux:button variant="danger" wire:click="confirm">{{ __('Confirm') }}</flux:button>
        </div>
    </flux:modal>
</div>
