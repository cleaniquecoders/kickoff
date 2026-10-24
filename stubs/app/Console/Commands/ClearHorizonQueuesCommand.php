<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class ClearHorizonQueuesCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'horizon:clear-all-queues {--force : Skip the confirmation prompt}';

    protected $description = 'Clear all queues under horizon';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('Every pending job is deleted, not failed. Rows waiting on one of them (a "pending"');
        $this->warn('status only a job moves forward) are stranded unless a scheduled reconciler re-dispatches them.');

        if (! $this->confirmToProceed('This deletes all pending jobs on every Horizon queue.', fn () => true)) {
            return self::FAILURE;
        }

        foreach (config('horizon.defaults') as $value) {
            $queues = $value['queue'];
            foreach ($queues as $queue) {
                $this->call('queue:clear', [
                    'connection' => $value['connection'],
                    '--queue' => $queue,
                    '--force' => true,
                ]);
            }
        }

        return 0;
    }
}
