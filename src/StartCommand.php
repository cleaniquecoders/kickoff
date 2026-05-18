<?php

namespace CleaniqueCoders\Kickoff\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'start')]
class StartCommand extends Command
{
    protected string $projectOwner;

    protected string $projectName;

    protected ?string $projectPath;

    const PLACEHOLDER_PROJECT_NAME = '${PROJECT_NAME}';

    const PLACEHOLDER_OWNER = '${OWNER}';

    protected function configure(): void
    {
        $this
            ->setDescription('Kickoff a new Laravel project setup')
            ->addArgument('owner', InputArgument::REQUIRED, 'The project owner.')
            ->addArgument('name', InputArgument::REQUIRED, 'The project name.')
            ->addArgument('path', InputArgument::OPTIONAL, 'The project path.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview what would be done without making changes.')
            ->addOption('skip-packages', null, InputOption::VALUE_NONE, 'Skip Composer and NPM package installation.')
            ->addOption('skip-npm', null, InputOption::VALUE_NONE, 'Skip NPM package installation only.');
    }

    public function getProjectName(): string
    {
        return $this->projectName;
    }

    public function getProjectPath(): string
    {
        return $this->projectPath;
    }

    public function getProjectOwner(): string
    {
        return $this->projectOwner;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $verbose = $output->isVerbose() || $output->isVeryVerbose() || $output->isDebug();
        $dryRun = $input->getOption('dry-run');
        $skipPackages = $input->getOption('skip-packages');
        $skipNpm = $input->getOption('skip-npm');

        $this->projectOwner = $projectOwner = $input->getArgument('owner');
        $this->projectName = $projectName = $input->getArgument('name');
        $this->projectPath = $projectPath = $input->getArgument('path');

        if (empty($projectPath)) {
            // If no path provided, create project in current directory with project name as subdirectory
            $projectPath = getcwd().DIRECTORY_SEPARATOR.$projectName;
        }

        $this->projectPath = $projectPath = normalizePath($projectPath);

        if ($dryRun) {
            return $this->dryRun($output, $projectOwner, $projectName, $projectPath, $skipPackages, $skipNpm);
        }

        // Check if we need to create a new Laravel project
        $needsCreation = ! file_exists($projectPath) || ! file_exists($projectPath.'/composer.json') || ! file_exists($projectPath.'/artisan');

        if ($needsCreation) {
            if (! $this->createLaravelProject($output, $projectPath, $projectName, $verbose)) {
                return Command::FAILURE;
            }
        }

        if (! $this->validateProject($output)) {
            return Command::FAILURE;
        }

        $output->writeln("\n🎉 Let's kickoff your <info>$projectOwner/$projectName</info> now!\n");

        $this->copyStubs($output, $verbose);
        $this->setupComposer($output, $verbose);
        $this->setupProjectName($output, $verbose);
        $this->setupEnvironmentFile($output, $verbose);

        if (! $skipPackages) {
            $this->installPackages($output, $verbose, $skipNpm);
        } else {
            $output->writeln("\n⏭️  Skipping package installation (--skip-packages)\n");
        }

        $this->setupDatabase($output, $verbose);
        $this->runTasks($output, $verbose);
        $this->restoreGitIgnoreFiles($output, $verbose);
        gitCommit('Kickoff project setup', $verbose);

        $output->writeln("\n🎉 Project setup completed successfully!\n");

        $this->printPostInstallInfo($output, $projectName);

        return Command::SUCCESS;
    }

