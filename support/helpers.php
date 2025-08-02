<?php

/**
 * Elegant Project Setup Script
 *
 * This script automates the following for a Laravel project:
 * - Installs required and development Composer packages
 * - Updates composer.json with scripts and helpers autoload
 * - Creates configuration files: rector.php, phpstan.neon.dist, pint.json
 * - Sets up directories: support/, routes/, tinker/, docs/
 * - Generates support/helpers.php with auto-loader
 * - Organizes route files into subdirectories
 * - Creates todo.md and updates .gitignore
 * - Sets up tinker/ and docs/README.md with TOC
 * - Creates executable bin/ scripts for project automation
 * - Sets up .config directory with Nginx and Supervisor templates
 * - Sets up VS Code IDE Extension Recommendation
 * - Generates tests/Feature/ArchitectureTest.php with Pest Arch rules
 * - Creates documentation templates (CHANGELOG, CONTRIBUTING, etc.)
 * - Publishes package configs and migrations via artisan
 * - Creates GitHub Actions workflow YAMLs for CI/CD
 * - Runs artisan tasks: config:clear, migrate, storage:link
 * - Outputs clean progress and completion messages
 */

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

function run(string $cmd)
{
    shell_exec("$cmd");
}

/**
 * Install Composer packages.
 */
function installPackages(array $require, array $requireDev, string $workingDir): void
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
 * Create a file with content if it does not exist.
 */
function ensureFile(string $path, string $content): void
{
    if (! file_exists($path)) {
        file_put_contents($path, $content);
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
 * Setup a directory and file.
 */
function setupDirAndFile(string $dir, string $file, string $content): void
{
    ensureDir($dir);
    ensureFile($file, $content);
}

/**
 * Create bash scripts
 */
function createBinScript(string $binDir, string $name, string $content): void
{
    $path = "$binDir/$name";
    ensureFile($path, $content);
    @chmod($path, 0755);
}

/**
 * Replace all ${PROJECT_NAME} in a string with the PROJECT_NAME constant.
 */
function replaceProjectName(string $content): string
{
    return str_replace('${PROJECT_NAME}', PROJECT_NAME, $content);
}
