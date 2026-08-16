<?php

/**
 * Port stub Livewire/Flux screens to Inertia React (TSX) + controllers + routes.
 *
 * Template-based emitter, NOT a transpiler: each known flux/wire construct has a
 * mapping rule; unknown constructs become {/* TODO: port ... *} placeholders.
 *
 * Usage: php bin/port-screens.php react
 *
 * Standalone: no framework boot, no composer autoload.
 */
$root = dirname(__DIR__);
$manifestFile = $root.'/stubs/manifest/screens.json';
$outDir = $root.'/stubs/port/react';

$subcommand = $argv[1] ?? null;
if ($subcommand !== 'react') {
    fwrite(STDERR, "Usage: php bin/port-screens.php react\n");
    exit(1);
}

if (! is_file($manifestFile)) {
    fwrite(STDERR, "Missing stubs/manifest/screens.json — run bin/build-screen-manifest.php first.\n");
    exit(1);
}

$manifest = json_decode(file_get_contents($manifestFile), true);
$screens = $manifest['screens'];
$livewireComponents = $manifest['livewire_components'];

// Index livewire components by FQCN for screen lookup.
$livewireByFqcn = [];
foreach ($livewireComponents as $lw) {
    $fqcn = 'App\\Livewire\\'.str_replace('/', '\\', substr(
        str_replace('\\', '/', substr($lw['path'], strlen('stubs/app/Livewire/'))),
        0, -4
    ));
    $livewireByFqcn[$fqcn] = $lw;
}

// ---------------------------------------------------------------------------
// Shared state (collected during translation, reported at end)
// ---------------------------------------------------------------------------

$stats = [
    'pages' => 0,
    'controllers' => 0,
    'todos' => 0,
    'unmapped_flux' => [],
    'unmapped_wire' => [],
    'unmapped_blade' => [],
];

$reportTodo = function (string $kind, string $what) use (&$stats): string {
    $stats['todos']++;
    $key = $kind === 'flux' ? 'unmapped_flux' : ($kind === 'wire' ? 'unmapped_wire' : 'unmapped_blade');
    $stats[$key][$what] = ($stats[$key][$what] ?? 0) + 1;
    // keep the emitted comment lexically balanced + comment-safe
    $safe = str_replace(['*/', '/*'], ['* /', '/ *'], $what);
    if (substr_count($safe, '(') !== substr_count($safe, ')')) {
        $safe .= ')';
    }

    return '{/* TODO: port '.$safe.' */}';
};

// ---------------------------------------------------------------------------
// Icon name conversion: kebab-case -> PascalCase for lucide-react
// ---------------------------------------------------------------------------

$iconMap = [
    'ellipsis' => 'Ellipsis', // flux alias; lucide: Ellipsis
    'loading' => 'LoaderCircle',
    'qr-code' => 'QrCode',
];

$toLucide = function (string $name) use ($iconMap): string {
    if (isset($iconMap[$name])) {
        return $iconMap[$name];
    }

    return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
};

// ---------------------------------------------------------------------------
// Blade/flux -> TSX translator (line-oriented, pattern-map driven)
// ---------------------------------------------------------------------------

/**
 * Translate one screen's blade body to TSX JSX. Returns [jsxBody, usedImports].
 * usedImports: ['ui' => [shadcn component names], 'lucide' => [icons], 'inertia' => [symbols]]
 */
