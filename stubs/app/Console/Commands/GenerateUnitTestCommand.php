<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Output\BufferedOutput;

class GenerateUnitTestCommand extends Command
{
    protected $signature = 'test:generate';

    protected $description = 'Generate tests from routes';

    /**
     * Buffer output
     */
    private BufferedOutput $buffer;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->buffer = new BufferedOutput;
        $this->callBuffer('route:list', [
            '--json' => true,
            '--except-vendor' => true,
        ]);

        $routes = json_decode($this->buffer->fetch(), JSON_OBJECT_AS_ARRAY);

        if (count($routes) == 0) {
            $this->components->error("Your application doesn't have any routes.");

            return self::FAILURE;
        }

        $this->info(count($routes).' routes found');

        foreach ($routes as $route) {
            if (empty($route['name'])) {
                $this->warn('Empty route name. Skip generate unit test for ('.$route['method'].') '.$route['uri']);

                continue;
            }
            $this->line('Generating unit test for ('.$route['method'].') '.$route['uri']);
            $class = str($route['name'])->replace('.', ' ')->headline()->replace(' ', '').'Test';

            $this->call('pest:test', [
                'name' => $class,
                '--force' => true,
            ]);
        }

        return 0;
    }

    /**
     * Call another console command.
     *
     * @param  \Symfony\Component\Console\Command\Command|string  $command
     */
    public function callBuffer($command, array $arguments = []): int
    {
        return $this->runCommand($command, $arguments, $this->buffer);
    }
}
