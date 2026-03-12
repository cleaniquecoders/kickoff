<?php

use Symfony\Component\Console\Output\OutputInterface;

function step(string $message, callable $callback, OutputInterface $output, bool $verbose = false, bool $critical = true): void
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
        if ($critical) {
            throw $e;
        }
    }
}

/**
 * Run script with optional verbosity.
 *
 * @throws RuntimeException if the command exits with a non-zero status
 */
function runCommand(string $cmd, bool $verbose = false): void
{
    if ($verbose) {
        passthru($cmd, $exitCode);
    } else {
        exec("$cmd 2>&1", $output, $exitCode);
    }

    if ($exitCode !== 0) {
        $errorOutput = $verbose ? '' : implode("\n", $output ?? []);
        throw new RuntimeException("Command failed (exit code $exitCode): $cmd".($errorOutput ? "\n$errorOutput" : ''));
    }
}

/**
 * Install Composer packages with verbosity option.
 */
function installPackages(array $require, array $requireDev, string $path, bool $verbose = false): void
{
    $workingDir = "--working-dir=".escapeshellarg($path);
    $lockFile = $path.'/composer.lock';

    if ($require) {
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }
        runCommand("composer require $workingDir ".implode(' ', $require), $verbose);
    }
    if ($requireDev) {
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }
        runCommand("composer require --dev $workingDir ".implode(' ', $requireDev), $verbose);
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
    runCommand('git commit -m "'.addslashes($message).'"', $verbose);
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