$translateBlade = function (string $blade, array $screen, ?array $lw) use (&$reportTodo, $toLucide): array {
    $ui = [];
    $lucide = [];
    $inertia = ['Link', 'router'];
    $usesForm = false;
    $stateProps = $lw['public_properties'] ?? [];

    $lines = preg_split('/\r?\n/', $blade);
    $out = [];
    $indent = 0;
    $pendingTag = null; // multi-line flux tag accumulator
    $ind = function () use (&$indent): string {
        return str_repeat('    ', $indent);
    };

    // Local-asset expressions: strip @php blocks, note them
    foreach ($lines as $rawLine) {
        $line = trim($rawLine);
        if ($line === '') {
            continue;
        }

        // --- Blade comments -> JSX comments
        if (preg_match('/^\{\{--(.*)--\}\}$/', $line, $m)) {
            $out[] = $ind().'{/*'.trim($m[1]).' */}';

            continue;
        }

        // --- HTML comments -> JSX comments (not TODOs)
        if (preg_match('/^<!--(.*)-->$/', $line, $m)) {
            $out[] = $ind().'{/*'.trim($m[1]).' */}';

            continue;
        }

        // --- @php blocks
        if (str_starts_with($line, '@php')) {
            $out[] = $ind().$reportTodo('blade', '@php block');

            continue;
        }

        // --- @if / @elseif / @else / @endif -> JS ternaries are too clever here;
        //     emit TODO except for the common "@if (x) ... @endif" one-liner guard.
        if (preg_match('/^@if\s*\((.+)\)$/', $line, $m)) {
            $out[] = $ind().$reportTodo('blade', '@if('.trim($m[1]).')');

            continue;
        }
        if (preg_match('/^@(elseif|else|endif|endunless|unless|endforelse|empty|endforeach|endcan|enderror|can|auth|guest|csrf|method|error|session|foreach|forelse)\b.*$/', $line, $m)) {
            $out[] = $ind().$reportTodo('blade', '@'.$m[1]);

            continue;
        }

        // --- {{ expr }} interpolation -> {expr} (strip __() to plain string when trivial)
        $interpolate = function (string $text): string {
            // __('Literal') -> "Literal"
            $text = preg_replace('/\{\{\s*__\(\s*[\'"]([^\'"]*)[\'"]\s*\)\s*\}\}/', '$1', $text);
            // trans_choice/route/config(...) -> TODO inline
            $text = preg_replace_callback(
                '/\{\{\s*(trans_choice|route|config)\s*\(([^}]*)\)\s*\}\}/',
                fn ($m) => '{/* TODO: '.$m[1].'('.trim($m[2]).') */}',
                $text
            );
            // {{ $this->stats['total'] }} -> {stats.total}; {{ $user->name }} -> {user.name}
            $text = preg_replace_callback(
                '/\{\{\s*(.+?)\s*\}\}/',
                function ($m) {
                    $expr = trim($m[1]);
                    // unwrap __('...') -> plain string
                    if (preg_match('/^__\(\s*[\'"](.+?)[\'"]\s*\)$/', $expr, $um)) {
                        return $um[1];
                    }
                    $expr = str_replace('$this->', '', $expr);
                    $expr = preg_replace('/\$(\w+)->/', '$1.', $expr);
                    $expr = preg_replace('/\$(\w+)/', '$1', $expr);
                    $expr = str_replace(['->', "['", "']"], ['.', '.', ''], $expr);

                    return '{'.$expr.'}';
                },
                $text
            );

            return $text;
        };

        // --- Layout / component wrappers we drop (kit supplies its own)
        if (preg_match('/^<x-layouts\.|^<\/x-layouts\.|^<x-breadcrumbs[^>]*\/?>$/', $line)) {
            continue;
        }

        // --- <x-*> custom blade components -> TODO
        if (preg_match('/^<x-([\w.-]+)/', $line, $m)) {
            $out[] = $ind().$reportTodo('blade', '<x-'.$m[1].'>');

            // keep translating inner lines naturally; closing tag skipped below
            continue;
        }
        if (preg_match('/^<\/x-[\w.-]+>$/', $line)) {
            continue;
        }

        // --- @livewire / <livewire:..> embeds -> TODO
        if (preg_match('/^<livewire:([\w.-]+)/', $line, $m)) {
            $out[] = $ind().$reportTodo('blade', '<livewire:'.$m[1].'>');

            continue;
        }

        // === FLUX COMPONENTS (multi-line aware) ===
        // Accumulate until the tag closes (`>` or `/>`); a flux tag may span lines.
        if ($pendingTag !== null) {
            $pendingTag .= ' '.$line;
            if (! preg_match('/\/?>\s*$/', $line)) {
                continue; // still inside the tag
            }
            $line = trim($pendingTag);
            $pendingTag = null;
        } elseif (preg_match('/^<flux:[\w.-]+/', $line) && ! preg_match('/\/?>\s*$/', $line)) {
            $pendingTag = $line;

            continue;
        }

        if (preg_match('/^<flux:([\w.-]+)(.*?)(\/?)>$/s', $line, $m)) {
            [$tag, $attrStr, $selfClose] = [$m[1], $m[2], $m[3] ?? ''];

            // Parse attributes into name=>value (value may be missing = boolean attr)
            $attrs = [];
            preg_match_all(
                '/([\w:.-]+)(?:\s*=\s*("([^"]*)"|\'([^\']*)\'|\{\{([^}]*)\}\}|\{([^}]*)\}))?/',
                $attrStr,
                $am,
                PREG_SET_ORDER
            );
            foreach ($am as $a) {
                $val = $a[2] ?? null; // full quoted/braced value incl. delimiters
                if (is_string($val)) {
                    $val = preg_replace('/^["\']|["\']$/', '', $val); // strip quotes
                    // unwrap __('...') coming from :attr="__('...')" bindings
                    if (preg_match('/^__\(\s*[\'"](.+?)[\'"]\s*\)$/', $val, $um)) {
                        $val = $um[1];
                    }
                }
                $attrs[$a[1]] = $val;
            }

            $class = $attrs['class'] ?? null;
            $classAttr = $class ? ' className="'.str_replace('cursor-pointer', '', $class).'"' : '';
            $iconName = null;
            if (isset($attrs['icon'])) {
                $iconName = $toLucide(trim($attrs['icon'], "'\""));
                $lucide[$iconName] = true;
            }

            // wire:* directive handling shared by several tags
            $wireModel = $attrs['wire:model'] ?? $attrs['wire:model.live'] ?? null;
            $wireClick = $attrs['wire:click'] ?? null;
            $wireConfirm = $attrs['wire:confirm'] ?? null;
            $wireNavigate = array_key_exists('wire:navigate', $attrs);
            $wireLoading = $attrs['wire:loading'] ?? null;
            $wireSubmit = $attrs['wire:submit'] ?? null;
            $wireKey = $attrs['wire:key'] ?? null;

            $emitTodo = function (string $directive) use (&$out, $ind, &$reportTodo) {
                $out[] = $ind().$reportTodo('wire', $directive);
            };

            // Helper: wire:click -> onClick handler (router.post for action methods)
            $clickHandler = function (?string $click, ?string $confirm): string {
                if ($click === null) {
                    return '';
                }
                // extract method name and first arg if any: delete('uuid') -> delete
                if (preg_match('/^(\w+)(?:\((.*)\))?$/', trim($click), $cm)) {
                    $method = $cm[1];
                    $arg = isset($cm[2]) ? trim($cm[2]) : null;
                    $call = $arg !== null && $arg !== ''
                        ? 'handle'.ucfirst($method).'('.$arg.')'
                        : 'handle'.ucfirst($method).'()';
                    if ($confirm) {
                        return ' onClick={() => confirmAction(() => '.$call.', '.var_export(trim($confirm), true).')}';
                    }

                    return ' onClick={() => '.$call.'}';
                }

                return ' onClick={() => {}}';
            };

            switch ($tag) {
                case 'input':
                    $ui['Input'] = true;
                    $ui['Label'] = true;
                    $label = $attrs['label'] ?? $attrs[':label'] ?? null;
                    $type = $attrs['type'] ?? 'text';
                    $name = $attrs['name'] ?? $wireModel ?? 'field';
                    $valueExpr = $wireModel !== null
                        ? ' value={'.$wireModel.'} onChange={(e) => set'.ucfirst($wireModel).'(e.target.value)}'
                        : '';
                    $out[] = $ind().'<div className="grid gap-2">';
                    $indent++;
                    if ($label) {
                        $out[] = $ind().'<Label htmlFor="'.$name.'">'.trim($label, "'\"").'</Label>';
                    }
                    $out[] = $ind().'<Input id="'.$name.'" type="'.$type.'"'.$valueExpr.$classAttr.' />';
                    $indent--;
                    $out[] = $ind().'</div>';
                    break;

                case 'textarea':
                    $ui['Textarea'] = true;
                    $valueExpr = $wireModel !== null
                        ? ' value={'.$wireModel.'} onChange={(e) => set'.ucfirst($wireModel).'(e.target.value)}'
                        : '';
                    $out[] = $ind().'<Textarea'.$valueExpr.$classAttr.' />';
                    break;

                case 'select':
                    $ui['Select'] = true;
                    $valueExpr = $wireModel !== null
                        ? ' value={'.$wireModel.'} onValueChange={set'.ucfirst($wireModel).'}'
                        : '';
                    $out[] = $ind().'<Select'.$valueExpr.'>';
                    $indent++;
                    $out[] = $ind().'<SelectTrigger'.$classAttr.'><SelectValue /></SelectTrigger>';
                    $out[] = $ind().'<SelectContent>';
                    $indent++;
                    // options emitted by the select.option case below
                    break;

                case 'select.option':
                    $value = $attrs['value'] ?? '';
                    $out[] = $ind().'<SelectItem value="'.trim($value, "'\"").'">';
                    $indent++;
                    break;

                case 'button':
                    $ui['Button'] = true;
                    $variant = $attrs['variant'] ?? null;
                    $variantAttr = $variant && $variant !== 'primary' ? ' variant="'.($variant === 'danger' ? 'destructive' : $variant).'"' : '';
                    $iconJsx = $iconName ? '<'.$iconName.' className="size-4" />' : '';
                    if ($wireLoading !== null) {
                        $emitTodo('wire:loading');
                    }
                    $out[] = $ind().'<Button'.$variantAttr.$clickHandler($wireClick, $wireConfirm).$classAttr.'>'
                        .$iconJsx;
                    $indent++;
                    break;

                case 'badge':
                    $ui['Badge'] = true;
                    $color = $attrs['color'] ?? null;
                    $variantAttr = $color ? ' variant="outline" data-color="'.trim($color, "'\"").'"' : '';
                    $out[] = $ind().'<Badge'.$variantAttr.$classAttr.'>';
                    $indent++;
                    break;

                case 'heading':
                    $level = $attrs['level'] ?? '2';
                    $size = $attrs['size'] ?? null;
                    $sizeClass = match ($size) {
                        'xl' => 'text-2xl font-semibold tracking-tight',
                        'lg' => 'text-xl font-semibold',
                        default => 'text-lg font-semibold',
                    };
                    $out[] = $ind().'<h'.min((int) $level, 6).' className="'.$sizeClass.($class ? ' '.$class : '').'">';
                    $indent++;
                    break;

                case 'subheading':
                    $out[] = $ind().'<p className="text-sm text-muted-foreground'.($class ? ' '.$class : '').'">';
                    $indent++;
                    break;

                case 'text':
                    $out[] = $ind().'<p className="text-sm text-muted-foreground'.($class ? ' '.$class : '').'">';
                    $indent++;
                    break;

                case 'link':
                    $inertia['Link'] = true;
                    $href = $attrs['href'] ?? $attrs[':href'] ?? '#';
                    if (str_contains($href, 'route(')) {
                        $out[] = $ind().$reportTodo('blade', 'route() in link href');
                        $href = '#';
                    }
                    $out[] = $ind().'<Link href="'.trim($href, "'\"").'" className="text-sm underline underline-offset-4'.($class ? ' '.$class : '').'">';
                    $indent++;
                    break;

                case 'checkbox':
                    $ui['Checkbox'] = true;
                    $label = $attrs['label'] ?? $attrs[':label'] ?? null;
                    $valueExpr = $wireModel !== null
                        ? ' checked={'.$wireModel.'} onCheckedChange={set'.ucfirst($wireModel).'}'
                        : '';
                    if ($label) {
                        $out[] = $ind().'<div className="flex items-center gap-2">';
                        $indent++;
                        $out[] = $ind().'<Checkbox'.$valueExpr.' />';
                        $out[] = $ind().'<Label>'.trim($label, "'\"").'</Label>';
                        $indent--;
                        $out[] = $ind().'</div>';
                    } else {
                        $out[] = $ind().'<Checkbox'.$valueExpr.' />';
                    }
                    break;

                case 'switch':
                    $ui['Switch'] = true;
                    $valueExpr = $wireModel !== null
                        ? ' checked={'.$wireModel.'} onCheckedChange={set'.ucfirst($wireModel).'}'
                        : '';
                    $out[] = $ind().'<Switch'.$valueExpr.' />';
                    break;

                case 'radio.group':
                    $ui['RadioGroup'] = true;
                    $valueExpr = $wireModel !== null
                        ? ' value={'.$wireModel.'} onValueChange={set'.ucfirst($wireModel).'}'
                        : '';
                    $out[] = $ind().'<RadioGroup'.$valueExpr.'>';
                    $indent++;
                    break;

                case 'radio':
                    $ui['RadioGroupItem'] = true;
                    $value = $attrs['value'] ?? '';
                    $out[] = $ind().'<RadioGroupItem value="'.trim($value, "'\"").'" />';
                    break;

                case 'modal':
                case 'modal.trigger':
                case 'modal.close':
                    $ui['Dialog'] = true;
                    if ($tag === 'modal') {
                        $openExpr = $wireModel !== null
                            ? ' open={'.$wireModel.'} onOpenChange={set'.ucfirst($wireModel).'}'
                            : '';
                        $out[] = $ind().'<Dialog'.$openExpr.'>';
                        $indent++;
                        $out[] = $ind().'<DialogContent'.$classAttr.'>';
                        $indent++;
                    } elseif ($tag === 'modal.trigger') {
                        $out[] = $ind().'<DialogTrigger asChild>';
                        $indent++;
                    } else {
                        $out[] = $ind().'<DialogClose asChild>';
                        $indent++;
                    }
                    break;

                case 'dropdown':
                    $ui['DropdownMenu'] = true;
                    $out[] = $ind().'<DropdownMenu>';
                    $indent++;
                    break;

                case 'menu':
                    $out[] = $ind().'<DropdownMenuContent>';
                    $indent++;
                    break;

                case 'menu.heading':
                    $out[] = $ind().'<DropdownMenuLabel>';
                    $indent++;
                    break;

                case 'menu.item':
                    $ui['DropdownMenuItem'] = true;
                    $href = $attrs['href'] ?? $attrs[':href'] ?? null;
                    $iconJsx = $iconName ? '<'.$iconName.' className="mr-2 size-4" />' : '';
                    $variantAttr = ($attrs['variant'] ?? null) === 'danger' ? ' className="text-destructive"' : '';
                    if ($href !== null) {
                        $out[] = $ind().'<DropdownMenuItem asChild'.$variantAttr.'>';
                        $indent++;
                        if (str_contains($href, 'route(')) {
                            $out[] = $ind().$reportTodo('blade', 'route() in menu.item href');
                            $href = '#';
                        }
                        $out[] = $ind().'<Link href="'.trim($href, "'\"").'">'.$iconJsx;
                        $indent++;
                    } else {
                        $out[] = $ind().'<DropdownMenuItem'.$variantAttr.$clickHandler($wireClick, $wireConfirm).'>'.$iconJsx;
                        $indent++;
                    }
                    break;

                case 'menu.separator':
                case 'separator':
                    $ui['Separator'] = true;
                    $out[] = $ind().'<Separator'.($tag === 'menu.separator' ? ' /* DropdownMenuSeparator */' : '').' />';
                    break;

                case 'callout':
                case 'callout.heading':
                case 'callout.text':
                    $ui['Alert'] = true;
                    if ($tag === 'callout') {
                        $out[] = $ind().'<Alert>';
                        $indent++;
                    } elseif ($tag === 'callout.heading') {
                        $out[] = $ind().'<AlertTitle>';
                        $indent++;
                    } else {
                        $out[] = $ind().'<AlertDescription>';
                        $indent++;
                    }
                    break;

                case 'field':
                case 'fieldset':
                    $out[] = $ind().'<div className="grid gap-2'.($class ? ' '.$class : '').'">';
                    $indent++;
                    break;

                case 'label':
                    $ui['Label'] = true;
                    $out[] = $ind().'<Label>';
                    $indent++;
                    break;

                case 'legend':
                    $out[] = $ind().'<p className="text-sm font-medium">';
                    $indent++;
                    break;

                case 'spacer':
                    $out[] = $ind().'<div className="flex-1" />';
                    break;

                case 'icon':
                    // <flux:icon.name /> -> lucide
                    $out[] = $ind().$reportTodo('flux', 'icon (bare <flux:icon>)');
                    break;

                default:
                    // icon.foo form
                    if (str_starts_with($tag, 'icon.')) {
                        $name = $toLucide(substr($tag, 5));
                        $lucide[$name] = true;
                        $out[] = $ind().'<'.$name.' className="size-4" />';
                    } else {
                        $out[] = $ind().$reportTodo('flux', $tag);
                    }
                    break;
            }

            continue;
        }

        // --- flux closing tags
        if (preg_match('/^<\/flux:([\w.-]+)>$/', $line, $m)) {
            $tag = $m[1];
            $close = match ($tag) {
                'select' => ['</SelectContent>', '</Select>'],
                'select.option' => ['</SelectItem>'],
                'button' => ['</Button>'],
                'badge' => ['</Badge>'],
                'heading', 'subheading', 'text' => [$tag === 'heading' ? '</h2>' : '</p>'],
                'link' => ['</Link>'],
                'radio.group' => ['</RadioGroup>'],
                'modal' => ['</DialogContent>', '</Dialog>'],
                'modal.trigger' => ['</DialogTrigger>'],
                'modal.close' => ['</DialogClose>'],
                'dropdown' => ['</DropdownMenu>'],
                'menu' => ['</DropdownMenuContent>'],
                'menu.heading' => ['</DropdownMenuLabel>'],
                'menu.item' => ['</DropdownMenuItem>'],
                'callout' => ['</Alert>'],
                'callout.heading' => ['</AlertTitle>'],
                'callout.text' => ['</AlertDescription>'],
                'field', 'fieldset' => ['</div>'],
                'label' => ['</Label>'],
                'legend' => ['</p>'],
                default => null,
            };
            if ($close === null) {
                continue; // unknown/self-closing — nothing to close
            }
            foreach ($close as $c) {
                $indent = max(0, $indent - 1);
                $out[] = $ind().$c;
            }

            continue;
        }

        // --- <form wire:submit="save"> -> Inertia useForm
        if (preg_match('/^<form([^>]*)wire:submit="?(\w+)"?([^>]*)>$/', $line, $m)) {
            $usesForm = true;
            $out[] = $ind().'<form onSubmit={handle'.ucfirst($m[2]).'}>';
            $indent++;

            continue;
        }

        // --- plain HTML passthrough with interpolation + attr renames
        if (preg_match('/^<(\w[\w-]*)/', $line, $m) || str_starts_with($line, '</')) {
            $html = $line;
            // wire:navigate on <a href> -> <Link>
            if (str_starts_with($line, '<a ') && str_contains($html, 'wire:navigate')) {
                $html = preg_replace('/wire:navigate\s*/', '', $html);
                $html = preg_replace('/^<a /', '<Link ', $html);
                $inertia['Link'] = true;
            }
            // wire:click on plain HTML -> onClick handler stub
            $html = preg_replace_callback(
                '/\s*wire:click="(\w+)\(([^"]*)\)"/',
                function ($m) {
                    $handler = 'handle'.ucfirst($m[1]);
                    $arg = trim($m[2]);
                    $call = $arg !== '' ? $handler.'('.$arg.')' : $handler.'()';

                    return ' onClick={() => '.$call.'}';
                },
                $html
            );
            $html = preg_replace_callback(
                '/\s*wire:click="(\w+)"/',
                fn ($m) => ' onClick={() => handle'.ucfirst($m[1]).'()}',
                $html
            );
            // strip remaining wire:* attributes, reporting once each
            $html = preg_replace_callback(
                '/\s*wire:([\w.-]+)(="[^"]*")?/',
                function ($m) use (&$reportTodo) {
                    $reportTodo('wire', 'wire:'.$m[1]);

                    return '';
                },
                $html
            );
            // @click / x-on:click alpine attrs -> TODO
            $html = preg_replace_callback(
                '/\s*(?:x-on:([\w.-]+)|@([\w.-]+))="[^"]*"/',
                function ($m) use (&$reportTodo) {
                    $reportTodo('blade', '@'.($m[1] ?: $m[2]));

                    return '';
                },
                $html
            );
            // :href="route(...)" etc -> TODO
            $html = preg_replace_callback(
                '/\s*:(\w+)="([^"]*\broute\(|\$wire|auth\(\)|Route::)[^"]*"/',
                function ($m) use (&$reportTodo) {
                    $reportTodo('blade', ':'.$m[1].' binding');

                    return '';
                },
                $html
            );
            // :checked="old(...)" etc leftover bound attrs -> TODO
            $html = preg_replace_callback(
                '/\s*:(\w+)="[^"]*\{\{?[^"]*"/',
                function ($m) use (&$reportTodo) {
                    $reportTodo('blade', ':'.$m[1].' binding');

                    return '';
                },
                $html
            );

            $html = $interpolate($html);
            // class -> className, for -> htmlFor (line may start mid-tag after a multi-line opener)
            $html = str_replace(' class="', ' className="', $html);
            $html = preg_replace('/^class="/', 'className="', $html);
            $html = preg_replace('/\bfor="/', 'htmlFor="', $html);
            $out[] = $ind().$html;

            continue;
        }

        // --- continuation lines of a plain multi-line HTML tag (e.g. `class="..."`)
        if (preg_match('/^[\w:-]+="/', $line) || preg_match('/^class="/', $line)) {
            $html = $interpolate($line);
            $html = preg_replace('/^class="/', 'className="', $html);
            $html = str_replace(' class="', ' className="', $html);
            $out[] = $ind().$html;

            continue;
        }

        // --- @foreach/@forelse handled above; plain text lines
        if (! str_starts_with($line, '<') && ! str_starts_with($line, '@')) {
            $out[] = $ind().$interpolate($line);

            continue;
        }

        // Anything else: raw TODO
        $out[] = $ind().$reportTodo('blade', substr($line, 0, 60));
    }

    return [
        implode("\n", $out),
        [
            'ui' => array_keys($ui),
            'lucide' => array_keys($lucide),
            // $inertia mixes positional values ('Link','router') and key=>true entries
            'inertia' => array_values(array_unique(array_map(
                fn ($k, $v) => is_string($k) ? $k : $v,
                array_keys($inertia),
                $inertia
            ))),
            'usesForm' => $usesForm,
        ],
    ];
};

