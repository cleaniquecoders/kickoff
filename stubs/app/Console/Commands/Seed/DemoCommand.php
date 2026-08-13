<?php

declare(strict_types=1);

namespace App\Console\Commands\Seed;

use Illuminate\Console\Command;

class DemoCommand extends Command
{
    protected $signature = 'seed:demo {--force : Run without the production confirmation prompt}';

    protected $description = 'Seed Demo Data';

    /**
     * Execute the console command.
     *
     * Unlike seed:prepare this is NOT a production path, so --force is opt-in.
     * The exit code is returned rather than swallowed: without a TTY the
     * underlying prompt cancels and would otherwise report success.
     */
    public function handle(): int
    {
        return $this->call('db:seed', [
            '--class' => '\Database\Seeders\DemoSeeder',
            '--force' => (bool) $this->option('force'),
            '--quiet' => true,
        ]);
    }
}
