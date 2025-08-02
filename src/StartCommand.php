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
    protected string $projectName;

    protected string $projectPath;

    protected function configure(): void
    {
        $this
            ->setDescription('Kickoff a new Laravel project setup')
            ->addArgument('projectName', InputArgument::REQUIRED, 'The project name.')
            ->addArgument('projectPath', InputArgument::REQUIRED, 'The project path.');
    }

    public function getProjectName(): string
    {
        return $this->projectName;
    }

    public function getProjectPath(): string
    {
        return $this->projectPath;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->projectName = $projectName = $input->getArgument('projectName');
        $this->projectPath = $projectPath = $input->getArgument('projectPath');

        $output->writeln("Let's kickoff <info>$projectName</info> project!");
        $output->writeln("Configuring <comment>$projectPath</comment>...");

        if (! file_exists($projectPath)) {
            $output->writeln("<error>$projectPath does not exist!</error>");

            return Command::FAILURE;
        }

        $output->writeln("\n🎉 Let's kickoff your project $projectName now!\n");

        $this->setupConfig($output);
        $this->setupHelper($output);
        $this->setupRoute($output);
        $this->setupScripts($output);
        $this->setupDirectory($output);
        $this->setupDocumentation($output);
        $this->setupCiCd($output);
        $this->setupArchitectureTest($output);
        $this->setupStubs($output);
        $this->installPackages($output);
        $this->runTasks($output);

        $output->writeln("\n🎉 Project setup completed successfully!\n");

        return Command::SUCCESS;
    }

    private function setupConfig(OutputInterface $output)
    {
        step('Creating rector.php', function () {
            ensureFile($this->getProjectPath().'/rector.php', "<?php\n\n// Rector config\n");
        }, $output);
        step('Creating phpstan.neon.dist', function () {
            ensureFile($this->getProjectPath().'/phpstan.neon.dist', "parameters:\n  level: 6\n  paths:\n    - app/\n");
        }, $output);
        step('Creating pint.json', function () {
            ensureFile($this->getProjectPath().'/pint.json', "{\n    \"preset\": \"laravel\"\n}\n");
        }, $output);

        $path = $this->getProjectPath().'/.config';
        ensureDir($path);

        $configDir = __DIR__.'/../stubs/config';
        if (is_dir($configDir)) {
            foreach (glob($configDir.'/*.stub') as $file) {
                $name = basename($file, '.stub');
                $content = file_get_contents($file);
                step("Setting up .config/$name script", function () use ($configDir, $name, $content) {
                    $file_path = $configDir.DIRECTORY_SEPARATOR.$name;
                    ensureFile($file_path, $content);
                }, $output);
            }
        }

        step('Setup VS Code IDE Extension Recommendation', function () {
            $path = $this->getProjectPath().'/.vscode';
            ensureDir($path);
            $file_path = $path.DIRECTORY_SEPARATOR.'extensions.json';
            $content = '{"recommendations":["bmewburn.vscode-intelephense-client","amiralizadeh9480.laravel-extra-intellisense","junstyle.php-cs-fixer","codingyu.laravel-goto-view","onecentlin.laravel-blade","ryannaddy.laravel-artisan","shufo.vscode-blade-formatter","mikestead.dotenv","esbenp.prettier-vscode","bradlc.vscode-tailwindcss","eamodio.gitlens","mhutchie.git-graph","ms-azuretools.vscode-docker","adpyke.vscode-sql-formatter","clarkyu.vscode-sql-beautify","deerawan.vscode-faker","ritwickdey.liveserver","dansysanalyst.pest-snippets","shashraf.vscode-pestphp"]}';
            ensureFile($file_path, $content);
        }, $output);
    }

    private function setupHelper(OutputInterface $output)
    {
        step('Setting up support/helpers.php', function () {
            $helpersDir = $this->getProjectPath().'/support';
            $helpersFile = $helpersDir.'/helpers.php';
            $content = file_get_contents(__DIR__.'/../stubs/helper.stub');
            setupDirAndFile($helpersDir, $helpersFile, $content);
        }, $output);

        step('Updating composer.json', function () {
            $composerFile = $this->getProjectPath().'/composer.json';
            $composer = json_decode(file_get_contents($composerFile), true);

            $composer['autoload']['files'] = ['support/helpers.php'];

            putFile($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            runSilent('composer dump-autoload');
        }, $output);
    }

    private function setupRoute(OutputInterface $output)
    {
        step('Organising routes', function () {
            $routesDir = $this->getProjectPath().'/routes';
            foreach (['web', 'api', 'console'] as $dir) {
                $subDir = "$routesDir/$dir";
                ensureDir($subDir);
                $mainFile = "$routesDir/$dir.php";
                if (file_exists($mainFile)) {
                    copy($mainFile, "$subDir/_.php");
                }
                putFile($mainFile, "<?php\n\nrequire_all_in(base_path('routes/$dir/*.php'));\n");
            }

            $authFile = "$routesDir/auth.php";
            if (file_exists($authFile)) {
                copy($authFile, "$routesDir/web/auth.php");
                unlink($authFile);
            }
        }, $output);
    }

    private function setupScripts(OutputInterface $output)
    {
        $stubDir = __DIR__.'/../stubs/bin';
        if (is_dir($stubDir)) {
            $binDir = $this->getProjectPath().'/bin';
            ensureDir($binDir);

            foreach (glob($stubDir.'/*.stub') as $file) {
                $name = basename($file, '.stub');
                $content = file_get_contents($file);
                step("Setting up bin/$name script", function () use ($binDir, $name, $content) {
                    createBinScript(
                        $binDir,
                        $name,
                        str_replace(
                            '${PROJECT_NAME}',
                            $this->getProjectName(),
                            $content
                        )
                    );
                }, $output);
            }
        }
    }

    private function setupDirectory(OutputInterface $output)
    {

        step('Creating tinker/ with .gitignore', function () {
            $tinkerDir = $this->getProjectPath().'/tinker';
            ensureDir($tinkerDir);
            ensureFile("$tinkerDir/.gitignore", "*\n!.gitignore\n");
        }, $output);
    }

    private function setupDocumentation(OutputInterface $output)
    {
        step('Creating todo.md', function () {
            ensureFile($this->getProjectPath().'/todo.md', "# TODO\n");
            file_put_contents($this->getProjectPath().'/.gitignore', "\ntodo.md\n", FILE_APPEND);
        }, $output);

        step('Creating docs/README.md with TOC', function () {
            $docsDir = $this->getProjectPath().'/docs';
            ensureDir($docsDir);
            ensureFile("$docsDir/README.md", "# Project Documentation\n\n- Getting Started\n- TOC goes here\n");
        }, $output);

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
                ensureFile($this->getProjectPath()."/$file", $content);
            }, $output);
        }
    }

    private function setupArchitectureTest(OutputInterface $output)
    {
        step('Creating ArchitectureTest.php', function () {
            $testDir = $this->getProjectPath().'/tests/Feature';
            $archTestFile = "$testDir/ArchitectureTest.php";
            $content = file_get_contents(__DIR__.'/../stubs/test.stub');

            setupDirAndFile($testDir, $archTestFile, $content);
        }, $output);
    }

    private function installPackages(OutputInterface $output)
    {
        step('Updating composer.json allow-plugins', function () {
            $composerFile = $this->getProjectPath().'/composer.json';
            $composer = json_decode(file_get_contents($composerFile), true);

            $composer['config']['allow-plugins']['pestphp/pest-plugin'] = true;

            putFile($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }, $output);

        step('Changing to project directory...', function () {
            chdir($this->getProjectPath());
        }, $output);

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
            installPackages($require, $requireDev, $this->getProjectPath());
        }, $output);
        step('Publishing package configs & migrations', function () {
            $tags = [
                '--tag=permission-migrations', '--tag=permission-config',
                '--tag=medialibrary-migrations', '--tag=medialibrary-config',
                '--tag=media-secure-config', '--tag=laravel-errors',
            ];
            foreach ($tags as $tag) {
                runSilent("php artisan vendor:publish {$tag}");
            }
        }, $output);
        step('Updating composer.json', function () {
            $composerFile = $this->getProjectPath().'/composer.json';
            $composer = json_decode(file_get_contents($composerFile), true);

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

    private function setupCiCd(OutputInterface $output)
    {
        step('Creating GitHub Actions workflows', function () {
            $workflowDir = $this->getProjectPath().'/.github/workflows';
            ensureDir($workflowDir);
            ensureFile("$workflowDir/pint.yml", "name: PHP Linting (Pint)\n");
            ensureFile("$workflowDir/phpstan.yml", "name: PHPStan\n");
            ensureFile("$workflowDir/rector.yml", "name: Rector CI\n");
            ensureFile("$workflowDir/tests.yml", "name: Test\n");
            ensureFile("$workflowDir/changelog.yml", "name: Update Changelog\n");
        }, $output);
    }

    private function setupStubs(OutputInterface $output)
    {
        step('Setup stubs', function () {
            ensureDir($this->getProjectPath().'/stubs');

            $stubDir = __DIR__.'/../stubs/';
            $stubs = [
                [
                    'path' => $this->getProjectPath().'/stubs/enum.stub',
                    'content' => file_get_contents($stubDir.'enum.stub'),
                ],
                [
                    'path' => $this->getProjectPath().'/app/Models/Base.php',
                    'content' => file_get_contents($stubDir.'base-model.stub'),
                ],
                [
                    'path' => $this->getProjectPath().'/stubs/migration.create.stub',
                    'content' => file_get_contents($stubDir.'migration.create.stub'),
                ],
            ];

            foreach ($stubs as $stub) {
                ensureFile($stub['path'], $stub['content']);
            }
        }, $output);
    }

    private function runTasks(OutputInterface $output)
    {
        step('Running artisan tasks', function () {
            runSilent('php artisan config:clear');
            runSilent('php artisan migrate');
            runSilent('php artisan storage:link');
        }, $output);
    }
}
