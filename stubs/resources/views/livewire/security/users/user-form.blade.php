<flux:modal variant="flyout" wire:model="showing" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">
                {{ $editingUuid ? __('Edit User') : __('Add User') }}
            </flux:heading>
            <flux:text class="mt-2">
                {{ $editingUuid
                    ? __('Update the account details below.')
                    : __('The new user will receive a link to set their own password.') }}
            </flux:text>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="name" :label="__('Name')" required autofocus />

            <flux:input wire:model="email" :label="__('Email')" type="email" required />
            @if ($editingUuid)
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Changing the email resets the verification status.') }}
                </p>
            @endif

            <flux:fieldset>
                <flux:legend>{{ __('Roles') }}</flux:legend>
                <div class="space-y-2">
                    @foreach ($this->assignableRoles as $role)
                        <flux:checkbox wire:model="roles" value="{{ $role->name }}"
                            :label="$role->display_name" :description="$role->description" />
                    @endforeach
                </div>
                @error('roles.*')
                    <flux:text class="mt-2 text-red-500">{{ $message }}</flux:text>
                @enderror
            </flux:fieldset>

            @unless ($editingUuid)
                <flux:checkbox wire:model="sendPasswordSetupLink"
                    :label="__('Send password setup link')"
                    :description="__('Email the user a link to set their password.')" />
            @endunless

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="close" class="cursor-pointer">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" class="cursor-pointer">
                    {{ $editingUuid ? __('Save Changes') : __('Create User') }}
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
