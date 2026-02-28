<?php

declare(strict_types=1);

namespace App\Console\Commands\Seed;

use Illuminate\Console\Command;

class DemoCommand extends Command
{
    protected $signature = 'seed:demo';

    protected $description = 'Seed Demo Data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('db:seed', [
            '--class' => '\Database\Seeders\DemoSeeder',
            '--quiet' => true,
        ]);
    }
}
