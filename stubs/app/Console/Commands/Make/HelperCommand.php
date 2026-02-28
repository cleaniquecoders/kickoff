<?php

declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;

class HelperCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    protected $name = 'make:helper';

    protected $description = 'Create a new helper';

    protected $type = 'Helper';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/helper.stub');
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = \Illuminate\Support\Str::replaceFirst($this->rootNamespace(), '', $name);

        return base_path('/support/'.str_replace('\\', '/', $name).'.php');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__.$stub;
    }
}
