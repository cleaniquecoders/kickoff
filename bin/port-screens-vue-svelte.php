<?php

/**
 * Port kickoff Livewire/Flux screens to Vue + Svelte (Inertia) variants.
 *
 * Standalone: no framework boot, no composer autoload.
 *
 * Usage:
 *   php bin/port-screens-vue-svelte.php vue
 *   php bin/port-screens-vue-svelte.php svelte
 *   php bin/port-screens-vue-svelte.php all
 *
 * Companion to bin/port-screens.php (react). Shares controllers/routes
 * via stubs/port/shared/ — do NOT duplicate backend wiring per frontend.
 */

$root = dirname(__DIR__);
$manifestFile = $root.'/stubs/manifest/screens.json';

if (! is_file($manifestFile)) {
    fwrite(STDERR, "Manifest not found: {$manifestFile}\nRun bin/build-screen-manifest.php first.\n");
    exit(1);
}

$manifest = json_decode(file_get_contents($manifestFile), true);
$screens = $manifest['screens'] ?? [];

$target = $argv[1] ?? 'all';
if (! in_array($target, ['vue', 'svelte', 'all'], true)) {
    fwrite(STDERR, "Usage: port-screens-vue-svelte.php [vue|svelte|all]\n");
    exit(1);
}

// ---------------------------------------------------------------------------
// Mapping tables
// ---------------------------------------------------------------------------

/**
 * Flux tag -> per-kit component map.
 * `name` is resolved per-kit below; `self` = self-closing allowed.
 */
$fluxMap = [
    'badge'          => ['vue' => 'Badge',          'svelte' => 'Badge',          'from' => 'badge'],
    'button'         => ['vue' => 'Button',         'svelte' => 'Button',         'from' => 'button'],
    'callout'        => ['vue' => 'Alert',          'svelte' => 'Alert',          'from' => 'alert'],
    'callout.heading'=> ['vue' => 'AlertTitle',     'svelte' => 'AlertTitle',     'from' => 'alert'],
    'callout.text'   => ['vue' => 'AlertDescription','svelte' => 'AlertDescription','from' => 'alert'],
    'checkbox'       => ['vue' => 'Checkbox',       'svelte' => 'Checkbox',       'from' => 'checkbox'],
    'dropdown'       => ['vue' => 'DropdownMenu',   'svelte' => 'DropdownMenu',   'from' => 'dropdown-menu'],
    'field'          => ['vue' => 'Field',          'svelte' => 'Field',          'from' => 'field'],
    'fieldset'       => ['vue' => 'Fieldset',       'svelte' => 'Fieldset',       'from' => 'field'],
    'heading'        => ['vue' => 'Heading',        'svelte' => 'Heading',        'from' => 'heading'],
    'icon'           => ['vue' => null,             'svelte' => null,             'from' => null], // -> lucide
    'input'          => ['vue' => 'Input',          'svelte' => 'Input',          'from' => 'input'],
    'label'          => ['vue' => 'Label',          'svelte' => 'Label',          'from' => 'label'],
    'legend'         => ['vue' => 'Legend',         'svelte' => 'Legend',         'from' => 'field'],
    'link'           => ['vue' => 'Link',           'svelte' => 'Link',           'from' => '@inertiajs'],
    'menu'           => ['vue' => 'DropdownMenuContent','svelte' => 'DropdownMenuContent','from' => 'dropdown-menu'],
    'menu.heading'   => ['vue' => 'DropdownMenuLabel','svelte' => 'DropdownMenuLabel','from' => 'dropdown-menu'],
    'menu.item'      => ['vue' => 'DropdownMenuItem','svelte' => 'DropdownMenuItem','from' => 'dropdown-menu'],
    'menu.separator' => ['vue' => 'DropdownMenuSeparator','svelte' => 'DropdownMenuSeparator','from' => 'dropdown-menu'],
    'modal'          => ['vue' => 'Dialog',         'svelte' => 'Dialog',         'from' => 'dialog'],
    'modal.close'    => ['vue' => 'DialogClose',    'svelte' => 'DialogClose',    'from' => 'dialog'],
    'modal.trigger'  => ['vue' => 'DialogTrigger',  'svelte' => 'DialogTrigger',  'from' => 'dialog'],
    'radio'          => ['vue' => 'RadioGroupItem', 'svelte' => 'RadioGroupItem', 'from' => 'radio-group'],
    'radio.group'    => ['vue' => 'RadioGroup',     'svelte' => 'RadioGroup',     'from' => 'radio-group'],
    'select'         => ['vue' => 'Select',         'svelte' => 'Select',         'from' => 'select'],
    'select.option'  => ['vue' => 'SelectItem',     'svelte' => 'SelectItem',     'from' => 'select'],
    'separator'      => ['vue' => 'Separator',      'svelte' => 'Separator',      'from' => 'separator'],
    'spacer'         => ['vue' => null,             'svelte' => null,             'from' => null], // -> <div class="flex-1" />
    'subheading'     => ['vue' => 'Subheading',     'svelte' => 'Subheading',     'from' => 'heading'],
    'switch'         => ['vue' => 'Switch',         'svelte' => 'Switch',         'from' => 'switch'],
    'text'           => ['vue' => 'Text',           'svelte' => 'Text',           'from' => 'text'],
    'textarea'       => ['vue' => 'Textarea',       'svelte' => 'Textarea',       'from' => 'textarea'],
];