    private function printPostInstallInfo(OutputInterface $output, string $projectName): void
    {
        $projectPath = $this->getProjectPath();

        $output->writeln('<comment>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</comment>');
        $output->writeln('<info>📋 Next steps — configure your .env before running the app</info>');
        $output->writeln('<comment>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</comment>');
        $output->writeln('');
        $output->writeln("Project ready at: <info>$projectPath</info>");
        $output->writeln('');
        $output->writeln('<info>1. Credentials & secrets</info> — replace every <comment>CHANGE_ME_BEFORE_DEPLOY</comment> in .env:');
        $output->writeln('   • <comment>SUPERADMIN_PASSWORD</comment>      — initial admin login password');
        $output->writeln('   • <comment>DB_PASSWORD</comment> / <comment>DB_ROOT_PASSWORD</comment> — MySQL credentials');
        $output->writeln('   • <comment>REDIS_PASSWORD</comment>           — leave blank if local Redis has no auth');
        $output->writeln('   • <comment>MEILI_MASTER_KEY</comment>         — Meilisearch master key');
        $output->writeln('   • <comment>MINIO_ROOT_USER</comment> / <comment>MINIO_ROOT_PASSWORD</comment> — MinIO S3 credentials');
        $output->writeln('');
        $output->writeln('<info>2. Cache / session / queue drivers</info> — defaults assume Redis:');
        $output->writeln('   • <comment>CACHE_DRIVER=redis</comment>   • <comment>SESSION_DRIVER=redis</comment>   • <comment>QUEUE_CONNECTION=sync</comment>');
        $output->writeln('   Switch any to <comment>file</comment> / <comment>database</comment> if you are not running Redis locally.');
        $output->writeln('');
        $output->writeln('<info>3. Mail</info> — defaults to Mailpit on <comment>localhost:1025</comment>. Update <comment>MAIL_*</comment> for SMTP/SES/etc.');
        $output->writeln('');
        $output->writeln('<info>4. Application settings</info> (site name, mail-from, notifications) live in the');
        $output->writeln('   <comment>database</comment> via Spatie Settings — manage them at <info>/admin/settings</info> after login.');
        $output->writeln('   Do NOT put these in .env.');
        $output->writeln('');
        $output->writeln('<info>5. Local services</info> — Docker Compose ships MySQL, Redis, Mailpit, Meilisearch, MinIO:');
        $output->writeln("   <comment>cd $projectName && docker compose up -d</comment>");
        $output->writeln('');
        $output->writeln('<info>6. Run the app</info>:');
        $output->writeln("   <comment>cd $projectName && composer dev</comment>");
        $output->writeln('   Then open <info>http://localhost:8000</info>');
        $output->writeln('');
        $output->writeln('<info>7. Optional</info>:');
        $output->writeln('   • <comment>php artisan boost:install</comment>   — finish interactive Laravel Boost setup');
        $output->writeln('   • <comment>php artisan horizon</comment>         — run the queue worker dashboard');
        $output->writeln('   • <comment>php artisan telescope:install</comment> — set <comment>TELESCOPE_ENABLED=true</comment> first');
        $output->writeln('');
        $output->writeln('<comment>📚 Full guide:</comment> <info>docs/01-getting-started/</info> in the new project');
        $output->writeln('<comment>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</comment>');
        $output->writeln('');
    }

    private function dryRun(OutputInterface $output, string $owner, string $name, string $path, bool $skipPackages, bool $skipNpm): int
    {
        $output->writeln("\n<info>🔍 Dry run — no changes will be made</info>\n");
        $output->writeln("  Owner:   <comment>$owner</comment>");
        $output->writeln("  Project: <comment>$name</comment>");
        $output->writeln("  Path:    <comment>$path</comment>");
        $output->writeln("  DB name: <comment>{$this->getDatabaseName()}</comment>");
        $output->writeln('');

        $exists = file_exists($path) && file_exists($path.'/artisan');
        $output->writeln('  Steps:');
        if (! $exists) {
            $output->writeln('    1. Create new Laravel project (laravel new)');
        }
        $output->writeln('    '.($exists ? '1' : '2').'. Copy stubs to project');
        $output->writeln('    '.($exists ? '2' : '3').'. Update composer.json (scripts, autoload)');
        $output->writeln('    '.($exists ? '3' : '4').'. Replace placeholders (${PROJECT_NAME}, ${OWNER})');
        $output->writeln('    '.($exists ? '4' : '5').'. Setup .env file');
        if (! $skipPackages) {
            $output->writeln('    '.($exists ? '5' : '6').'. Install Composer packages (19 require, 5 require-dev)');
            $output->writeln('    '.($exists ? '6' : '7').'. Publish vendor configs & migrations');
            if (! $skipNpm) {
                $output->writeln('    '.($exists ? '7' : '8').'. Install NPM packages (lodash, axios, tippy.js)');
            }
        }
        $output->writeln('    Run build tasks (migrations, assets, key generation)');
        $output->writeln('');

        return Command::SUCCESS;
    }