// ---------------------------------------------------------------------------
// TSX page emitter
// ---------------------------------------------------------------------------

$emitPage = function (array $screen, ?array $lw, string $jsx, array $used, array $routesByName): string {
    $toPascal = fn (string $s): string => str_replace(' ', '', ucwords(str_replace(['-', '_', '/'], ' ', $s)));
    $componentName = $toPascal(basename($screen['path'], '.blade.php'));
    // Disambiguate with parent dir: livewire/security/users/index -> SecurityUsersIndex
    $relParts = explode('/', dirname(str_replace('stubs/resources/views/', '', $screen['path'])));
    $relParts = array_filter($relParts, fn ($p) => ! in_array($p, ['livewire', '.'], true));
    $prefix = $toPascal(implode('/', $relParts));
    $pageName = $prefix.$componentName;

    $stateProps = $lw['public_properties'] ?? [];
    $actions = array_filter(
        $lw['actions'] ?? [],
        fn ($a) => ! in_array($a, ['mount', 'render', '__invoke']) && ! str_starts_with($a, 'updated')
    );

    $imports = [];
    $inertiaImports = array_values(array_unique(array_merge(['Head'], $used['inertia'])));
    sort($inertiaImports);
    $imports[] = 'import { '.implode(', ', $inertiaImports)." } from '@inertiajs/react';";
    if ($used['usesForm']) {
        $imports[] = "import { useForm } from '@inertiajs/react';";
    }
    $imports[] = "import AppLayout from '@/layouts/app-layout';";
    if ($used['ui']) {
        sort($used['ui']);
        $imports[] = 'import { '.implode(', ', $used['ui'])." } from '@/components/ui/*'; // split per shadcn convention";
    }
    if ($used['lucide']) {
        sort($used['lucide']);
        $imports[] = 'import { '.implode(', ', $used['lucide'])." } from 'lucide-react';";
    }
    $imports[] = "import { useState } from 'react';";

    // Props typing from public properties
    $props = [];
    foreach ($stateProps as $p) {
        $props[] = $p.'?: any;';
    }
    $propsTs = $props ? "type Props = {\n    ".implode("\n    ", $props)."\n};" : 'type Props = Record<string, never>;';

    // useState hooks for wire:model-bound props
    $stateHooks = [];
    foreach ($stateProps as $p) {
        $camel = lcfirst($p);
        $stateHooks[] = '    const ['.$camel.', set'.ucfirst($camel).'] = useState<any>('
            .'props.'.$camel.');';
    }

    // action handler stubs — skip computed-property accessors (users, stats, roles...)
    $skipActions = ['assignableRoles', 'roles', 'selectedUser', 'stats', 'users',
        'permissions', 'tokens', 'connectedApps', 'recentNotifications', 'unreadCount',
        'totalCount', 'inheritedPermissionIds', 'inheritedVia', 'user', 'selectedAudit'];
    $handlers = [];
    foreach ($actions as $a) {
        if (in_array($a, $skipActions, true)) {
            continue;
        }
        $handler = 'handle'.ucfirst($a);
        $handlers[] = <<<TS
    function {$handler}(...args: any[]) {
        router.post(route('port.{$a}'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
TS;
    }
    // confirmAction helper if wire:confirm used anywhere
    if (str_contains($jsx, 'confirmAction(')) {
        $handlers[] = <<<'TS'
    function confirmAction(fn: () => void, message: string) {
        // TODO: replace with AlertDialog per shadcn conventions
        if (window.confirm(message)) fn();
    }
TS;
    }

    $handlersTs = $handlers ? "\n".implode("\n", $handlers) : '';
    $importsTs = implode("\n", array_unique($imports));
    $stateHooksTs = implode("\n", $stateHooks);

    return <<<TSX
{$importsTs}

{$propsTs}

export default function {$pageName}(props: Props) {
{$stateHooksTs}{$handlersTs}

    return (
        <AppLayout>
            <Head title="{$pageName}" />
{$jsx}
        </AppLayout>
    );
}
TSX;
};

// ---------------------------------------------------------------------------
// Controller emitter
// ---------------------------------------------------------------------------

$studly = fn (string $seg): string => str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $seg)));

