<?php

use CleaniqueCoders\Kickoff\Console\StartCommand;

test('it can kickoff project', function () {
    $command = $this->getMockBuilder(StartCommand::class)
        ->onlyMethods(['execute', 'getProjectName', 'getProjectPath', 'getProjectOwner'])
        ->getMock();

    $command->expects($this->once())
        ->method('getProjectOwner')
        ->willReturn('nasrulhazim');

    $command->expects($this->once())
        ->method('getProjectName')
        ->willReturn('demo-project');

    $command->expects($this->once())
        ->method('getProjectPath')
        ->willReturn('/tmp/demo-project');

    expect($command->getProjectOwner())->toBe('nasrulhazim')
        ->and($command->getProjectName())->toBe('demo-project')
        ->and($command->getProjectPath())->toBe('/tmp/demo-project');
});

test('configure sets arguments', function () {
    $command = new StartCommand;
    $definition = $command->getDefinition();

    expect($definition->hasArgument('owner'))->toBeTrue()
        ->and($definition->hasArgument('name'))->toBeTrue()
        ->and($definition->hasArgument('path'))->toBeTrue()
        ->and($definition->getArgument('owner')->isRequired())->toBeTrue()
        ->and($definition->getArgument('name')->isRequired())->toBeTrue()
        ->and($definition->getArgument('path')->isRequired())->toBeFalse()
        ->and($definition->hasOption('dry-run'))->toBeTrue()
        ->and($definition->hasOption('skip-packages'))->toBeTrue()
        ->and($definition->hasOption('skip-npm'))->toBeTrue();
});

test('get project name and path', function () {
    $command = new StartCommand;
    $reflection = new ReflectionClass($command);

    $reflection->getProperty('projectOwner')->setValue($command, 'nasrulhazim');
    $reflection->getProperty('projectName')->setValue($command, 'my-app');
    $reflection->getProperty('projectPath')->setValue($command, '/tmp/my-app');

    expect($command->getProjectOwner())->toBe('nasrulhazim')
        ->and($command->getProjectName())->toBe('my-app')
        ->and($command->getProjectPath())->toBe('/tmp/my-app');
});

test('command name is start', function () {
    $command = new StartCommand;
    expect($command->getName())->toBe('start');
});

test('command has description', function () {
    $command = new StartCommand;
    expect($command->getDescription())->not->toBeEmpty();
});

test('get database name converts to snake case', function () {
    $command = new StartCommand;
    $reflection = new ReflectionClass($command);

    $method = $reflection->getMethod('getDatabaseName');
    $nameProp = $reflection->getProperty('projectName');

    // Hyphenated name
    $nameProp->setValue($command, 'my-cool-project');
    expect($method->invoke($command))->toBe('my_cool_project');

    // Already snake_case
    $nameProp->setValue($command, 'simple_app');
    expect($method->invoke($command))->toBe('simple_app');

    // PascalCase
    $nameProp->setValue($command, 'MyApp');
    expect($method->invoke($command))->toBe('myapp');

    // Multiple special chars
    $nameProp->setValue($command, 'my--app..name');
    expect($method->invoke($command))->toBe('my_app_name');
});

test('placeholder constants', function () {
    expect(StartCommand::PLACEHOLDER_PROJECT_NAME)->toBe('${PROJECT_NAME}')
        ->and(StartCommand::PLACEHOLDER_OWNER)->toBe('${OWNER}');
});

test('normalize path collapses mixed separators to native', function () {
    $sep = DIRECTORY_SEPARATOR;

    expect(normalizePath('C:\\Users\\USER/myapp'))->toBe("C:{$sep}Users{$sep}USER{$sep}myapp")
        ->and(normalizePath('/tmp/demo'))->toBe("{$sep}tmp{$sep}demo")
        ->and(normalizePath('a\\b/c'))->toBe("a{$sep}b{$sep}c");
});

test('is windows matches php os family', function () {
    $method = new ReflectionClass(StartCommand::class)->getMethod('isWindows');

    expect($method->invoke(null))->toBe(PHP_OS_FAMILY === 'Windows');
});

test('configure exposes frontend option with required value and livewire default', function () {
    $command = new StartCommand;
    $definition = $command->getDefinition();

    expect($definition->hasOption('frontend'))->toBeTrue();

    $option = $definition->getOption('frontend');

    expect($option->isValueRequired())->toBeTrue()
        ->and($option->getDefault())->toBe('livewire');
});

