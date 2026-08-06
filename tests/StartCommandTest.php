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