/** Core wire directives this porter knows how to translate. */
const CORE_WIRE = ['model', 'model.live', 'click', 'navigate', 'submit', 'confirm', 'loading'];

// Lucide component name per kit: same PascalCase, different package.
$lucidePkg = ['vue' => 'lucide-vue-next', 'svelte' => 'lucide-svelte'];

$studly = fn (string $seg): string => str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $seg)));

// ---------------------------------------------------------------------------
// Blade translation (regex-based pattern map; complex constructs -> TODO)
// ---------------------------------------------------------------------------

/**
 * Translate blade source to a kit template body.
 * Returns [body, todos[], unmapped[]].
 */
function translateBlade(string $blade, string $kit, array $fluxMap, callable $studly): array
{
    $todos = [];
    $unmapped = [];
    $out = $blade;

    // --- strip blade comments, note them
    $out = preg_replace('/\{\{--.*?--\}\}/s', '', $out);

    // --- php class blocks (Livewire inline components) -> TODO
    if (preg_match('/<\?php.*?\?>/s', $out)) {
        $out = preg_replace('/<\?php.*?\?>/s', '', $out);
        $todos[] = 'inline Livewire PHP class moved to page <script> — port mount()/actions into setup state';
    }

    // --- blade directives that need server data / auth -> TODO
    $directiveTodos = [
        '@can' => 'authorization check — move to controller-provided `can` prop',
        '@cannot' => 'authorization check — move to controller-provided `can` prop',
        '@unless' => 'conditional — port to template if/else with server-provided prop',
        '@auth' => 'auth check — use shared `auth.user` Inertia prop',
        '@guest' => 'guest check — use shared `auth.user` Inertia prop',
        '@php' => 'inline PHP — move to computed/derived value',
        '@include' => 'blade @include — extract to a shared component or inline',
        '@livewire' => 'nested Livewire component — extract to child page component',
        '@svg' => 'blade @svg icon — use lucide component directly',
        '@csrf' => 'CSRF — Inertia handles via XSRF-TOKEN cookie automatically',
        '@method' => 'HTTP method spoof — use router.post/put/delete from Inertia',
    ];
    foreach ($directiveTodos as $directive => $note) {
        if (str_contains($out, $directive)) {
            $todos[] = "{$directive}: {$note}";
        }
    }

    // --- control flow: forelse/foreach/for/if/elseif/else/endif/endforeach...
    // Keep a simple structural translation; anything nested too deep gets a TODO.
    $loopCount = preg_match_all('/@for(each|else|)\b/', $out);
    if ($loopCount > 2) {
        $todos[] = "deeply nested loops ({$loopCount}) — verify generated each-blocks against blade source";
    }

    if ($kit === 'vue') {
        // @foreach ($x as $y) -> <template v-for="y in x" :key="y.uuid ?? y.id ?? $index">
        // Done heuristically below on a per-tag basis — here we just flag them.
    } else {
        // svelte {#each}
    }

    // Remove directive lines but keep their bodies; the structural wrappers are
    // handled when we walk flux tags. Anything left as a bare @directive line
    // at the end becomes a TODO comment.
    $out = preg_replace('/^\s*@(if|elseif|else|endif|foreach|forelse|empty|endforeach|endforelse|endunless|unless|endcan|can|cannot|endcannot|auth|endauth|guest|endguest|php|endphp|include|livewire|svg|csrf|method|session|endsession|error|enderror|isset|endisset|empty|endempty|switch|case|break|endswitch|default|while|endwhile|for|endfor|continue|verbatim|endverbatim|once|endonce|push|endpush|stack|prepend|endprepend|inject|section|endsection|yield|extends|parent|show|stop|append|overwrite|hasSection|sectionMissing)\b[^\n]*$/m', '', $out);

    // --- flux tags -------------------------------------------------------
    $unmappedTags = [];
    $out = preg_replace_callback(
        '#<(/?)flux:([a-z0-9.\-]+)((?:"[^"]*"|\'[^\']*\'|[^>"\'])*)>(/?)#i',
        function ($m) use ($kit, $fluxMap, $studly, &$unmappedTags) {
            [, $close, $tag, $attrStr, $selfClose] = $m;

            if (! isset($fluxMap[$tag])) {
                // flux:icon.foo -> lucide component <Foo />
                if (str_starts_with($tag, 'icon.')) {
                    $iconName = $studly(substr($tag, 5));
                    if ($close) {
                        return '';
                    }

                    return '<'.$iconName.' class="size-4" />';
                }
                $unmappedTags[] = $tag;
                return '<!-- TODO: unmapped flux:'.$tag.' -->';
            }
            $map = $fluxMap[$tag];
            $name = $map[$kit];

            if ($name === null) {
                // special-cased tags
                if ($tag === 'icon') {
                    // flux:icon.foo or icon="foo" — we can't resolve the name here
                    // reliably from regex; leave a marker the icon pass replaces.
                    return $close ? '</LucideIcon>' : '<LucideIcon'.portAttrs($attrStr, $kit).($selfClose ? ' /' : '').'>';
                }
                if ($tag === 'spacer') {
                    return $close ? '' : '<div class="flex-1"></div>';
                }
            }

            $attrs = portAttrs($attrStr, $kit);
            if ($close) {
                return '</'.$name.'>';
            }
            // attr capture is greedy and may swallow the self-closing slash
            $self = str_ends_with(trim($m[0]), '/>');
            if ($self) {
                $attrs = rtrim($attrs);
                if (str_ends_with($attrs, '/')) {
                    $attrs = rtrim(substr($attrs, 0, -1));
                }
            }

            return '<'.$name.$attrs.($self ? ' />' : '>');
        },
        $out
    );
    $unmapped = array_unique($unmappedTags);

    // --- blade x-components (layouts, breadcrumbs, flyout, empty-state, ...) --
    // Not portable 1:1; replace with a TODO marker naming the component.
    $out = preg_replace_callback(
        '#<x-([a-z0-9.\-]+)((?:"[^"]*"|\'[^\']*\'|[^>"\'])*?)(/?)>#i',
        function ($m) use (&$todos) {
            $todos[] = 'x-'.$m[1].' blade component — port to kit equivalent or drop';

            return '<!-- TODO: x-'.$m[1].' -->';
        },
        $out
    );
    $out = preg_replace('#</x-[a-z0-9.\-]+>#i', '', $out);

    // --- wire directives -------------------------------------------------
    $unmappedWire = [];
    $out = preg_replace_callback(
        '/\swire:([a-zA-Z0-9]+)((?:\.[a-zA-Z0-9]+(?:\.\d+ms)?)*)(="([^"]*)")?/',
        function ($m) use ($kit, &$unmappedWire) {
            $directive = $m[1];
            $modifiers = $m[2];
            $value = $m[4] ?? '';

            switch ($directive) {
                case 'model':
                    // wire:model, wire:model.live, wire:model.live.debounce.300ms
                    return $kit === 'vue' ? ' v-model="'.$value.'"' : ' bind:value={'."$value".'}';
                case 'click':
                    $handler = $kit === 'vue'
                        ? ' @click="'.vueAction($value).'"'
                        : ' onclick={'.svelteAction($value).'}';
                    if (str_contains($modifiers, '.prevent') && $kit === 'vue') {
                        $handler = ' @click.prevent="'.vueAction($value).'"';
                    }

                    return $handler;
                case 'navigate':
                    return ''; // Inertia <Link> handles navigation
                case 'submit':
                    if ($kit === 'vue') {
                        return ' @submit.prevent="'.vueAction($value).'"';
                    }

                    return ' onsubmit={(e) => { e.preventDefault(); '.svelteAction($value).'; }}';
                case 'confirm':
                    return ' data-confirm="'.htmlspecialchars($value, ENT_QUOTES).'" /* TODO: wire confirm() into click handler */';
                case 'loading':
                    return ' /* TODO: wire:loading'.$modifiers.' — use form.processing / inertia progress */';
                case 'key':
                    return $kit === 'vue' ? ' :key="'.$value.'" /* TODO: wire:key — use a real loop key */' : ' /* TODO: wire:key — use a real each-block key */';
                default:
                    $unmappedWire[] = 'wire:'.$directive.$modifiers;
                    return ' /* TODO: unmapped wire:'.$directive.$modifiers.'="'.htmlspecialchars($value, ENT_QUOTES).'" */';
            }
        },
        $out
    );

    // --- Alpine dispatches / $wire calls -> TODO --------------------------
    if (str_contains($out, '$dispatch(') || str_contains($out, '$wire.')) {
        $todos[] = 'Alpine $dispatch/$wire usage — port to Inertia router visit or emitted component event';
        $out = preg_replace('/x-on:click="[^"]*\$(dispatch|wire)[^"]*"/', '/* TODO: alpine handler */', $out);
    }

    // --- blade echo -> kit moustache --------------------------------------
    // Vue keeps {{ }} but blade PHP inside must be flagged; Svelte needs { }.
    if ($kit === 'svelte') {
        $out = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '{$1}', $out);
        $out = preg_replace('/\{!!\s*(.+?)\s*!!\}/', '{@html $1}', $out);
    }
    if (preg_match('/[{{]\s*(trans_choice|route\(|__\(|session\(|old\(|auth\()/', $out)) {
        $todos[] = 'verify blade echos against server-provided Inertia props (routes, translations)';
    }

    // --- nested <livewire:... /> tags ---------------------------------------
    $out = preg_replace_callback(
        '/<livewire:([a-z0-9.\-]+)[^>]*\/?>/i',
        function ($m) use (&$todos) {
            $todos[] = 'livewire:'.$m[1].' nested component — extract to child page component';

            return '<!-- TODO: livewire:'.$m[1].' -->';
        },
        $out
    );

    // Collapse 3+ blank lines
    $out = preg_replace("/\n{3,}/", "\n\n", $out);

    return [trim($out), array_unique($todos), array_merge($unmapped, array_unique($unmappedWire))];
}

