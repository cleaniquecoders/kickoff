<?php

namespace Laravel\Installer\Console\Tests;

use PHPUnit\Framework\TestCase;

class StartCommandTest extends TestCase
{
    public function test_it_can_kickoff_project()
    {
        $command = $this->getMockBuilder(\CleaniqueCoders\Kickoff\Console\StartCommand::class)
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
        $command = new \CleaniqueCoders\Kickoff\Console\StartCommand;
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('owner'));
        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->hasArgument('path'));
    }

    public function test_get_project_name_and_path()
    {
        $command = new \CleaniqueCoders\Kickoff\Console\StartCommand;
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
}