    private function validateProject(OutputInterface $output): bool
    {
        if (! file_exists($filePath = $this->getProjectPath().'/artisan')) {
            $output->writeln("<error>Missing required file: $filePath. Not a valid Laravel project.</error>");

            return false;
        }

        return true;
    }

    private function createLaravelProject(OutputInterface $output, string $projectPath, string $projectName, bool $verbose): bool
    {
        // Check if laravel installer is available (cross-platform)
        $checkCommand = self::isWindows() ? 'where laravel' : 'command -v laravel 2>/dev/null';
        exec($checkCommand, $laravelOutput, $laravelReturnCode);
        if ($laravelReturnCode !== 0) {
            $output->writeln("<error>Missing 'laravel' installer. Install with: composer global require laravel/installer</error>");

            return false;
        }

        // Get parent directory and ensure it exists
        $parentDir = dirname($projectPath);
        if (! is_dir($parentDir)) {
            $output->writeln("<error>Parent directory does not exist: $parentDir</error>");

            return false;
        }

        $output->writeln("\n📦 Creating new Laravel project <info>$projectName</info>...\n");

        // Pass the normalized full path directly to `laravel new` instead of chaining
        // `cd && …`. Chaining leaks shell-dialect assumptions (cmd vs bash) and forced
        // earlier builds to mix forward and back slashes on Windows, which crashed the
        // installer's mkdir() call.
        $command = sprintf(
            'laravel new %s --git --livewire --pest --npm --livewire-class-components --no-interaction',
            escapeshellarg($projectPath)
        );

        step('Creating Laravel project with Livewire, Pest, and Git', function () use ($command, $verbose) {
            runCommand($command, $verbose);
        }, $output, $verbose);

        // Verify the project was created successfully
        return file_exists($projectPath.DIRECTORY_SEPARATOR.'artisan');
    }

    private static function isWindows(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }

    private function copyStubs(OutputInterface $output, bool $verbose)
    {
        step('Copy application stubs', function () use ($verbose, $output) {
            copyRecursively(__DIR__.'/../stubs/', $this->getProjectPath(), $verbose, $output);
        }, $output, $verbose);
    }

