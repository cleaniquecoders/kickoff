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
     *
     * `--force` is forwarded unconditionally and deliberately. This is the
     * production install path (bin/install, bin/deploy), so it runs unattended
     * with no TTY — and without --force `db:seed` prints the "APPLICATION IN
     * PRODUCTION" prompt, cancels, and still exits 0. Run after migrate:fresh
     * that leaves a migrated but *unseeded* database: no roles, no superadmin,
     * no way to log in, and a caller that believes it succeeded.
     */
    public function handle(): int
    {
        return $this->call('db:seed', [
            '--class' => '\Database\Seeders\PrepareSeeder',
            '--force' => true,
            '--quiet' => true,
        ]);
    }
}
