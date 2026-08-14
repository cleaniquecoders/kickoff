<?php

/**
 * Scan stubs/ template and emit stubs/manifest/screens.json.
 * Standalone: no framework boot, no composer autoload.
 */
$root = dirname(__DIR__);
$viewsDir = $root.'/stubs/resources/views';
$livewireDir = $root.'/stubs/app/Livewire';
$manifestDir = $root.'/stubs/manifest';
$outFile = $manifestDir.'/screens.json';

$excludedSegments = ['/components/', '/layouts/', '/partials/', '/flux/', '/vendor/'];

$toRelative = fn (string $abs): string => str_replace('\\', '/', substr($abs, strlen($root) + 1));

$studly = fn (string $seg): string => str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $seg)));

$isExcluded = function (string $rel) use ($excludedSegments): bool {
    foreach ($excludedSegments as $seg) {
        if (str_contains($rel, $seg)) {
            return true;
        }
    }

    return false;
};

// --- Collect all blade views -------------------------------------------------

$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsDir, FilesystemIterator::SKIP_DOTS)
);
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $bladeFiles[] = $file->getPathname();
    }
}
sort($bladeFiles);

// --- Livewire classes (path lookup for screen matching) ----------------------

$livewireFiles = [];
if (is_dir($livewireDir)) {
    $lwIterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($livewireDir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($lwIterator as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
            $livewireFiles[] = $file->getPathname();
        }
    }
    sort($livewireFiles);
}

// map: relative-class-key (e.g. "Admin/Roles/Index") => FQCN
$livewireByKey = [];
foreach ($livewireFiles as $file) {
    $rel = str_replace('\\', '/', substr($file, strlen($livewireDir) + 1)); // Admin/Roles/Index.php
    $key = substr($rel, 0, -4);
    $livewireByKey[$key] = 'App\\Livewire\\'.str_replace('/', '\\', $key);
}

// --- Extractors --------------------------------------------------------------

$extractScreen = function (string $file) use ($toRelative, $studly, $livewireByKey): array {
    $content = file_get_contents($file);
    $rel = $toRelative($file);

    // flux components: <flux:tag ...> ; <flux:icon.name> normalises to "icon"
    preg_match_all('/<flux:([a-zA-Z0-9._-]+)/', $content, $m);
    $flux = [];
    foreach ($m[1] as $tag) {
        $flux[] = str_starts_with($tag, 'icon.') ? 'icon' : $tag;
    }

    // wire directives: keep base + ".live" mode, drop other modifiers
    preg_match_all('/wire:([a-zA-Z][\w-]*(?:\.[\w.-]+)?)/', $content, $m);
    $wire = [];
    foreach ($m[1] as $directive) {
        $parts = explode('.', $directive);
        $base = array_shift($parts);
        $wire[] = 'wire:'.$base.(in_array('live', $parts, true) ? '.live' : '');
    }

    // icons: static icon="name" attributes + <flux:icon.name> tags
    preg_match_all('/(?<![:\w-])icon="([a-z0-9-]+)"/', $content, $m1);
    preg_match_all('/<flux:icon\.([a-z0-9-]+)/', $content, $m2);
    $icons = array_merge($m1[1], $m2[1]);

    // routes: route('name'...)
    preg_match_all('/\broute\(\s*[\'"]([\w.-]+)[\'"]/', $content, $m);
    $routes = $m[1];

    // livewire component by naming convention: admin/roles/index => App\Livewire\Admin\Roles\Index
    $viewRel = substr($toRelative($file), strlen('stubs/resources/views/'));
    $viewRel = substr($viewRel, 0, -strlen('.blade.php'));
    $viewRel = preg_replace('#^livewire/#', '', $viewRel);
    $key = implode('/', array_map($studly, explode('/', $viewRel)));
    $livewireComponent = $livewireByKey[$key] ?? null;

    $sort = function (array $items): array {
        $items = array_values(array_unique($items));
        sort($items);

        return $items;
    };

    return [
        'path' => $rel,
        'flux_components' => $sort($flux),
        'wire_directives' => $sort($wire),
        'icons' => $sort($icons),
        'routes' => $sort($routes),
        'livewire_component' => $livewireComponent,
    ];
};

