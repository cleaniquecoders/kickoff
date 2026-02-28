<?php

declare(strict_types=1);

namespace App\Console\Commands\Reload;

use Illuminate\Console\Command;

class CacheCommand extends Command
{
    protected $signature = 'reload:cache';

    protected $description = 'Reload all caches';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('event:clear');
        $this->call('optimize:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('cache:clear');

        $this->components->info('Successfully reload caches.');
    }
}
