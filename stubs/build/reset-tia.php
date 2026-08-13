<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Discard the Test Impact Analysis graph
|--------------------------------------------------------------------------
|
| Used by `composer test-tia-fresh`, in preference to Pest's own `--fresh`
| flag: re-recording into an empty directory takes about 75 seconds, whereas
| `--fresh` takes about 155 for the same result.
|
| The graph lives outside the repository — `~/.pest/tia/<project-key>/`, keyed
| off the normalised git remote — so `pest --baseline` is the only reliable way
| to find it. Nothing here touches the repo.
|
*/

$pest = __DIR__.'/../vendor/bin/pest';

if (! is_file($pest)) {
    fwrite(STDERR, "vendor/bin/pest not found — run composer install first.\n");

    exit(1);
}

exec(escapeshellarg(PHP_BINARY).' '.escapeshellarg($pest).' --baseline 2>/dev/null', $output, $status);

$baseline = trim(implode('', $output));

if ($status !== 0 || $baseline === '') {
    fwrite(STDERR, "Could not determine the TIA baseline directory.\n");

    exit(1);
}

// Guard against ever pointing this at something that is not a Pest baseline.
if (! str_contains(str_replace('\\', '/', $baseline), '/.pest/tia/')) {
    fwrite(STDERR, "Refusing to delete an unexpected path: {$baseline}\n");

    exit(1);
}

if (! is_dir($baseline)) {
    echo "No TIA graph to discard.\n";

    exit(0);
}

$entries = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseline, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST,
);

foreach ($entries as $entry) {
    /** @var SplFileInfo $entry */
    $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
}

rmdir($baseline);

echo "Discarded the TIA graph at {$baseline}\n";
