<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearHorizonQueuesCommand extends Command
{
    protected $signature = 'horizon:clear-all-queues';

    protected $description = 'Clear all queues under horizon';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach (config('horizon.defaults') as $value) {
            $queues = $value['queue'];
            foreach ($queues as $queue) {
                $this->call('queue:clear', [
                    'connection' => $value['connection'],
                    '--queue' => $queue,
                ]);
            }
        }

        return 0;
    }
}
