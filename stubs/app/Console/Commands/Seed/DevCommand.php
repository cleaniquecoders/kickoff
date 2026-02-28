<?php

declare(strict_types=1);

namespace App\Console\Commands\Seed;

use Illuminate\Console\Command;

class DevCommand extends Command
{
    protected $signature = 'seed:dev';

    protected $description = 'Seed Development Data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('db:seed', [
            '--class' => '\Database\Seeders\DevSeeder',
            '--quiet' => true,
        ]);
    }
}
