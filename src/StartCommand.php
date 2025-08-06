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
            ->addOption('verbose', 'v', InputOption::VALUE_NONE, 'Run with verbose output.');
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
        $verbose = $input->getOption('verbose');

        $this->projectOwner = $projectOwner = $input->getArgument('owner');
        $this->projectName = $projectName = $input->getArgument('name');
        $this->projectPath = $projectPath = $input->getArgument('path');

        if (empty($projectPath)) {
            $this->projectPath = $projectPath = getcwd();
        }

        if (! file_exists($projectPath)) {
            $output->writeln("<error>$projectPath does not exist!</error>");

            return Command::FAILURE;
        }

        if (! file_exists($projectPath.'/composer.json')) {
            $output->writeln("<error>$projectPath/composer.json does not exist! Invalid Laravel project.</error>");

            return Command::FAILURE;
        }

        $this->validateProject($output);

        $output->writeln("\n🎉 Let's kickoff your <info>$projectOwner/$projectName</info> now!\n");

        $this->copyStubs($output, $verbose);

        $this->setupComposer($output, $verbose);

        $this->setupProjectName($output, $verbose);

        $this->setupEnvironmentFile($output, $verbose);

        $this->installPackages($output, $verbose);

        $this->runTasks($output, $verbose);

        $output->writeln("\n🎉 Project setup completed successfully!\n");

        return Command::SUCCESS;
    }

    private function validateProject(OutputInterface $output)
    {
        if (! file_exists($filePath = $this->getProjectPath().'/artisan')) {
            $output->writeln("<error>Missing required file: $filePath. Not a valid Laravel project.</error>");
            exit(Command::FAILURE);
        }
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
            runCommand('composer dump-autoload', $verbose);
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

        step('Update .env.example', function () {
            $file = $this->getProjectPath().'/.env.example';
            $this->updatePlaceholder(self::PLACEHOLDER_PROJECT_NAME, $file);
        }, $output, $verbose);
    }

    private function setupEnvironmentFile(OutputInterface $output, bool $verbose)
    {
        step('Update project environment file', function () {
            copy($this->getProjectPath().'/.env.example', $this->getProjectPath().'/.env');
            $envFile = $this->getProjectPath().'/.env';
            $content = file_get_contents($envFile);
            $content = str_replace(['DB_DATABASE=kickoff'], ['DB_DATABASE='.$this->getDatabaseName()], $content);
            file_put_contents($envFile, $content);
        }, $output, $verbose);
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

    private function installPackages(OutputInterface $output, bool $verbose)
    {
        step('Changing to project directory', function () {
            chdir($this->getProjectPath());
        }, $output, $verbose);

        step('Installing required packages', function () use ($verbose) {
            $require = [
                'spatie/laravel-permission',
                'spatie/laravel-medialibrary',
                'cleaniquecoders/traitify',
                'cleaniquecoders/laravel-media-secure',
                'owen-it/laravel-auditing',
                'yadahan/laravel-authentication-log',
                'lab404/laravel-impersonate',
                'laravel/telescope',
                'laravel/horizon',
                'predis/predis',
                'blade-ui-kit/blade-icons',
                'mallardduck/blade-lucide-icons',
            ];
            $requireDev = [
                'barryvdh/laravel-debugbar',
                'larastan/larastan',
                'driftingly/rector-laravel',
                'pestphp/pest-plugin-arch',
            ];
            installPackages($require, $requireDev, $this->getProjectPath(), $verbose);
        }, $output, $verbose);

        step('Publishing package configs & migrations', function () use ($verbose) {
            $tags = [
                '--provider="OwenIt\\Auditing\\AuditingServiceProvider"',
                '--tag=permission-migrations', '--tag=permission-config',
                '--tag=medialibrary-migrations', '--tag=medialibrary-config',
                '--tag=media-secure-config', '--tag=laravel-errors',
                '--tag=authentication-log-migrations', '--tag=authentication-log-config',
                '--tag=impersonate', '--tag=telescope-migrations',
                '--tag=blade-lucide-icons', '--tag=blade-lucide-icons-config',
            ];
            foreach ($tags as $tag) {
                runCommand("php artisan vendor:publish {$tag}", $verbose);
            }
        }, $output, $verbose);

        step('Install tippy.js', function () use ($verbose) {
            runCommand('npm install tippy.js', $verbose);
        }, $output, $verbose);
    }

    private function runTasks(OutputInterface $output, bool $verbose)
    {
        step('Building application', function () use ($verbose) {
            runCommand('bin/install', $verbose);
            runCommand('npm run build', $verbose);
            runCommand('php artisan key:generate', $verbose);
            runCommand('php artisan reload:db', $verbose);
        }, $output, $verbose);
    }
}
