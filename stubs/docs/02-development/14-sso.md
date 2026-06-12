# Single Sign-On (SSO)

Database-backed SSO via
[cleaniquecoders/laravel-config-sso](https://github.com/cleaniquecoders/laravel-config-sso):
configure OAuth providers (Google, GitHub, GitLab, Bitbucket, Keycloak, Azure AD) from the
admin UI with encrypted client secrets — no `.env` juggling. Uses Laravel Socialite under
the hood and links/creates users on first login.

## Pre-configured Defaults

- **Admin UI** at `/admin/settings/sso` (sidebar: Settings → SSO Providers), gated by the
  `admin.manage.sso` permission, rendered in the app layout
- **Auto-registration**: new SSO users are created with the `user` role (matches the seeded
  roles); set `registration.default_role` to `null` in `config/config-sso.php` to disable
- **Redirects**: successful logins land on the `dashboard` route
- OAuth client IDs/secrets are stored encrypted in the database via the admin UI — nothing
  goes in `.env`

## Login Buttons

Add the bundled component to your login page (it renders nothing when no providers are
enabled, so it is always safe):

```blade
<x-sso />
{{-- or limit: <x-sso only="github,google" /> --}}
```

> Note: the starter kit's live login view is `resources/views/livewire/auth/login.blade.php`.
> Kickoff does not override it — add `<x-sso />` where you want the buttons.

## Keycloak / Azure AD

The core four drivers need nothing extra. For Keycloak or Azure AD, install the matching
Socialite provider first:

```bash
composer require socialiteproviders/keycloak   # Keycloak
composer require socialiteproviders/azure      # Azure AD
```

## Env Toggle

```env
CONFIG_SSO_FEATURE=true
```
