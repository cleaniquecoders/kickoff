# Frontend Option Spec

Design spec for the `--frontend=` option on `kickoff start`. No code yet — this is the contract the implementation must satisfy.

## 1. CLI Option

**Location**: `src/StartCommand.php` → `configure()`

```php
->addOption(
    'frontend',
    null,
    InputOption::VALUE_REQUIRED,
    'Frontend stack: livewire, react, vue, svelte, or vendor-kit.',
    'livewire'
)
```

| Value        | `laravel new` flag        | Notes |
|--------------|---------------------------|-------|
| `livewire`   | `--livewire` (current)    | Default. Preserves existing behavior exactly. |
| `react`      | `--react`                 | Inertia React starter kit. |
| `vue`        | `--vue`                   | Inertia Vue starter kit. |
| `svelte`     | `--svelte`                | Inertia Svelte starter kit. |
| `vendor-kit` | `--using=<owner/package>` | Escape hatch for a third-party starter kit. Argument is passed verbatim as `--using`. |

**Validation** (`execute()`, before any filesystem work):

```php
$frontend = $input->getOption('frontend');
$allowed = ['livewire', 'react', 'vue', 'svelte', 'vendor-kit'];

if (! in_array($frontend, $allowed, true)) {
    $output->writeln("<error>Invalid --frontend value: $frontend. Allowed: livewire, react, vue, svelte, vendor-kit.</error>");
    return Command::FAILURE;
}

if ($frontend === 'vendor-kit') {
    $output->writeln('<error>--frontend=vendor-kit requires --vendor-kit=<owner/package>.</error>');
    return Command::FAILURE;
}
```

Add a second option `--vendor-kit=` required when `frontend === 'vendor-kit'`.

**Flags kept for all values**: `--git --pest --npm --no-interaction`.

**Livewire-only flag dropped for non-livewire**: `--livewire-class-components` applies only when `frontend === 'livewire'`.

## 2. Category-Filtered Stub Copy

### 2.1 Categories

| Category | Contents | Livewire | Inertia (react/vue/svelte) |
|----------|----------|----------|----------------------------|
| **A** — backend-neutral | Everything not listed in B/C/D | ✅ | ✅ |
| **B** — builders | `app/Actions/Builder/Menu/`, `app/Actions/Builder/Breadcrumb.php`, `app/Actions/Builder/MenuItem.php`, `app/Actions/Builder/Menu.php` | ✅ | ✅ |
| **C** — screens | `app/Livewire/`, `resources/views/livewire/`, `resources/views/flux/`, `resources/views/components/`, `resources/views/layouts/`, `app/View/Components/` | ✅ | ❌ |
| **D** — routes | `routes/web.php` (neutral), `routes/web/auth.php`, `routes/web/pages.php`, `routes/web/seo.php`, `routes/web/support.php`, `routes/web/_.php` | ✅ | ✅ |
| | `routes/web/administration.php`, `routes/web/security.php`, `routes/web/media.php`, `routes/web/notifications.php` | ✅ | ❌ |

### 2.2 Implementation

`copyRecursively()` gains an `$exclude` parameter — array of path prefixes relative to the source root:

```php
function copyRecursively(
    string $src,
    string $dst,
    bool $verbose = false,
    ?OutputInterface $output = null,
    array $exclude = []
): void
```

Matching rule: a path is skipped when its normalized sub-path (`$iterator->getSubPathName()`, forward slashes) equals an exclude entry or starts with an exclude entry followed by `/`. Both files and directories are skipped; directory exclusion prunes the whole subtree.

`StartCommand::copyStubs()` builds the exclude list from `$frontend`:

```php
private function stubExcludes(string $frontend): array
{
    if ($frontend === 'livewire') {
        return [];
    }

    return [
        'app/Livewire',
        'app/View/Components',
        'resources/views/livewire',
        'resources/views/flux',
        'resources/views/components',
        'resources/views/layouts',
        'routes/web/administration.php',
        'routes/web/security.php',
        'routes/web/media.php',
        'routes/web/notifications.php',
    ];
}
```