$extractLivewire = function (string $file) use ($toRelative): array {
    $content = file_get_contents($file);

    preg_match('/^\s*(?:final\s+|abstract\s+)?class\s+(\w+)/m', $content, $m);
    $class = $m[1] ?? null;

    // public properties (exclude constants and static)
    preg_match_all(
        '/^\s*public\s+(?!const\b|static\b|function\b)(?:readonly\s+)?(?:\??[\w\\\\|]+\s+)?\$(\w+)/m',
        $content,
        $m
    );
    $properties = $m[1];

    // public functions
    preg_match_all('/^\s*public\s+function\s+(\w+)\s*\(/m', $content, $m);
    $actions = $m[1];

    // attributes of interest
    preg_match_all(
        '/#\[(Computed|On|Url|Layout|Title)(?:\(\s*[\'"]([^\'"]*)[\'"]\s*\))?\]/',
        $content,
        $m,
        PREG_SET_ORDER
    );
    $attributes = [];
    foreach ($m as $attr) {
        $entry = ['name' => $attr[1]];
        if (isset($attr[2]) && $attr[2] !== '') {
            $entry['value'] = $attr[2];
        }
        $attributes[] = $entry;
    }
    usort($attributes, fn ($a, $b) => [$a['name'], $a['value'] ?? ''] <=> [$b['name'], $b['value'] ?? '']);

    // traits: indented "use Foo, Bar;" inside class body
    preg_match_all('/^\s+use\s+([\\\\\w]+(?:\s*,\s*[\\\\\w]+)*)\s*;/m', $content, $m);
    $traits = [];
    foreach ($m[1] as $list) {
        foreach (explode(',', $list) as $trait) {
            $trait = trim($trait);
            $traits[] = substr($trait, (int) strrpos($trait, '\\') + 1) ?: $trait;
        }
    }

    $usesValidate = (bool) preg_match('/\$this->validate\s*\(/', $content);

    $sort = function (array $items): array {
        $items = array_values(array_unique($items));
        sort($items);

        return $items;
    };

    return [
        'class' => $class,
        'path' => $toRelative($file),
        'public_properties' => $sort($properties),
        'actions' => $sort($actions),
        'attributes' => $attributes,
        'traits' => $sort($traits),
        'uses_validate' => $usesValidate,
    ];
};

// --- Build manifest ----------------------------------------------------------

$screens = [];
$excludedCount = 0;
foreach ($bladeFiles as $file) {
    if ($isExcluded($toRelative($file))) {
        $excludedCount++;

        continue;
    }
    $screens[] = $extractScreen($file);
}

$livewire = array_map($extractLivewire, $livewireFiles);
usort($livewire, fn ($a, $b) => strcmp($a['class'] ?? '', $b['class'] ?? ''));

$collect = function (string $key) use ($screens): array {
    $all = [];
    foreach ($screens as $screen) {
        $all = array_merge($all, $screen[$key]);
    }
    $all = array_values(array_unique($all));
    sort($all);

    return $all;
};

$manifest = [
    'version' => 1,
    'generated_by' => 'bin/build-screen-manifest.php',
    'summary' => [
        'screen_count' => count($screens),
        'livewire_class_count' => count($livewire),
        'excluded_view_count' => $excludedCount,
        'flux_components' => $collect('flux_components'),
        'wire_directives' => $collect('wire_directives'),
        'icons' => $collect('icons'),
    ],
    'screens' => $screens,
    'livewire_components' => $livewire,
];

if (! is_dir($manifestDir)) {
    mkdir($manifestDir, 0755, true);
}

file_put_contents(
    $outFile,
    json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
);

fwrite(STDOUT, sprintf(
    "Wrote %s (%d screens, %d livewire classes, %d excluded views)\n",
    $toRelative($outFile),
    count($screens),
    count($livewire),
    $excludedCount
));
