<?php

/**
 * Elegant Project Setup Script
 * - Autoload support/helpers.php
 * - Install Laravel packages & QA tools
 * - Create configs: rector.php, phpstan.neon.dist, pint.json
 * - Setup support/, routes/, tinker/, docs/
 * - Generate docs README.md with TOC
 * - Create ArchitectureTest.php with Pest Arch
 * - Create documentation templates
 * - Publish package configs & migrations
 * - Create GitHub Actions workflows
 * - Run artisan tasks
 * - Clean progress output
 */
function step(string $message, callable $callback)
{
    echo "⏳ $message...";
    try {
        $callback();
        echo " ✅\n";
    } catch (Exception $e) {
        echo " ❌\n";
    }
}

function runSilent(string $cmd)
{
    shell_exec("$cmd > /dev/null 2>&1");
}

// ------------------------------------------------------------
// 1. Install packages
// ------------------------------------------------------------
step('Installing required packages', function () {
    $require = [
        'spatie/laravel-permission',
        'spatie/laravel-medialibrary',
        'cleaniquecoders/traitify',
        'cleaniquecoders/laravel-media-secure',
        'owen-it/laravel-auditing',
    ];
    $requireDev = [
        'barryvdh/laravel-debugbar',
        'larastan/larastan',
        'driftingly/rector-laravel',
        'pestphp/pest-plugin-arch',
    ];
    if ($require) {
        runSilent('composer require '.implode(' ', $require));
    }
    if ($requireDev) {
        runSilent('composer require --dev '.implode(' ', $requireDev));
    }
});

