<?php

use Symfony\Component\Console\Output\OutputInterface;

function step(string $message, callable $callback, OutputInterface $output, bool $verbose = false): void
{
    $output->write("⏳ $message...");
    try {
        $callback();
        $output->writeln(' ✅');
        if ($verbose) {
            $output->writeln("<info>Step '$message' completed successfully.</info>");
        }
    } catch (\Throwable $e) {
        $output->writeln(' ❌');
        $output->writeln("<error>{$e->getMessage()}</error>");
        if ($verbose && $e->getTraceAsString()) {
            $output->writeln("<comment>Trace:</comment> {$e->getTraceAsString()}");
        }
    }
}

/**
 * Run script with optional verbosity.
 */
function runCommand(string $cmd, bool $verbose = false)
{
    if ($verbose) {
        passthru($cmd);
    } else {
        shell_exec("$cmd > /dev/null 2>&1");
    }
}

/**
 * Install Composer packages with verbosity option.
 */
function installPackages(array $require, array $requireDev, string $path, bool $verbose = false): void
{
    if ($require) {
        runCommand('rm composer.lock');
        runCommand('composer require '.implode(' ', $require), $verbose);
    }
    if ($requireDev) {
        runCommand('rm composer.lock');
        runCommand('composer require --dev '.implode(' ', $requireDev), $verbose);
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
 * Git add all changes and commit with a message.
 */
function gitCommit(string $message, bool $verbose = false): void
{
    runCommand('git add -A', $verbose);
    runCommand('git commit -m "' . addslashes($message) . '"', $verbose);
}

/**
 * Recursively copy a source directory to a destination using iterators with verbosity.
 */
function copyRecursively(string $src, string $dst, bool $verbose = false, ?OutputInterface $output = null): void
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
            if ($verbose && $output) {
                $output->writeln("<info>Created directory:</info> $targetPath");
            }
        } else {
            copy($item->getPathname(), $targetPath);
            if ($verbose && $output) {
                $output->writeln("<info>Copied file:</info> {$item->getPathname()} to $targetPath");
            }
        }
    }
}