    private function setupComposer(OutputInterface $output, bool $verbose)
    {
        step('Update composer.json for helper, config plugins and scripts', function () use ($verbose) {
            $composerFile = $this->getProjectPath().'/composer.json';
            $composer = json_decode(file_get_contents($composerFile), true);

            $composer['autoload']['files'] = ['support/helpers.php'];
            $composer['config']['allow-plugins']['pestphp/pest-plugin'] = true;

            $composer['scripts'] = [
                'post-autoload-dump' => [
                    'Illuminate\\Foundation\\ComposerScripts::postAutoloadDump',
                    '@php artisan package:discover --ansi',
                ],
                'post-update-cmd' => [
                    '@php artisan vendor:publish --tag=laravel-assets --ansi --force',
                ],
                'post-root-package-install' => [
                    "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
                ],
                'post-create-project-cmd' => [
                    '@php artisan key:generate --ansi',
                    "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
                    '@php artisan migrate --graceful --ansi',
                ],
                'dev' => [
                    'Composer\\Config::disableProcessTimeout',
                    'npx concurrently -c "#93c5fd,#c4b5fd,#fb7185,#fdba74" "php artisan serve" "php artisan queue:listen --tries=1" "php artisan pail --timeout=0" "npm run dev" --names=server,queue,logs,vite --kill-others',
                ],
                'analyse' => '@php vendor/bin/phpstan analyse',
                'test' => '@php vendor/bin/pest',
                'test-arch' => '@php vendor/bin/pest tests/Feature/ArchitectureTest.php',
                'test-coverage' => 'vendor/bin/pest --coverage',
                'format' => '@php vendor/bin/pint',
                'lint' => '@php vendor/bin/phplint',
                'rector' => 'vendor/bin/rector process',
            ];

            putFile($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            runCommand('composer dump-autoload --no-scripts --working-dir='.escapeshellarg($this->getProjectPath()), $verbose);
        }, $output, $verbose);
    }

    private function setupProjectName(OutputInterface $output, bool $verbose)
    {
        step('Update project name in bin/ directory', function () {
            $binDir = $this->getProjectPath().'/bin';

            foreach (glob($binDir.'/*') as $file) {
                $this->updatePlaceholder(self::PLACEHOLDER_PROJECT_NAME, $file);
            }
        }, $output, $verbose);

        step('Update README', function () {
            $file = $this->getProjectPath().'/README.md';
            $this->updatePlaceholder(self::PLACEHOLDER_PROJECT_NAME, $file);
            $this->updatePlaceholder(self::PLACEHOLDER_OWNER, $file);
        }, $output, $verbose);

        // .env.example placeholder substitution is handled in setupEnvironmentFile() so
        // that DB_DATABASE / MINIO_BUCKET get snake-cased BEFORE the generic ${PROJECT_NAME}
        // replacement runs.
    }

    private function setupEnvironmentFile(OutputInterface $output, bool $verbose)
    {
        step('Update project environment file', function () {
            $envExampleFile = $this->getProjectPath().'/.env.example';

            // Snake-case DB_DATABASE and MINIO_BUCKET BEFORE the generic ${PROJECT_NAME}
            // substitution — MySQL won't accept kebab-case database names.
            $this->snakeCaseDbPlaceholders($envExampleFile);

            $this->updatePlaceholder(self::PLACEHOLDER_PROJECT_NAME, $envExampleFile);
            $this->updatePlaceholder(self::PLACEHOLDER_OWNER, $envExampleFile);

            copy($envExampleFile, $this->getProjectPath().'/.env');
        }, $output, $verbose);
    }

    private function snakeCaseDbPlaceholders(string $file): void
    {
        if (! is_file($file)) {
            return;
        }
        $snake = $this->getDatabaseName();
        $content = file_get_contents($file);
        $content = str_replace(
            ['DB_DATABASE=${PROJECT_NAME}', 'MINIO_BUCKET=${PROJECT_NAME}'],
            ["DB_DATABASE=$snake", "MINIO_BUCKET=$snake"],
            $content
        );
        file_put_contents($file, $content);
    }

    private function getDatabaseName(): string
    {
        $name = $this->getProjectName();
        $snake = strtolower(preg_replace('/[^\w]+/', '_', $name));
        $snake = preg_replace('/_+/', '_', $snake);

        return trim($snake, '_');
    }

    private function updatePlaceholder($placeholder, $file)
    {
        if (is_file($file)) {
            $content = file_get_contents($file);
            $newContent = str_replace(
                $placeholder,
                $placeholder === self::PLACEHOLDER_PROJECT_NAME ? $this->getProjectName() : $this->getProjectOwner(),
                $content
            );
            file_put_contents($file, $newContent);
        }
    }

    private function installPackages(OutputInterface $output, bool $verbose, bool $skipNpm = false)
    {
        step('Changing to project directory', function () {
            chdir($this->getProjectPath());
        }, $output, $verbose);

        step('Installing required packages', function () use ($verbose) {
            $require = [
                'laravel/sanctum',
                'blade-ui-kit/blade-icons',
                'cleaniquecoders/laravel-media-secure',
                'cleaniquecoders/traitify',
                'diglactic/laravel-breadcrumbs',
                'dragon-code/laravel-deploy-operations',
                'lab404/laravel-impersonate',
                'laravel/horizon',
                'laravel/telescope',
                // Livewire 4 with compatible packages
                'livewire/livewire:^4.0',
                'livewire/flux',  // Compatible with both Livewire 3 & 4
                'mallardduck/blade-lucide-icons',
                'owen-it/laravel-auditing',
                'predis/predis',
                'spatie/laravel-activitylog',
                'spatie/laravel-medialibrary',
                'cleaniquecoders/media-manager',
                'spatie/laravel-permission',
                'spatie/laravel-settings',
                'yadahan/laravel-authentication-log',
            ];
            $requireDev = [
                'barryvdh/laravel-debugbar',
                'cleaniquecoders/laravel-db-doc',
                'driftingly/rector-laravel',
                'laravel/boost',
                'larastan/larastan',
                'pestphp/pest-plugin-arch',
            ];
            installPackages($require, $requireDev, $this->getProjectPath(), $verbose);
        }, $output, $verbose);

        step('Publishing package configs & migrations', function () use ($verbose) {
            $options = [
                '--provider="OwenIt\\Auditing\\AuditingServiceProvider"',
                '--provider="Spatie\\Activitylog\\ActivitylogServiceProvider"',
                '--provider="Spatie\\LaravelSettings\\LaravelSettingsServiceProvider"',
                '--tag=authentication-log-config',
                '--tag=authentication-log-migrations',
                '--tag=blade-lucide-icons',
                '--tag=blade-lucide-icons-config',
                '--tag=impersonate',
                '--tag=laravel-errors',
                '--tag=livewire:assets',
                '--tag=media-secure-config',
                '--tag=medialibrary-config',
                '--tag=medialibrary-migrations',
                '--tag=permission-config',
                '--tag=permission-migrations',
                '--tag=sanctum-config',
                '--tag=telescope-migrations',
                '--tag=livewire:config',
                '--tag=media-manager-config',
                '--tag=media-manager-views',
            ];
            foreach ($options as $option) {
                runCommand("php artisan vendor:publish {$option}", $verbose);
            }
        }, $output, $verbose);

        step('Installing Laravel Boost', function () use ($verbose) {
            runCommand('php artisan boost:install --guidelines --skills --mcp --no-interaction', $verbose);
        }, $output, $verbose, false);

        if (! $skipNpm) {
            step('Install npm packages', function () use ($verbose) {
                runCommand('npm install lodash axios tippy.js', $verbose);
            }, $output, $verbose);
        } else {
            $output->writeln('⏭️  Skipping NPM package installation (--skip-npm)');
        }
    }

    private function setupDatabase(OutputInterface $output, bool $verbose)
    {
        step('Provisioning database', function () use ($output, $verbose) {
            $envFile = $this->getProjectPath().'/.env';
            if (! is_file($envFile)) {
                throw new \RuntimeException('.env not found — cannot provision database.');
            }

            $env = $this->parseEnvFile($envFile);
            $connection = $env['DB_CONNECTION'] ?? 'mysql';

            if ($connection === 'sqlite') {
                $this->ensureSqliteFile();
                if ($verbose) {
                    $output->writeln('<info>   .env already targets SQLite — file ensured.</info>');
                }

                return;
            }

            if ($connection !== 'mysql') {
                if ($verbose) {
                    $output->writeln("<comment>   Non-MySQL connection ($connection) — skipping auto-provision.</comment>");
                }

                return;
            }

            $dbName = $env['DB_DATABASE'] ?? '';

            if ($this->tryCreateMysqlDatabase($env, $dbName, $verbose, $output)) {
                if ($verbose) {
                    $output->writeln("<info>   MySQL database '$dbName' is ready.</info>");
                }

                return;
            }

            $this->switchEnvToSqlite($envFile);
            $output->writeln('');
            $output->writeln("   <comment>⚠️  MySQL not reachable — switched .env to SQLite (database/database.sqlite).</comment>");
            $output->writeln('   <comment>   To switch back: set DB_CONNECTION=mysql and configure DB_HOST/DB_USERNAME/DB_PASSWORD in .env.</comment>');
        }, $output, $verbose);
    }

    private function tryCreateMysqlDatabase(array $env, string $dbName, bool $verbose, OutputInterface $output): bool
    {
        if ($dbName === '') {
            return false;
        }

        exec('command -v mysql 2>/dev/null', $whichOut, $whichExit);
        if ($whichExit !== 0) {
            if ($verbose) {
                $output->writeln('<comment>   mysql CLI not found.</comment>');
            }

            return false;
        }

        $host = $env['DB_HOST'] ?? '127.0.0.1';
        $port = $env['DB_PORT'] ?? '3306';
        $user = $env['DB_USERNAME'] ?? 'root';
        $pass = $env['DB_PASSWORD'] ?? '';

        // Skip placeholder passwords — we know they won't work.
        if ($pass === 'CHANGE_ME_BEFORE_DEPLOY') {
            $pass = '';
        }

        $parts = [
            'mysql',
            '-h'.escapeshellarg($host),
            '-P'.escapeshellarg($port),
            '-u'.escapeshellarg($user),
        ];
        if ($pass !== '') {
            $parts[] = '-p'.escapeshellarg($pass);
        }
        $parts[] = '-e';
        $parts[] = escapeshellarg("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        $cmd = implode(' ', $parts).' 2>&1';
        exec($cmd, $out, $exit);

        if ($exit !== 0 && $verbose) {
            $output->writeln('<comment>   MySQL CREATE DATABASE failed: '.implode(' ', $out).'</comment>');
        }

        return $exit === 0;
    }

    private function switchEnvToSqlite(string $envFile): void
    {
        $this->ensureSqliteFile();
        $content = file_get_contents($envFile);
        $content = preg_replace('/^DB_CONNECTION=.*/m', 'DB_CONNECTION=sqlite', $content);
        file_put_contents($envFile, $content);
    }

    private function ensureSqliteFile(): void
    {
        $sqlitePath = $this->getProjectPath().'/database/database.sqlite';
        ensureDir(dirname($sqlitePath));
        if (! file_exists($sqlitePath)) {
            touch($sqlitePath);
        }
    }

    private function parseEnvFile(string $file): array
    {
        $env = [];
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $trimmed = ltrim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#') || ! str_contains($line, '=')) {
                continue;
            }
            [$k, $v] = explode('=', $line, 2);
            $env[trim($k)] = trim($v, " \"'");
        }

        return $env;
    }