test('configure exposes vendor-kit option with required value and no default', function () {
    $command = new StartCommand;
    $definition = $command->getDefinition();

    expect($definition->hasOption('vendor-kit'))->toBeTrue();

    $option = $definition->getOption('vendor-kit');

    expect($option->isValueRequired())->toBeTrue()
        ->and($option->getDefault())->toBeNull();
});

test('stub excludes returns empty list for livewire', function () {
    $command = new StartCommand;
    $method = (new ReflectionClass($command))->getMethod('stubExcludes');
    $method->setAccessible(true);

    expect($method->invoke($command, 'livewire'))->toBe([]);
});

test('stub excludes returns the eleven screen and route entries for non-livewire stacks', function (string $frontend) {
    $command = new StartCommand;
    $method = (new ReflectionClass($command))->getMethod('stubExcludes');
    $method->setAccessible(true);

    $excludes = $method->invoke($command, $frontend);

    expect($excludes)->toBe([
        'app/Livewire',
        'app/View/Components',
        'resources/views/livewire',
        'resources/views/flux',
        'resources/views/components',
        'resources/views/layouts',
        'routes/web/administration.php',
        'routes/web/security.php',
        'routes/web/media.php',
        'routes/web/notifications.php',
    ])
        ->and($excludes)->toHaveCount(10)
        ->and($excludes)->toContain('routes/web/administration.php')
        ->and($excludes)->not->toContain('routes/web/_.php');
})->with(['react', 'vue', 'svelte', 'vendor-kit']);

test('packages for returns inputs unchanged for livewire', function () {
    $command = new StartCommand;
    $method = (new ReflectionClass($command))->getMethod('packagesFor');
    $method->setAccessible(true);

    $require = [
        'livewire/livewire:^4.0',
        'livewire/flux',
        'mallardduck/blade-lucide-icons',
        'laravel/horizon',
    ];
    $requireDev = ['laravel/pint'];

    $result = $method->invoke($command, 'livewire', $require, $requireDev);

    expect($result)->toBe([$require, $requireDev])
        ->and($result[0])->toContain('livewire/livewire:^4.0')
        ->and($result[0])->toContain('livewire/flux')
        ->and($result[0])->toContain('mallardduck/blade-lucide-icons')
        ->and($result[0])->toContain('laravel/horizon');
});

test('packages for strips livewire packages including version constraints for react', function () {
    $command = new StartCommand;
    $method = (new ReflectionClass($command))->getMethod('packagesFor');
    $method->setAccessible(true);

    $require = [
        'livewire/livewire:^4.0',
        'livewire/flux',
        'mallardduck/blade-lucide-icons',
        'laravel/horizon',
    ];
    $requireDev = ['laravel/pint'];

    [$filtered, $filteredDev] = $method->invoke($command, 'react', $require, $requireDev);

    expect($filtered)->toBe(['laravel/horizon'])
        ->and($filtered)->not->toContain('livewire/livewire:^4.0')
        ->and($filtered)->not->toContain('livewire/flux')
        ->and($filtered)->not->toContain('mallardduck/blade-lucide-icons')
        ->and($filteredDev)->toBe($requireDev);
});

test('detect frontend stack reads composer json', function (array $composer, string $expected) {
    $projectPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'kickoff-test-'.uniqid();
    mkdir($projectPath, 0777, true);
    file_put_contents($projectPath.DIRECTORY_SEPARATOR.'composer.json', json_encode($composer));

    $command = new StartCommand;
    $method = (new ReflectionClass($command))->getMethod('detectFrontendStack');
    $method->setAccessible(true);

    expect($method->invoke($command, $projectPath))->toBe($expected);

    unlink($projectPath.DIRECTORY_SEPARATOR.'composer.json');
    rmdir($projectPath);
})->with([
    'livewire installed' => [['require' => ['livewire/livewire' => '^4.0']], 'livewire'],
    'inertia installed' => [['require' => ['inertiajs/inertia-laravel' => '^2.0']], 'inertia'],
    'neither installed' => [['require' => ['laravel/framework' => '^12.0']], 'unknown'],
]);
