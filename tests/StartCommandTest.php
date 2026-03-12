<?php

namespace CleaniqueCoders\Kickoff\Tests;

use CleaniqueCoders\Kickoff\Console\StartCommand;
use PHPUnit\Framework\TestCase;

class StartCommandTest extends TestCase
{
    public function test_it_can_kickoff_project()
    {
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

        $this->assertEquals('nasrulhazim', $command->getProjectOwner());
        $this->assertEquals('demo-project', $command->getProjectName());
        $this->assertEquals('/tmp/demo-project', $command->getProjectPath());
    }

    public function test_configure_sets_arguments()
    {
        $command = new StartCommand;
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('owner'));
        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->hasArgument('path'));

        $this->assertTrue($definition->getArgument('owner')->isRequired());
        $this->assertTrue($definition->getArgument('name')->isRequired());
        $this->assertFalse($definition->getArgument('path')->isRequired());
    }

    public function test_get_project_name_and_path()
    {
        $command = new StartCommand;
        $reflection = new \ReflectionClass($command);

        $ownerProp = $reflection->getProperty('projectOwner');
        $ownerProp->setAccessible(true);
        $ownerProp->setValue($command, 'nasrulhazim');

        $nameProp = $reflection->getProperty('projectName');
        $nameProp->setAccessible(true);
        $nameProp->setValue($command, 'my-app');

        $pathProp = $reflection->getProperty('projectPath');
        $pathProp->setAccessible(true);
        $pathProp->setValue($command, '/tmp/my-app');

        $this->assertEquals('nasrulhazim', $command->getProjectOwner());
        $this->assertEquals('my-app', $command->getProjectName());
        $this->assertEquals('/tmp/my-app', $command->getProjectPath());
    }

    public function test_command_name_is_start()
    {
        $command = new StartCommand;
        $this->assertEquals('start', $command->getName());
    }

    public function test_command_has_description()
    {
        $command = new StartCommand;
        $this->assertNotEmpty($command->getDescription());
    }

    public function test_get_database_name_converts_to_snake_case()
    {
        $command = new StartCommand;
        $reflection = new \ReflectionClass($command);

        $method = $reflection->getMethod('getDatabaseName');
        $method->setAccessible(true);

        $nameProp = $reflection->getProperty('projectName');
        $nameProp->setAccessible(true);

        // Hyphenated name
        $nameProp->setValue($command, 'my-cool-project');
        $this->assertEquals('my_cool_project', $method->invoke($command));

        // Already snake_case
        $nameProp->setValue($command, 'simple_app');
        $this->assertEquals('simple_app', $method->invoke($command));

        // PascalCase
        $nameProp->setValue($command, 'MyApp');
        $this->assertEquals('myapp', $method->invoke($command));

        // Multiple special chars
        $nameProp->setValue($command, 'my--app..name');
        $this->assertEquals('my_app_name', $method->invoke($command));
    }

    public function test_placeholder_constants()
    {
        $this->assertEquals('${PROJECT_NAME}', StartCommand::PLACEHOLDER_PROJECT_NAME);
        $this->assertEquals('${OWNER}', StartCommand::PLACEHOLDER_OWNER);
    }
}