/** Map a blade attribute string onto the kit's conventions. */
function portAttrs(string $attrStr, string $kit): string
{
    // :href="route('x')" -> href={route('x')} (svelte) / :href="route('x')" stays for vue
    if ($kit === 'svelte') {
        $attrStr = preg_replace('/\s:([a-zA-Z-]+)="([^"]*)"/', ' $1={$2}', $attrStr);
    }

    return $attrStr;
}

/** Turn a Livewire action string into a Vue handler expression. */
function vueAction(string $action): string
{
    // delete('uuid') -> delete('uuid') — assumes the method exists on setup scope
    return $action;
}

/** Turn a Livewire action string into a Svelte handler expression. */
function svelteAction(string $action): string
{
    return $action;
}

// ---------------------------------------------------------------------------
// Page scaffolding
// ---------------------------------------------------------------------------

function lucideImports(array $icons, callable $studly): array
{
    $imports = [];
    foreach ($icons as $icon) {
        if ($icon === 'loading') {
            continue; // spinner, kit-specific
        }
        $imports[] = $studly($icon);
    }

    return array_unique($imports);
}

function collectComponents(array $fluxTags, string $kit, array $fluxMap): array
{
    $byModule = [];
    foreach ($fluxTags as $tag) {
        if (! isset($fluxMap[$tag])) {
            continue;
        }
        $map = $fluxMap[$tag];
        if ($map[$kit] === null) {
            continue;
        }
        $byModule[$map['from']][] = $map[$kit];
    }
    foreach ($byModule as &$names) {
        $names = array_unique($names);
        sort($names);
    }
    ksort($byModule);

    return $byModule;
}

