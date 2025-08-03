<?php

namespace CleaniqueCoders\Kickoff\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addArgument('path', InputArgument::OPTIONAL, 'The project path.');
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

        $this->copyStubs($output);

        $this->setupComposer($output);

        $this->setupProjectName($output);

        $this->setupEnvironmentFile($output);

        $this->installPackages($output);

        $this->runTasks($output);

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

    private function copyStubs(OutputInterface $output)
    {
        step('Copy application stubs', function () {
            copyRecursively(
                __DIR__.'/../stubs/',
                $this->getProjectPath()
            );
        }, $output);
    }

    private function setupComposer(OutputInterface $output)
    {
        step('Update composer.json for helper, config plugins and scripts', function () {
            $composerFile = $this->getProjectPath().'/composer.json';
            $composer = json_decode(file_get_contents($composerFile), true);

            $composer['autoload']['files'] = ['support/helpers.php'];

            $composer['config']['allow-plugins']['pestphp/pest-plugin'] = true;

            $composer['scripts'] = [
                'analyse' => '@php vendor/bin/phpstan analyse',
                'test' => '@php vendor/bin/pest',
                'test-arch' => '@php vendor/bin/pest tests/Feature/ArchitectureTest.php',
                'test-coverage' => 'vendor/bin/pest --coverage',
                'format' => '@php vendor/bin/pint',
                'lint' => '@php vendor/bin/phplint',
                'rector' => 'vendor/bin/rector process',
            ];

            putFile($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            runSilent('composer dump-autoload');
        }, $output);
    }

    private function setupProjectName(OutputInterface $output)
    {
        step('Update project name in bin/ directory', function () {
            $binDir = $this->getProjectPath().'/bin';

            foreach (glob($binDir.'/*') as $file) {
                $this->updatePlaceholder(self::PLACEHOLDER_PROJECT_NAME, $file);
            }
        }, $output);

        step('Update README', function () {
            $file = $this->getProjectPath().'/README.md';

            $this->updatePlaceholder(self::PLACEHOLDER_PROJECT_NAME, $file);
            $this->updatePlaceholder(self::PLACEHOLDER_OWNER, $file);
        }, $output);

        step('Update .env.example', function () {
            $file = $this->getProjectPath().'/.env.example';

            $this->updatePlaceholder(self::PLACEHOLDER_PROJECT_NAME, $file);
        }, $output);
    }

    private function setupEnvironmentFile(OutputInterface $output)
    {
        step('Update project environment file', function () {
            copy($this->getProjectPath().'/.env.example', $this->getProjectPath().'/.env');

            $envFile = $this->getProjectPath().'/.env';

            $content = file_get_contents($envFile);
            $content = str_replace(
                ['DB_DATABASE=kickoff'],
                ['DB_DATABASE='.$this->getDatabaseName()],
                $content
            );
            file_put_contents(
                $envFile,
                $content
            );
        }, $output);

    }

    private function getDatabaseName(): string
    {
        $name = $this->getProjectName();
        // Convert to snake_case and lower case, ensuring no repeated underscores
        $snake = strtolower(preg_replace('/[^\w]+/', '_', $name));
        // Replace multiple underscores with a single underscore
        $snake = preg_replace('/_+/', '_', $snake);
        // Trim leading/trailing underscores
        $snake = trim($snake, '_');

        return $snake;
    }

    private function updatePlaceholder($placeholder, $file)
    {
        if (is_file($file)) {
            $content = file_get_contents($file);
            $newContent = str_replace(
                $placeholder,
                $placeholder === self::PLACEHOLDER_PROJECT_NAME
                    ? $this->getProjectName() : $this->getProjectOwner(),
                $content);
            file_put_contents($file, $newContent);
        }
    }

    private function installPackages(OutputInterface $output)
    {
        step('Changing to project directory', function () {
            chdir($this->getProjectPath());
        }, $output);
        step('Installing required packages', function () {
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
            installPackages($require, $requireDev, $this->getProjectPath());
        }, $output);
        step('Publishing package configs & migrations', function () {
            $tags = [
                '--provider="OwenIt\Auditing\AuditingServiceProvider"',
                '--tag=permission-migrations', '--tag=permission-config',
                '--tag=medialibrary-migrations', '--tag=medialibrary-config',
                '--tag=media-secure-config', '--tag=laravel-errors',
                '--tag=authentication-log-migrations', '--tag=authentication-log-config',
                '--tag=impersonate', '--tag=telescope-migrations',
                '--tag=blade-lucide-icons', '--tag=blade-lucide-icons-config',
            ];
            foreach ($tags as $tag) {
                runSilent("php artisan vendor:publish {$tag}");
            }
        }, $output);

        step('Install tippy.js', function () {
            runSilent('npm install tippy.js');
        }, $output);
    }

    private function runTasks(OutputInterface $output)
    {
        step('Building application', function () {
            runSilent('bin/install');
            runSilent('npm run build');
            runSilent('php artisan key:generate');
            runSilent('php artisan reload:all');
        }, $output);
    }
}
