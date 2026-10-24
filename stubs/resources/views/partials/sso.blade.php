{{--
    SSO sign-in buttons (Admin > Settings > SSO). Renders nothing — not even the
    divider — until a provider is active. rescue(): on a fresh install the
    sso_providers table may not exist yet.
--}}
@php($ssoProviders = rescue(fn () => \CleaniqueCoders\ConfigSso\Facades\ConfigSso::providers(), collect(), false))

@if ($ssoProviders->isNotEmpty())
    <x-sso />
    <flux:separator :text="__('or')" />
@endif