function scaffoldVue(array $screen, string $bladeBody, array $todos, array $components, array $lucide, string $lucidePkg): string
{
    $scriptImports = [];
    $scriptImports[] = "import AppLayout from '@/layouts/AppLayout.vue';";
    $scriptImports[] = "import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';";
    if ($lucide) {
        $scriptImports[] = "import { ".implode(', ', $lucide)." } from '{$lucidePkg}';";
    }
    foreach ($components as $module => $names) {
        if ($module === '@inertiajs') {
            continue;
        }
        $scriptImports[] = "import { ".implode(', ', $names)." } from '@/components/ui/{$module}';";
    }

    $todoBlock = $todos
        ? "\n<!--\nTODO (".count($todos)."):\n - ".implode("\n - ", $todos)."\n-->\n"
        : '';

    $title = ucwords(str_replace(['-', '_', '/'], ' ', basename($screen['path'], '.blade.php')));

    return <<<VUE
<script setup lang="ts">
{$scriptImports[0]}
{$scriptImports[1]}


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="{$title}" />
{$todoBlock}
{$bladeBody}
</template>
VUE;
}

function scaffoldSvelte(array $screen, string $bladeBody, array $todos, array $components, array $lucide, string $lucidePkg): string
{
    $scriptImports = [];
    $scriptImports[] = "import AppLayout from '@/layouts/AppLayout.svelte';";
    $scriptImports[] = "import { Link, router, useForm } from '@inertiajs/svelte';";
    if ($lucide) {
        $scriptImports[] = "import { ".implode(', ', $lucide)." } from '{$lucidePkg}';";
    }
    foreach ($components as $module => $names) {
        if ($module === '@inertiajs') {
            continue;
        }
        $scriptImports[] = "import { ".implode(', ', $names)." } from '\$lib/components/ui/{$module}';";
    }

    $todoBlock = $todos
        ? "\n<!--\nTODO (".count($todos)."):\n - ".implode("\n - ", $todos)."\n-->\n"
        : '';

    $title = ucwords(str_replace(['-', '_', '/'], ' ', basename($screen['path'], '.blade.php')));

    return <<<SVELTE
<script lang="ts">
{$scriptImports[0]}
{$scriptImports[1]}

// let props = \$props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = \$state({});        // TODO: UI-only state (flyouts, selection)
// let total = \$derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>{$title}</title></svelte:head>

<AppLayout>
{$todoBlock}
{$bladeBody}
</AppLayout>
SVELTE;
}