$controllerFor = function (string $fqcn, array $lw): array {
    // App\Livewire\Security\Users\Index -> Security\UsersController, page path security/users/index
    $rel = substr($fqcn, strlen('App\\Livewire\\'));
    $parts = explode('\\', $rel);
    $className = array_pop($parts);
    $ns = implode('\\', $parts);

    // Index -> UsersController (namespace as name), else ClassNameController
    $base = $className === 'Index' ? end($parts) : $className;
    $controller = $base.'Controller';

    // Validation rules: parse from class file
    $rules = [];
    if ($lw['uses_validate'] && is_file(dirname(__DIR__).'/'.$lw['path'])) {
        $src = file_get_contents(dirname(__DIR__).'/'.$lw['path']);
        if (preg_match('/\$this->validate\s*\(\s*\[(.*?)\]\s*\)/s', $src, $m)) {
            $rules = $m[1];
        }
    }

    $pagePath = strtolower(implode('/', array_map(
        fn ($p) => strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $p)),
        array_filter(array_merge($parts, [$className === 'Index' ? 'index' : $className]))
    )));

    $props = array_map(fn ($p) => "'{$p}'", $lw['public_properties']);
    $propsTs = $props ? implode(', ', $props) : '';

    $validateBlock = $rules
        ? "\n        \$validated = \$request->validate([\n            // ported from Livewire \$this->validate()\n            {$rules}\n        ]);\n"
        : "\n        // no validation rules on the source component\n";

    $php = <<<PHP
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\\{$ns};

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from {$fqcn} — review query/prop hydration before production use.
 */
