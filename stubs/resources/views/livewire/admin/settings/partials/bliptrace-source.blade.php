{{-- Where a field's effective value comes from: saved here, the G8SCOPE_* env, or the SDK default. --}}
@switch($source)
    @case('ui')
        <flux:badge size="sm" color="blue" title="{{ __('Saved on this page') }}">{{ __('UI') }}</flux:badge>
        @break
    @case('env')
        <flux:badge size="sm" color="amber" title="{{ __('From the environment') }}">{{ __('Env') }}</flux:badge>
        @break
    @default
        <flux:badge size="sm" color="zinc" title="{{ __('SDK default') }}">{{ __('Default') }}</flux:badge>
@endswitch
