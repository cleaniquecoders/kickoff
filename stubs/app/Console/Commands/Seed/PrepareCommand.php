<?php

declare(strict_types=1);

namespace App\Console\Commands\Seed;

use Illuminate\Console\Command;

class PrepareCommand extends Command
{
    protected $signature = 'seed:prepare';

    protected $description = 'Preparing Application to Run';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('db:seed', [
            '--class' => '\Database\Seeders\PrepareSeeder',
            '--quiet' => true,
        ]);
    }
}