class {$controller}
{
    public function __invoke(): Response
    {
        return Inertia::render('{$pagePath}', [
            // public properties from the Livewire component become props
            // source: {$lw['path']}
            // [{$propsTs}]
        ]);
    }

    public function store(Request \$request)
    {{$validateBlock}
        // TODO: port the component's save/action methods here.
    }
}
PHP;

    return [$ns, $controller, $php];
};

// ---------------------------------------------------------------------------
// Route name resolution from existing stub route files
// ---------------------------------------------------------------------------

$routeFiles = glob($root.'/stubs/routes/web/*.php');
$knownRouteNames = [];
foreach ($routeFiles as $rf) {
    $src = file_get_contents($rf);
    preg_match_all('/->name\(\s*[\'"]([\w.-]+)[\'"]\s*\)/', $src, $m1);
    preg_match_all('/->as\(\s*[\'"]([\w.-]+)[\'"]\s*\)/', $src, $m2);
    $groupPrefix = $m2[1][0] ?? '';
    foreach ($m1[1] as $name) {
        $knownRouteNames[] = $groupPrefix.$name;
    }
}
$knownRouteNames = array_values(array_unique($knownRouteNames));

// ---------------------------------------------------------------------------
// Main: emit pages
// ---------------------------------------------------------------------------