    private function runTasks(OutputInterface $output, bool $verbose)
    {
        step('Building application', function () use ($verbose) {
            runCommand('bin/install', $verbose);
            runCommand('npm run build', $verbose);

            // Force array-backed cache/session for bootstrap artisan calls so they don't
            // depend on Redis being up with the matching REDIS_PASSWORD. Spatie Laravel
            // Data's structure cache resolves CACHE_STORE → CACHE_DRIVER=redis from the
            // stub .env and would otherwise try to authenticate against local Redis.
            $this->withSafeBootstrapEnv(function () use ($verbose) {
                runCommand('php artisan key:generate', $verbose);
                runCommand('php artisan make:notifications-table', $verbose);
                runCommand('php artisan operations:install', $verbose);
                runCommand('php artisan reload:db', $verbose);
            });
        }, $output, $verbose);
    }

    private function withSafeBootstrapEnv(callable $callback): void
    {
        $overrides = [
            'CACHE_STORE' => 'array',
            'CACHE_DRIVER' => 'array',
            'SESSION_DRIVER' => 'array',
        ];

        $previous = [];
        foreach ($overrides as $key => $value) {
            $previous[$key] = getenv($key);
            putenv("$key=$value");
        }

        try {
            $callback();
        } finally {
            foreach ($previous as $key => $value) {
                putenv($value === false ? $key : "$key=$value");
            }
        }
    }

    private function restoreGitIgnoreFiles(OutputInterface $output, bool $verbose)
    {
        step('Restore .gitignore files from stubs', function () use ($verbose, $output) {
            $stubsDir = __DIR__.'/../stubs';
            $projectPath = $this->getProjectPath();

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($stubsDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                if ($item->isFile() && $item->getFilename() === '.gitignore') {
                    $targetPath = $projectPath.DIRECTORY_SEPARATOR.$iterator->getSubPathName();
                    ensureDir(dirname($targetPath));
                    copy($item->getPathname(), $targetPath);
                    if ($verbose && $output) {
                        $output->writeln("<info>Restored:</info> $targetPath");
                    }
                }
            }
        }, $output, $verbose);
    }
}