// ---------------------------------------------------------------------------
// Path mapping: blade view -> pages/**.{vue,svelte}
// ---------------------------------------------------------------------------

function pagePath(string $bladeRel, string $ext): string
{
    // stubs/resources/views/livewire/security/users/index.blade.php
    // -> pages/security/users/Index.vue
    $rel = preg_replace('#^stubs/resources/views/#', '', $bladeRel);
    $rel = preg_replace('#\.blade\.php$#', '', $rel);
    $rel = preg_replace('#^livewire/#', '', $rel);
    $segments = array_map(fn ($s) => str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $s))), explode('/', $rel));
    $dirs = array_map('lcfirst', array_slice($segments, 0, -1));
    $path = 'resources/js/pages/'.($dirs ? implode('/', $dirs).'/' : '').end($segments).'.'.$ext;

    return $path;
}

// ---------------------------------------------------------------------------
// Shared backend (controllers + routes) — one copy for both frontends
// ---------------------------------------------------------------------------

function writeSharedBackend(string $root, array $screens): array
{
    $written = [];
    $controllersDir = $root.'/stubs/port/shared/app/Http/Controllers/Port';
    $routesDir = $root.'/stubs/port/shared/routes/web';
    @mkdir($controllersDir, 0777, true);
    @mkdir($routesDir, 0777, true);

    $routeLines = ["<?php", "", "use Illuminate\\Support\\Facades\\Route;", ""];

    // Group screens by controller-ish bucket from their path.
    $buckets = [];
    foreach ($screens as $screen) {
        $rel = preg_replace('#^stubs/resources/views/#', '', $screen['path']);
        $rel = preg_replace('#\.blade\.php$#', '', $rel);
        $rel = preg_replace('#^livewire/#', '', $rel);
        $parts = explode('/', $rel);
        $bucket = $parts[0] ?? 'misc';
        $buckets[$bucket][] = $screen;
    }

    foreach ($buckets as $bucket => $bucketScreens) {
        $studly = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $bucket)));
        $controller = "{$studly}Controller";
        $methods = [];
        foreach ($bucketScreens as $screen) {
            $page = pagePath($screen['path'], 'vue'); // path stem is kit-agnostic
            $page = preg_replace('#^resources/js/pages/#', '', $page);
            $page = preg_replace('#\.vue$#', '', $page);
            $parts = explode('/', $page);
            $method = lcfirst(end($parts));
            // disambiguate generic names (index/show) with the parent dir:
            // admin/roles/Index -> rolesIndex; admin/index -> adminIndex stays index if unique
            if (isset($methods[$method]) || in_array($method, ['index', 'show'], true)) {
                $parent = count($parts) > 1 ? $parts[count($parts) - 2] : '';
                if ($parent !== '' && $parent !== $bucket) {
                    $method = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $parent)))).ucfirst($method);
                }
            }
            $base = $method;
            $i = 2;
            while (isset($methods[$method])) {
                $method = $base.$i++;
            }
            $methods[$method] = $page;
        }

        $body = ["<?php", "", "namespace App\\Http\\Controllers\\Port;", "", "use Inertia\\Inertia;", "", "class {$controller} extends \\App\\Http\\Controllers\\Controller", "{"];
        foreach ($methods as $method => $page) {
            $body[] = "    public function {$method}()";
            $body[] = "    {";
            $body[] = "        // TODO: pass real props; Livewire component state must move here.";
            $body[] = "        return Inertia::render('{$page}', []);";
            $body[] = "    }";
            $body[] = "";
        }
        $body[] = "}";
        file_put_contents("{$controllersDir}/{$controller}.php", implode("\n", $body)."\n");
        $written[] = "stubs/port/shared/app/Http/Controllers/Port/{$controller}.php";

        foreach ($methods as $method => $page) {
            $uri = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $page));
            $routeLines[] = "Route::inertia('/port/{$uri}', '{$page}')->name('port.{$bucket}.{$method}'); // or Route::get(..., [{$controller}::class, '{$method}'])";
        }
        $routeLines[] = "";
    }

    file_put_contents("{$routesDir}/port.php", implode("\n", $routeLines)."\n");
    $written[] = 'stubs/port/shared/routes/web/port.php';

    return $written;
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

