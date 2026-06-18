<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The audits table is published by owen-it/laravel-auditing without a uuid
     * column, but App\Models\Audit uses InteractsWithUuid and the security
     * audit-trail routes/views resolve audits by uuid (route('security.audit-trail.show', $audit->uuid)).
     * Without this column $audit->uuid is always null and URL generation throws
     * "Missing required parameter ... [Missing parameter: uuid]".
     */
    public function up(): void
    {
        if (Schema::hasTable('audits') && ! Schema::hasColumn('audits', 'uuid')) {
            Schema::table('audits', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('audits') && Schema::hasColumn('audits', 'uuid')) {
            Schema::table('audits', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};