$pageFiles = [];
$controllerFiles = [];
$routesPhp = [];

foreach ($screens as $screen) {
    $rel = $screen['path']; // stubs/resources/views/...
    $bladePath = $root.'/'.$rel;
    if (! is_file($bladePath)) {
        continue;
    }
    // Skip mail views — not UI screens
    if (str_starts_with(str_replace('\\', '/', $rel), 'stubs/resources/views/mail/')) {
        continue;
    }

    $lw = $screen['livewire_component'] ? ($livewireByFqcn[$screen['livewire_component']] ?? null) : null;
    $blade = file_get_contents($bladePath);
    [$jsx, $used] = $translateBlade($blade, $screen, $lw);

    // output path: resources/js/pages/<rel>.tsx (strip livewire/ prefix)
    $pageRel = substr($rel, strlen('stubs/resources/views/'));
    $pageRel = substr($pageRel, 0, -strlen('.blade.php'));
    $isLivewireView = str_starts_with($pageRel, 'livewire/');
    $pageRel = preg_replace('#^livewire/#', '', $pageRel);
    $pageRel = str_replace('_detail', 'detail', $pageRel);

    // A plain blade page (e.g. security/users/index) that only @livewire-embeds a
    // full-page component collides with that component's own view under livewire/.
    // The livewire view is the real screen — don't let the wrapper overwrite it.
    if (isset($pageFiles[$pageRel]) && ! $isLivewireView) {
        continue;
    }
    $pageFiles[$pageRel] = $emitPage($screen, $lw, $jsx, $used, $knownRouteNames);
}

