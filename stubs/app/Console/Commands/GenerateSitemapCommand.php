<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

/**
 * Crawl the app and write public/sitemap.xml.
 *
 * The /sitemap.xml route serves a small hand-built sitemap of the public
 * routes until this file exists; once generated, the static file wins. For
 * content-heavy sites, schedule this daily:
 *
 *     Schedule::command('seo:generate-sitemap')->daily();
 */
class GenerateSitemapCommand extends Command
{
    protected $signature = 'seo:generate-sitemap';

    protected $description = 'Crawl the application and write public/sitemap.xml';

    public function handle(): int
    {
        $url = (string) config('app.url');

        $this->info("Crawling {$url} ...");

        SitemapGenerator::create($url)
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap written to public/sitemap.xml');

        return self::SUCCESS;
    }
}
