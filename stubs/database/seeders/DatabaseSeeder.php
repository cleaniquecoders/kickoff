<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * `migrate:fresh --seed` must leave a database you can log in to: roles, permissions and
     * the superadmin from config/seeder.php. Sample data stays opt-in (DevSeeder / DemoSeeder,
     * via seed:dev / seed:demo) so nothing here has to be deleted from production.
     */
    public function run(): void
    {
        $this->call(PrepareSeeder::class);
    }
}
