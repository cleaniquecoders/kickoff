# Kickoff screen port — Svelte

Ported from `stubs/resources/views/**` (Livewire + Flux) by
`bin/port-screens-vue-svelte.php`. **40 pages, 212 TODO markers.**

## Shared backend

Controllers and routes are **shared with the React port** — see `stubs/port/shared/`:

- `stubs/port/shared/app/Http/Controllers/Port/AdminController.php`
- `stubs/port/shared/app/Http/Controllers/Port/AuthController.php`
- `stubs/port/shared/app/Http/Controllers/Port/DashboardController.php`
- `stubs/port/shared/app/Http/Controllers/Port/NotificationsController.php`
- `stubs/port/shared/app/Http/Controllers/Port/SecurityController.php`
- `stubs/port/shared/app/Http/Controllers/Port/SettingsController.php`
- `stubs/port/shared/app/Http/Controllers/Port/MailController.php`
- `stubs/port/shared/app/Http/Controllers/Port/PagesController.php`
- `stubs/port/shared/app/Http/Controllers/Port/WelcomeController.php`
- `stubs/port/shared/routes/web/port.php`

Both the Svelte and React frontends mount against the same Inertia controllers
under `App\Http\Controllers\Port\*` and the routes in `routes/web/port.php`.
Do not duplicate controllers per frontend.

## Stack

- Svelte 5 runes (`$state`, `$derived`)
- shadcn-svelte (`$lib/components/ui/*`)
- `@inertiajs/svelte` (`useForm`, `router`, `Link`)
- `lucide-svelte` icons

## Flux → Svelte component map

| Flux tag | Svelte component | shadcn module |
|---|---|---|
| `flux:badge` | `Badge` | `badge` |
| `flux:button` | `Button` | `button` |
| `flux:callout` | `Alert` | `alert` |
| `flux:callout.heading` | `AlertTitle` | `alert` |
| `flux:callout.text` | `AlertDescription` | `alert` |
| `flux:checkbox` | `Checkbox` | `checkbox` |
| `flux:dropdown` | `DropdownMenu` | `dropdown-menu` |
| `flux:field` | `Field` | `field` |
| `flux:fieldset` | `Fieldset` | `field` |
| `flux:heading` | `Heading` | `heading` |
| `flux:icon` | `(none)` | `—` |
| `flux:input` | `Input` | `input` |
| `flux:label` | `Label` | `label` |
| `flux:legend` | `Legend` | `field` |
| `flux:link` | `Link` | `@inertiajs` |
| `flux:menu` | `DropdownMenuContent` | `dropdown-menu` |
| `flux:menu.heading` | `DropdownMenuLabel` | `dropdown-menu` |
| `flux:menu.item` | `DropdownMenuItem` | `dropdown-menu` |
| `flux:menu.separator` | `DropdownMenuSeparator` | `dropdown-menu` |
| `flux:modal` | `Dialog` | `dialog` |
| `flux:modal.close` | `DialogClose` | `dialog` |
| `flux:modal.trigger` | `DialogTrigger` | `dialog` |
| `flux:radio` | `RadioGroupItem` | `radio-group` |
| `flux:radio.group` | `RadioGroup` | `radio-group` |
| `flux:select` | `Select` | `select` |
| `flux:select.option` | `SelectItem` | `select` |
| `flux:separator` | `Separator` | `separator` |
| `flux:spacer` | `(none)` | `—` |
| `flux:subheading` | `Subheading` | `heading` |
| `flux:switch` | `Switch` | `switch` |
| `flux:text` | `Text` | `text` |
| `flux:textarea` | `Textarea` | `textarea` |

## wire: directive map

| Livewire | Svelte |
|---|---|
| `wire:model` / `.live` | `bind:value` |
| `wire:click` | `onclick={...}` |
| `wire:submit` | `onsubmit` with `preventDefault` |
| `wire:navigate` | `<Link>` |
| `wire:confirm` | `confirm()` in handler (TODO) |
| `wire:loading` | `form.processing` / Inertia progress (TODO) |

## Unmapped constructs

- `wire:target` (1x)
- `wire:cloak` (2x)

Anything not in the two tables above was left as an HTML `<!-- TODO: ... -->`
comment in the generated page. Auth (`@can`), loops over server data, nested
Livewire components and Alpine `$dispatch`/`$wire` calls always produce TODOs —
they must be re-wired against real Inertia props by hand.
