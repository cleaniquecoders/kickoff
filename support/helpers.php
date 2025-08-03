<?php

use Symfony\Component\Console\Output\OutputInterface;

function step(string $message, callable $callback, OutputInterface $output): void
{
    $output->write("⏳ $message...");
    try {
        $callback();
        $output->writeln(' ✅');
    } catch (\Throwable $e) {
        $output->writeln(' ❌');
        $output->writeln("<error>{$e->getMessage()}</error>");
    }
}

/**
 * Run script silently.
 */
function runSilent(string $cmd)
{
    shell_exec("$cmd > /dev/null 2>&1");
}

/**
 * Install Composer packages.
 */
function installPackages(array $require, array $requireDev): void
{
    if ($require) {
        runSilent('composer require '.implode(' ', $require));
    }
    if ($requireDev) {
        runSilent('composer require --dev '.implode(' ', $requireDev));
    }
}

/**
 * Create a directory if it does not exist.
 */
function ensureDir(string $path, int $mode = 0755): void
{
    if (! is_dir($path)) {
        @mkdir($path, $mode, true);
    }
}

/**
 * Create or update a file with content.
 */
function putFile(string $path, string $content): void
{
    file_put_contents($path, $content);
}

/**
 * Recursively copy a source directory to a destination using iterators.
 */
function copyRecursively(string $src, string $dst): void
{
    ensureDir($dst);

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $targetPath = $dst.DIRECTORY_SEPARATOR.$iterator->getSubPathName();
        if ($item->isDir()) {
            ensureDir($targetPath);
        } else {
            copy($item->getPathname(), $targetPath);
        }
    }
}