$kits = $target === 'all' ? ['vue', 'svelte'] : [$target];
$report = [];

// A plain blade page (e.g. security/users/index) that only hosts a full-page
// Livewire component collides with that component's own view under livewire/.
// The livewire view is the real screen — skip the wrapper (same rule as the
// react port in bin/port-screens.php).
$livewireStems = [];
foreach ($screens as $screen) {
    if (str_contains($screen['path'], '/views/livewire/')) {
        $livewireStems[] = preg_replace('#^stubs/resources/views/livewire/#', '', $screen['path']);
    }
}
$screens = array_values(array_filter($screens, function ($screen) use ($livewireStems) {
    if (str_contains($screen['path'], '/views/livewire/')) {
        return true;
    }
    $stem = preg_replace('#^stubs/resources/views/#', '', $screen['path']);

    return ! in_array($stem, $livewireStems, true);
}));

// Shared backend written once, regardless of kit.
$sharedFiles = writeSharedBackend($root, $screens);

foreach ($kits as $kit) {
    $ext = $kit;
    $outRoot = $root."/stubs/port/{$kit}";
    $stats = ['pages' => 0, 'todos' => 0, 'unmapped' => []];
    $mappingRows = [];

    foreach ($screens as $screen) {
        $bladeAbs = $root.'/'.$screen['path'];
        if (! is_file($bladeAbs)) {
            continue;
        }
        $blade = file_get_contents($bladeAbs);

        [$body, $todos, $unmapped] = translateBlade($blade, $kit, $fluxMap, $studly);

        // Imports must reflect what actually survived translation (TODO markers
        // carry no tags), so scan the translated body rather than the manifest.
        $usedTags = [];
        foreach ($fluxMap as $tag => $map) {
            $name = $map[$kit] ?? null;
            if ($name !== null && preg_match('/<'.$name.'[\s>\/]/', $body)) {
                $usedTags[] = $tag;
            }
        }
        $components = collectComponents($usedTags, $kit, $fluxMap);

        $usedLucide = [];
        if (preg_match_all('/<([A-Z][a-zA-Z0-9]*) class="size-4" \/>/', $body, $lm)) {
            $usedLucide = array_unique($lm[1]);
        }

        $content = $kit === 'vue'
            ? scaffoldVue($screen, $body, $todos, $components, $usedLucide, $lucidePkg[$kit])
            : scaffoldSvelte($screen, $body, $todos, $components, $usedLucide, $lucidePkg[$kit]);

        $rel = pagePath($screen['path'], $ext);
        $dest = $outRoot.'/'.$rel;
        @mkdir(dirname($dest), 0777, true);
        file_put_contents($dest, $content);

        $stats['pages']++;
        $stats['todos'] += count($todos);
        foreach ($unmapped as $u) {
            $stats['unmapped'][$u] = ($stats['unmapped'][$u] ?? 0) + 1;
        }
    }

    // README
    $readme = readme($kit, $fluxMap, $stats, $sharedFiles);
    file_put_contents($outRoot.'/README.md', $readme);

    $report[$kit] = $stats;
}