`vendor-kit` uses the same exclude list as Inertia (starter kit ships its own frontend).

## 3. Package List Filtering

`installPackages()` receives `$frontend` and filters before calling `installPackages()` helper:

```php
private function packagesFor(string $frontend, array $require, array $requireDev): array
{
    if ($frontend === 'livewire') {
        return [$require, $requireDev];
    }

    $livewireOnly = [
        'livewire/livewire',
        'livewire/flux',
        'mallardduck/blade-lucide-icons',
    ];

    $require = array_values(array_filter(
        $require,
        fn (string $pkg) => ! in_array(strtolower(strtok($pkg, ':')), $livewireOnly, true)
    ));

    return [$require, $requireDev];
}
```

Notes:

- `strtok($pkg, ':')` strips version constraints (`livewire/livewire:^4.0`) before matching.
- `requireDev` is unchanged — no dev package is frontend-specific.
- NPM packages (`lodash axios tippy.js`) are unchanged — Inertia starter kits already bring their own JS deps.

## 4. Vendor:Publish Tag Filtering

In the `'Publishing package configs & migrations'` step, the `$options` array is filtered by `$frontend`:

```php
$livewireOnlyTags = [
    '--tag=livewire:assets',
    '--tag=livewire:config',
    '--tag=blade-lucide-icons',
    '--tag=blade-lucide-icons-config',
    '--tag=media-manager-views',
];

if ($frontend !== 'livewire') {
    $options = array_values(array_diff($options, $livewireOnlyTags));
}
```

`--tag=media-manager-config` is kept — `cleaniquecoders/media-manager` still installs on Inertia, only its views are Livewire-specific.

## 5. Guard Rails — Existing Project

When `artisan` already exists (project created earlier, possibly by another kickoff run):

1. **Detect installed stack** by inspecting `composer.json`:
   - `livewire/livewire` in `require` → `livewire`
   - `inertiajs/inertia-laravel` in `require` → `inertia`
   - neither → `unknown`
2. **Compare to `--frontend`**:
   - Match → proceed as normal.
   - Mismatch → **hard fail**, no files copied:

```php
if ($existing !== 'unknown' && $existing !== $requestedGroup) {
    $output->writeln("<error>Existing project uses '$existing' but --frontend=$frontend was requested.</error>");
    $output->writeln('<error>Aborting to avoid mixing frontend stacks. Create a fresh project or omit --frontend.</error>');
    return Command::FAILURE;
}
```

`$requestedGroup` maps `react|vue|svelte|vendor-kit` → `inertia`, `livewire` → `livewire`.

3. **`unknown` stack**: proceed with a warning, copy only category A/B stubs, skip C/D entirely, skip package filtering (install nothing frontend-specific). This covers hand-rolled projects.

## 6. Out of Scope

- **Inertia screen porting pipeline** — converting the Livewire admin/security/media/notifications screens to Inertia React/Vue/Svelte pages is a separate tool. This spec only ensures kickoff scaffolds a clean Inertia base without Livewire cruft.
- **Fortify config adjustment** for Inertia (stub `config/fortify.php` currently assumes Livewire views; see CLAUDE.md gotcha #12).
- **Breadcrumb/menu builders** still reference Livewire route names — they are kept because category B is neutral, but Inertia projects will need their own menu definitions. No attempt to auto-generate them here.
- **Inertia-specific NPM scripts** — starter kit defaults are used as-is.

## Files to Touch When Implementing

| File | Change |
|------|--------|
| `src/StartCommand.php` | Add `--frontend` / `--vendor-kit` options; validation; `stubExcludes()`; `packagesFor()`; tag filtering; guard rails |
| `support/helpers.php` | Add `$exclude` param to `copyRecursively()` |
| `tests/StartCommandTest.php` | Option validation, exclude list, package filtering, guard rail cases |
| `bin/sandbox` | Optional `--frontend` passthrough for testing |
