<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Telescope\Telescope;

class PurgeExpiredDataCommand extends Command
{
    protected $signature = 'data:purge
        {--audit-days=365 : Days to retain audit records}
        {--telescope-hours=48 : Hours to retain telescope entries}
        {--dry-run : Show what would be deleted without deleting}';

    protected $description = 'Purge expired audit records and telescope entries based on retention policy';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN - no records will be deleted');
        }

        $this->purgeAuditRecords((int) $this->option('audit-days'), $dryRun);
        $this->purgeTelescopeEntries((int) $this->option('telescope-hours'), $dryRun);
        $this->purgeSoftDeletedRecords($dryRun);

        return self::SUCCESS;
    }

    private function purgeAuditRecords(int $days, bool $dryRun): void
    {
        $cutoff = now()->subDays($days);
        $table = config('audit.drivers.database.table', 'audits');

        $count = DB::table($table)->where('created_at', '<', $cutoff)->count();
        $this->info("Audit records older than {$days} days: {$count}");

        if (! $dryRun && $count > 0) {
            DB::table($table)->where('created_at', '<', $cutoff)->delete();
            $this->info("Deleted {$count} audit records");
        }
    }

    private function purgeTelescopeEntries(int $hours, bool $dryRun): void
    {
        if (! class_exists(Telescope::class)) {
            return;
        }

        $cutoff = now()->subHours($hours);

        $count = DB::table('telescope_entries')->where('created_at', '<', $cutoff)->count();
        $this->info("Telescope entries older than {$hours} hours: {$count}");

        if (! $dryRun && $count > 0) {
            DB::table('telescope_entries_tags')->whereIn(
                'entry_uuid',
                DB::table('telescope_entries')->where('created_at', '<', $cutoff)->select('uuid')
            )->delete();
            DB::table('telescope_entries')->where('created_at', '<', $cutoff)->delete();
            $this->info("Deleted {$count} telescope entries");
        }
    }

    private function purgeSoftDeletedRecords(bool $dryRun): void
    {
        $cutoff = now()->subDays(90);

        $count = DB::table('users')->whereNotNull('deleted_at')->where('deleted_at', '<', $cutoff)->count();
        $this->info("Soft-deleted users older than 90 days: {$count}");

        if (! $dryRun && $count > 0) {
            DB::table('users')->whereNotNull('deleted_at')->where('deleted_at', '<', $cutoff)->delete();
            $this->info("Permanently deleted {$count} user records");
        }
    }
}