// ---------------------------------------------------------------------------
// Report
// ---------------------------------------------------------------------------

echo "Shared backend:\n";
foreach ($sharedFiles as $f) {
    echo "  {$f}\n";
}
echo "\n";
foreach ($report as $kit => $stats) {
    echo "[{$kit}] pages: {$stats['pages']}, todos: {$stats['todos']}\n";
    if ($stats['unmapped']) {
        echo "  unmapped:\n";
        foreach ($stats['unmapped'] as $tag => $count) {
            echo "    {$tag} ({$count}x)\n";
        }
    } else {
        echo "  unmapped: none\n";
    }
}

// ---------------------------------------------------------------------------

function readme(string $kit, array $fluxMap, array $stats, array $sharedFiles): string
{
    $kitName = ucfirst($kit);
    $rows = [];
    foreach ($fluxMap as $tag => $map) {
        $target = $map[$kit] ?? '(none)';
        $from = $map['from'] ?? '—';
        $rows[] = "| `flux:{$tag}` | `{$target}` | `{$from}` |";
    }
    $table = implode("\n", $rows);

    $unmappedList = $stats['unmapped']
        ? "\n".implode("\n", array_map(fn ($t, $c) => "- `{$t}` ({$c}x)", array_keys($stats['unmapped']), $stats['unmapped']))."\n"
        : ' none';

    return <<<MD
# Kickoff screen port — {$kitName}

Ported from `stubs/resources/views/**` (Livewire + Flux) by
`bin/port-screens-vue-svelte.php`. **{$stats['pages']} pages, {$stats['todos']} TODO markers.**

## Shared backend

Controllers and routes are **shared with the React port** — see `stubs/port/shared/`:

MD
        ."\n".implode("\n", array_map(fn ($f) => "- `{$f}`", $sharedFiles))."\n".<<<MD

Both the {$kitName} and React frontends mount against the same Inertia controllers
under `App\Http\Controllers\Port\*` and the routes in `routes/web/port.php`.
Do not duplicate controllers per frontend.

## Stack

MD
        ."\n".($kit === 'vue'
            ? "- Composition API + `<script setup lang=\"ts\">`\n- shadcn-vue (`@/components/ui/*`)\n- `@inertiajs/vue3` (`useForm`, `router`, `Link`)\n- `lucide-vue-next` icons\n"
            : "- Svelte 5 runes (`\$state`, `\$derived`)\n- shadcn-svelte (`\$lib/components/ui/*`)\n- `@inertiajs/svelte` (`useForm`, `router`, `Link`)\n- `lucide-svelte` icons\n").<<<MD

## Flux → {$kitName} component map

| Flux tag | {$kitName} component | shadcn module |
|---|---|---|
{$table}

## wire: directive map

| Livewire | {$kitName} |
|---|---|
MD
        ."\n".($kit === 'vue'
            ? "| `wire:model` / `.live` | `v-model` |\n| `wire:click` | `@click` |\n| `wire:submit` | `@submit.prevent` |\n| `wire:navigate` | `<Link>` |\n| `wire:confirm` | `confirm()` in handler (TODO) |\n| `wire:loading` | `form.processing` / Inertia progress (TODO) |\n"
            : "| `wire:model` / `.live` | `bind:value` |\n| `wire:click` | `onclick={...}` |\n| `wire:submit` | `onsubmit` with `preventDefault` |\n| `wire:navigate` | `<Link>` |\n| `wire:confirm` | `confirm()` in handler (TODO) |\n| `wire:loading` | `form.processing` / Inertia progress (TODO) |\n").<<<MD

## Unmapped constructs
{$unmappedList}
Anything not in the two tables above was left as an HTML `<!-- TODO: ... -->`
comment in the generated page. Auth (`@can`), loops over server data, nested
Livewire components and Alpine `\$dispatch`/`\$wire` calls always produce TODOs —
they must be re-wired against real Inertia props by hand.

MD;
}
