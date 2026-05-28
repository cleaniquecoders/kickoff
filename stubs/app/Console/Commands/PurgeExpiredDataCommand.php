<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Telescope\Telescope;

class PurgeExpiredDataCommand extends Command
{
    protected $signature = 'data:purge
        {--audit-days= : Days to retain CRUD audit records (default: config audit.retention.crud_audits_days)}
        {--telescope-hours=48 : Hours to retain telescope entries}
        {--dry-run : Show what would be deleted without deleting}';

    protected $description = 'Purge expired audit records and telescope entries based on retention policy';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN - no records will be deleted');
        }

        // The --audit-days option overrides the configured retention; otherwise
        // fall back to config (7 years), NOT a hard-coded 365 that would shred
        // a year of forensic history every night the scheduler runs.
        $auditDays = (int) ($this->option('audit-days') ?? config('audit.retention.crud_audits_days', 2555));

        $this->purgeAuditRecords($auditDays, $dryRun);
        $this->purgeTelescopeEntries((int) $this->option('telescope-hours'), $dryRun);
        $this->purgeSoftDeletedRecords($dryRun);

        return self::SUCCESS;
    }

    private function purgeAuditRecords(int $days, bool $dryRun): void
    {
        $cutoff = now()->subDays($days);
        $table = config('audit.drivers.database.table', 'audits');

        // Hard guard: this command purges owen-it CRUD diffs only. Any
        // append-only domain audit table (e.g. `audit_logs`) is the immutable
        // governance trail and must never be deleted here.
        if ($table === 'audit_logs') {
            $this->error('Refusing to purge the append-only audit_logs table.');

            return;
        }

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
        $days = (int) config('audit.retention.soft_deleted_users_days', 90);
        $cutoff = now()->subDays($days);

        $count = DB::table('users')->whereNotNull('deleted_at')->where('deleted_at', '<', $cutoff)->count();
        $this->info("Soft-deleted users older than {$days} days: {$count}");

        if (! $dryRun && $count > 0) {
            DB::table('users')->whereNotNull('deleted_at')->where('deleted_at', '<', $cutoff)->delete();
            $this->info("Permanently deleted {$count} user records");
        }
    }
}
