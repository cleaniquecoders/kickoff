<?php

declare(strict_types=1);

namespace App\Console\Commands\Reload;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearMediaCommand extends Command
{
    protected $signature = 'reload:media';

    protected $description = 'Clear all media files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (app()->isProduction()) {
            return self::SUCCESS;
        }

        foreach ([
            storage_path('media'),
            storage_path('media-library'),
        ] as $directory) {
            $this->delete($directory);
            $this->components->info('Cleared media in '.$directory);
        }

        $this->components->info('Media storage successfully cleared.');

        return self::SUCCESS;
    }

    private function delete(string $directory): void
    {
        if (File::exists($directory)) {
            // Get all files and directories inside, except .gitignore
            $files = File::allFiles($directory);
            $directories = File::directories($directory);

            // Delete all files except .gitignore
            foreach ($files as $file) {
                if ($file->getFilename() !== '.gitignore') {
                    File::delete($file->getPathname());
                }
            }

            // Delete all directories and subdirectories
            foreach ($directories as $dir) {
                File::deleteDirectory($dir);
            }
        }
    }
}