// Controllers: one per livewire component
foreach ($livewireComponents as $lw) {
    $fqcn = 'App\\Livewire\\'.str_replace('/', '\\', substr(
        str_replace('\\', '/', substr($lw['path'], strlen('stubs/app/Livewire/'))),
        0, -4
    ));
    [$ns, $controller, $php] = $controllerFor($fqcn, $lw);
    $rel = ($ns ? str_replace('\\', '/', $ns).'/' : '').$controller.'.php';
    $controllerFiles[$rel] = $php;

    // Route stub: reference real route names from the screen that uses this component
    $screenRoutes = [];
    foreach ($screens as $screen) {
        if ($screen['livewire_component'] === $fqcn && $screen['routes']) {
            $screenRoutes = array_merge($screenRoutes, $screen['routes']);
        }
    }
    $routesPhp[] = [
        'ns' => $ns,
        'controller' => $controller,
        'routes' => array_values(array_unique($screenRoutes)),
    ];
}

// ---------------------------------------------------------------------------
// Write files
// ---------------------------------------------------------------------------

$putFile = function (string $abs, string $content): void {
    $dir = dirname($abs);
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($abs, $content);
};

foreach ($pageFiles as $rel => $tsx) {
    $putFile($outDir.'/resources/js/pages/'.$rel.'.tsx', $tsx);
    $stats['pages']++;
}

foreach ($controllerFiles as $rel => $php) {
    $putFile($outDir.'/app/Http/Controllers/Port/'.$rel, $php);
    $stats['controllers']++;
}

// routes/web/port.php — reference real route names where known
$routeLines = [];
$routeLines[] = '<?php';
$routeLines[] = '';
$routeLines[] = '// Generated by bin/port-screens.php — review before wiring into your app.';
$routeLines[] = 'use Illuminate\Support\Facades\Route;';
$routeLines[] = '';
foreach ($routesPhp as $r) {
    $fqcn = 'App\\Http\\Controllers\\Port\\'.($r['ns'] ? $r['ns'].'\\' : '').$r['controller'];
    $routeLines[] = '// '.$fqcn;
    if ($r['routes']) {
        foreach ($r['routes'] as $name) {
            $exists = in_array($name, $knownRouteNames, true) ? 'exists in stubs' : 'NOT FOUND in stubs';
            $routeLines[] = "// Route::get('...', [{$r['controller']}::class, '__invoke'])->name('{$name}'); // {$exists}";
        }
    } else {
        $routeLines[] = '// (no route names referenced by the source screen)';
    }
    $routeLines[] = '';
}
$putFile($outDir.'/routes/web/port.php', implode("\n", $routeLines));

// ---------------------------------------------------------------------------
// README
// ---------------------------------------------------------------------------

$unmappedFlux = array_keys($stats['unmapped_flux']);
$unmappedWire = array_keys($stats['unmapped_wire']);
$unmappedBlade = array_keys($stats['unmapped_blade']);
sort($unmappedFlux);
sort($unmappedWire);
sort($unmappedBlade);
$unmappedFluxList = $unmappedFlux ? implode(', ', $unmappedFlux) : '(none)';
$unmappedWireList = $unmappedWire ? implode(', ', $unmappedWire) : '(none)';
$unmappedBladeList = $unmappedBlade ? implode(', ', $unmappedBlade) : '(none)';

