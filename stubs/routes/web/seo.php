<?php

use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/*
 * Public SEO endpoints — robots.txt and sitemap.xml.
 *
 * robots.txt is admin-editable (Admin > Settings > SEO & Analytics); the
 * Sitemap: line is appended automatically. Kickoff removes the framework's
 * static public/robots.txt during setup so this route is reachable.
 */
Route::get('robots.txt', function () {
    $content = rtrim((string) config('seo.robots_txt'))
        ."\n\nSitemap: ".route('seo.sitemap')."\n";

    return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
})->name('seo.robots');

/*
 * On-the-fly sitemap of the public routes. Once the app has real content,
 * either extend this list or schedule `php artisan seo:generate-sitemap` to
 * crawl the site into public/sitemap.xml (a generated file takes precedence —
 * the web server serves it before Laravel; the file check keeps `artisan
 * serve` and tests consistent).
 */
Route::get('sitemap.xml', function () {
    if (file_exists(public_path('sitemap.xml'))) {
        return response()->file(public_path('sitemap.xml'), ['Content-Type' => 'application/xml']);
    }

    return Sitemap::create()
        ->add(Url::create(route('home'))
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(1.0))
        ->toResponse(request());
})->name('seo.sitemap');
