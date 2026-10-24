<?php

use Illuminate\Support\Facades\File;

beforeEach(fn () => config(['security.headers.csp' => true]));

function cspHeader(): string
{
    return (string) test()->get(route('login'))->headers->get('Content-Security-Policy');
}

it('allows the fonts the layout loads', function () {
    expect(cspHeader())
        ->toMatch("/style-src [^;]*https:\/\/fonts\.bunny\.net/")
        ->toMatch("/font-src [^;]*https:\/\/fonts\.bunny\.net/");
});

it('allows Google Tag Manager only once an ID is configured', function () {
    expect(cspHeader())->not->toContain('googletagmanager.com');

    config(['seo.google.tag_manager_id' => 'GTM-TEST']);

    expect(cspHeader())
        ->toMatch("/script-src [^;]*https:\/\/www\.googletagmanager\.com/")
        ->toMatch("/frame-src [^;]*https:\/\/www\.googletagmanager\.com/");
});

it('loads no third-party asset the policy does not know about', function () {
    // Every remote <script>/<link>/<img> a view emits must be allowed in SecurityHeaders.
    $known = ['fonts.bunny.net', 'www.googletagmanager.com'];

    $offenders = collect(File::allFiles(resource_path('views')))
        ->flatMap(function ($file) use ($known) {
            $content = preg_replace('/\{\{--.*?--\}\}/s', '', $file->getContents());
            preg_match_all('/<(?:script|link|img|iframe)\b[^>]*\b(?:src|href)="(https?:\/\/[^"\/]+)/i', $content, $m);

            return collect($m[1])
                ->reject(fn ($url) => in_array(parse_url($url, PHP_URL_HOST), $known, true))
                ->map(fn ($url) => $file->getRelativePathname().': '.$url);
        })
        ->values()
        ->all();

    expect($offenders)->toBe([]);
});