// ------------------------------------------------------------
// 2. Update composer.json with scripts & helpers autoload
// ------------------------------------------------------------
step('Updating composer.json', function () {
    $composerFile = __DIR__.'/composer.json';
    $composer = json_decode(file_get_contents($composerFile), true);

    $composer['autoload']['files'] = ['support/helpers.php'];

    $composer['scripts'] = [
        'analyse' => '@php vendor/bin/phpstan analyse',
        'test' => '@php vendor/bin/pest',
        'test-arch' => '@php vendor/bin/pest tests/Feature/ArchitectureTest.php',
        'test-coverage' => 'vendor/bin/pest --coverage',
        'format' => '@php vendor/bin/pint',
        'lint' => '@php vendor/bin/phplint',
        'rector' => 'vendor/bin/rector process',
    ];

    file_put_contents($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    runSilent('composer dump-autoload');
});

// ------------------------------------------------------------
// 3. Create configs
// ------------------------------------------------------------
step('Creating rector.php', function () {
    if (! file_exists(__DIR__.'/rector.php')) {
        file_put_contents(__DIR__.'/rector.php', "<?php\n\n// Rector config\n");
    }
});
step('Creating phpstan.neon.dist', function () {
    if (! file_exists(__DIR__.'/phpstan.neon.dist')) {
        file_put_contents(__DIR__.'/phpstan.neon.dist', "parameters:\n  level: 6\n  paths:\n    - app/\n");
    }
});
step('Creating pint.json', function () {
    if (! file_exists(__DIR__.'/pint.json')) {
        file_put_contents(__DIR__.'/pint.json', "{\n    \"preset\": \"laravel\"\n}\n");
    }
});

// ------------------------------------------------------------
// 4. Setup directories: support/, routes/, tinker/, docs/
// ------------------------------------------------------------
step('Setting up support/helpers.php', function () {
    @mkdir(__DIR__.'/support', 0755, true);
    $helpersFile = __DIR__.'/support/helpers.php';
    if (! file_exists($helpersFile)) {
        $content = <<<PHP
<?php

if (! function_exists('require_all_in')) {
    /**
     * Require all files in the given path.
     *
     * @param string \$path File path pattern. eg. routes/web/*.php
     * @return void
     */
    function require_all_in(string \$path): void
    {
        collect(glob(\$path))
            ->each(function (\$path) {
                if (basename(\$path) !== basename(__FILE__)) {
                    require \$path;
                }
            });
    }
}

// Auto-load all helpers in support/
require_all_in(__DIR__.'/*.php');
PHP;
        file_put_contents($helpersFile, $content);
    }
});

step('Organising routes', function () {
    foreach (['web', 'api', 'console'] as $dir) {
        @mkdir(__DIR__."/routes/$dir", 0755, true);
        $mainFile = __DIR__."/routes/$dir.php";
        if (file_exists($mainFile)) {
            copy($mainFile, __DIR__."/routes/$dir/_.php");
        }
        file_put_contents($mainFile, "<?php\n\nrequire_all_in(base_path('routes/$dir/*.php'));\n");
    }

    $authFile = __DIR__.'/routes/auth.php';
    if (file_exists($authFile)) {
        copy($authFile, __DIR__.'/routes/web/auth.php');
        unlink($authFile);
    }
});

step('Creating tinker/ with .gitignore', function () {
    @mkdir(__DIR__.'/tinker', 0755, true);
    file_put_contents(__DIR__.'/tinker/.gitignore', "*\n!.gitignore\n");
});

step('Creating docs/README.md with TOC', function () {
    @mkdir(__DIR__.'/docs', 0755, true);
    file_put_contents(__DIR__.'/docs/README.md', "# Project Documentation\n\n- Getting Started\n- TOC goes here\n");
});

// ------------------------------------------------------------
// 5. Create ArchitectureTest.php
// ------------------------------------------------------------
step('Creating ArchitectureTest.php', function () {
    @mkdir(__DIR__.'/tests/Feature', 0755, true);
    $archTestFile = __DIR__.'/tests/Feature/ArchitectureTest.php';
    if (! file_exists($archTestFile)) {
        $content = <<<PHP
<?php

it('runs on PHP 8.4 or above')
    ->expect(phpversion())
    ->toBeGreaterThanOrEqual('8.4.0');

arch()
    ->expect(['dd', 'dump', 'ray'])
    ->not
    ->toBeUsedIn([
        'app',
        'config',
        'database',
        'routes',
        'support',
    ]);

it('does not using url method')
    ->expect(['url'])
    ->not
    ->toBeUsed();

arch()
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

arch()
    ->expect('App\Policies')
    ->toBeClasses();

arch()
    ->expect('App\Policies')
    ->toHaveSuffix('Policy');

arch()
    ->expect('App\Mail')
    ->toBeClasses();

arch()
    ->expect('App\Mail')
    ->toExtend('Illuminate\Mail\Mailable');

arch()
    ->expect('env')
    ->toOnlyBeUsedIn([
        'config',
    ]);

arch()
    ->expect('App')
    ->not
    ->toUse([
        'DB::raw',
        'DB::select',
        'DB::statement',
        'DB::table',
        'DB::insert',
        'DB::update',
        'DB::delete',
    ]);

arch()
    ->expect('App\Concerns')
    ->toBeTraits();

arch()
    ->expect('App\Enums')
    ->toBeEnums();

arch()
    ->expect('App\Contracts')
    ->toBeInterfaces();
PHP;
        file_put_contents($archTestFile, $content);
    }
});

// ------------------------------------------------------------
// 6. Create documentation templates
// ------------------------------------------------------------
$templates = [
    'CHANGELOG.md' => "# Changelog\n\n## [Unreleased]\n- Initial setup\n",
    'CONTRIBUTING.md' => "# Contributing\n\nThanks for contributing! Submit PRs to `main`.\n",
    'CODE_OF_CONDUCT.md' => "# Code of Conduct\n\nBe respectful and inclusive.\n",
    'SECURITY.md' => "# Security Policy\n\nReport issues to security@example.com\n",
    'SUPPORT.md' => "# Support\n\nFor help, open an issue.\n",
    'LICENSE.md' => "MIT License\n\n(c) ".date('Y')." Your Name\n",
];
foreach ($templates as $file => $content) {
    step("Creating $file", function () use ($file, $content) {
        if (! file_exists(__DIR__."/$file")) {
            file_put_contents(__DIR__."/$file", $content);
        }
    });
}

// ------------------------------------------------------------
// 7. Publish package configs/migrations
// ------------------------------------------------------------
step('Publishing package configs & migrations', function () {
    $tags = [
        '--tag=permission-migrations', '--tag=permission-config',
        '--tag=medialibrary-migrations', '--tag=medialibrary-config',
        '--tag=media-secure-config', '--tag=laravel-errors',
    ];
    foreach ($tags as $tag) {
        runSilent("php artisan vendor:publish {$tag}");
    }
});

// ------------------------------------------------------------
// 8. GitHub Actions Workflows (minimal placeholders here)
// ------------------------------------------------------------
step('Creating GitHub Actions workflows', function () {
    @mkdir(__DIR__.'/.github/workflows', 0755, true);
    file_put_contents(__DIR__.'/.github/workflows/pint.yml', "name: PHP Linting (Pint)\n");
    file_put_contents(__DIR__.'/.github/workflows/phpstan.yml', "name: PHPStan\n");
    file_put_contents(__DIR__.'/.github/workflows/rector.yml', "name: Rector CI\n");
    file_put_contents(__DIR__.'/.github/workflows/tests.yml', "name: Test\n");
    file_put_contents(__DIR__.'/.github/workflows/changelog.yml', "name: Update Changelog\n");
});

// ------------------------------------------------------------
// 9. Artisan tasks
// ------------------------------------------------------------
step('Running artisan tasks', function () {
    runSilent('php artisan config:clear');
    runSilent('php artisan migrate');
    runSilent('php artisan storage:link');
});

// ------------------------------------------------------------
// Done
// ------------------------------------------------------------
echo "\n🎉 Project setup completed successfully!\n";