$readme = <<<MD
# React Port Output

Generated by \`bin/port-screens.php react\`. This is a **template-based port** of the
Livewire/Flux stub screens to Inertia React (TSX) + Laravel controllers + route stubs.
It is a starting point for a React frontend — not a drop-in replacement.

## What was generated

| Output | Count |
|---|---|
| TSX pages (\`resources/js/pages/**.tsx\`) | {$stats['pages']} |
| Controllers (\`app/Http/Controllers/Port/**.php\`) | {$stats['controllers']} |
| Route stubs (\`routes/web/port.php\`) | 1 |
| TODO placeholders (unported constructs) | {$stats['todos']} |

## Mapping table

### Flux components → shadcn/ui

| Flux | React |
|---|---|
| \`flux:input\` | \`Input\` + \`Label\` |
| \`flux:textarea\` | \`Textarea\` |
| \`flux:select\` / \`flux:select.option\` | \`Select\` / \`SelectItem\` |
| \`flux:button\` | \`Button\` |
| \`flux:badge\` | \`Badge\` |
| \`flux:modal\` (+trigger/close) | \`Dialog\` (+Trigger/Close) |
| \`flux:dropdown\` / \`flux:menu*\` | \`DropdownMenu\` family |
| \`flux:callout*\` | \`Alert\` family |
| \`flux:checkbox\` | \`Checkbox\` + \`Label\` |
| \`flux:switch\` | \`Switch\` |
| \`flux:radio.group\` / \`flux:radio\` | \`RadioGroup\` / \`RadioGroupItem\` |
| \`flux:separator\` / \`flux:menu.separator\` | \`Separator\` |
| \`flux:heading\` / \`flux:subheading\` / \`flux:text\` | Tailwind \`<h*>\` / \`<p>\` |
| \`flux:field\` / \`flux:fieldset\` / \`flux:legend\` | \`<div className="grid gap-2">\` / \`<Label>\` |
| \`flux:icon.foo\` | \`<Foo />\` from \`lucide-react\` |
| \`flux:spacer\` | \`<div className="flex-1" />\` |
| navlist/sidebar | kit \`AppLayout\` sidebar (pages wrapped in \`<AppLayout>\`) |

### Livewire directives → React/Inertia

| Directive | React |
|---|---|
| \`wire:model\` / \`wire:model.live\` | \`useState\` + controlled input (\`value\`/\`onChange\`) |
| \`wire:click="action"\` | \`onClick\` → \`router.post(route('port.action'))\` handler stub |
| \`wire:submit="save"\` | \`<form onSubmit={handleSave}>\` + \`useForm\` |
| \`wire:confirm\` | \`confirmAction()\` helper (TODO: replace with \`AlertDialog\`) |
| \`wire:navigate\` | \`<Link>\` from \`@inertiajs/react\` |
| \`wire:loading\` | \`processing\` flag from \`useForm\` (TODO placeholder emitted) |
| \`wire:key\` | React \`key\` prop (TODO placeholder emitted) |

## TODO placeholder report

Constructs with **no mapping rule** were emitted as \`{/* TODO: port ... */}\` comments.

- Unmapped flux tags: {$stats['todos']} total placeholders
  - flux: {$unmappedFluxList}
  - wire: {$unmappedWireList}
  - blade: {$unmappedBladeList}

## How to copy into a real project

1. Create a React starter kit project (\`laravel new myapp --react\`).
2. Copy \`resources/js/pages/**\` into the project's \`resources/js/pages/\`.
3. Copy \`app/Http/Controllers/Port/**\` into \`app/Http/Controllers/\` (rename/adjust
   namespaces as you see fit — these are stubs).
4. Review \`routes/web/port.php\` — uncomment and wire the routes you want, then merge
   into \`routes/web.php\`. Route names that exist in the original stubs are marked.
5. Resolve each \`{/* TODO: port ... */}\` — most are \`@if\` guards (convert to
   \`{cond && ...}\`), \`route()\` calls (import from Wayfinder-generated routes), and
   \`@can\` authorization checks (pass permissions as props or use a policy composable).
6. Controllers are skeletons: fill in the query that hydrates each prop (the Livewire
   component's \`render()\`/\`mount()\` is the reference implementation).

## Known gaps (by design)

- No attempt to port Blade \`@if/@foreach/@can\` control flow — TODOs are emitted.
- \`@php\` blocks, Alpine \`x-data\`/\`@click\`, and \`\$dispatch\` events are TODOs.
- Auth screens (\`auth/*.blade.php\`) are ported as pages, but a real React project
  should use the starter kit's own auth screens instead.
- The \`Port\` controller namespace is deliberate — treat these as templates to move
  into your own controller structure.
MD;

$putFile($outDir.'/README.md', $readme);

// ---------------------------------------------------------------------------
// Report
// ---------------------------------------------------------------------------

fwrite(STDOUT, sprintf(
    "Wrote %d pages, %d controllers, routes/web/port.php, README.md\n",
    $stats['pages'],
    $stats['controllers']
));
fwrite(STDOUT, sprintf("TODO placeholders: %d\n", $stats['todos']));
fwrite(STDOUT, 'Unmapped flux tags: '.($unmappedFlux ? implode(', ', $unmappedFlux) : '(none)')."\n");
fwrite(STDOUT, 'Unmapped wire directives: '.($unmappedWire ? implode(', ', $unmappedWire) : '(none)')."\n");
fwrite(STDOUT, 'Unmapped blade constructs: '.($unmappedBlade ? implode(', ', $unmappedBlade) : '(none)')."\n");
