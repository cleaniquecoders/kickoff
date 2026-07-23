<?php

test('robots.txt serves the configured content with the sitemap appended', function () {
    config(['seo.robots_txt' => "User-agent: *\nDisallow: /admin"]);

    $response = $this->get('/robots.txt');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

    expect($response->getContent())
        ->toContain("User-agent: *\nDisallow: /admin")
        ->toContain('Sitemap: '.route('seo.sitemap'));
});

test('sitemap.xml lists the public home route', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk();

    expect($response->getContent())
        ->toContain('<urlset')
        ->toContain(route('home'));
});

test('the ga4 snippet renders on the home page when a measurement id is set', function () {
    config(['seo.google.analytics_id' => 'G-TEST123456']);

    $this->get('/')
        ->assertOk()
        ->assertSee('googletagmanager.com/gtag/js?id=G-TEST123456', false)
        ->assertSee("gtag('config', 'G-TEST123456')", false);
});

test('no analytics snippets render when no ids are configured', function () {
    config([
        'seo.google.analytics_id' => null,
        'seo.google.tag_manager_id' => null,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertDontSee('googletagmanager.com', false);
});

test('the gtm snippet and noscript iframe render when a container id is set', function () {
    config(['seo.google.tag_manager_id' => 'GTM-TEST123']);

    $response = $this->get('/');

    $response->assertOk();

    expect($response->getContent())
        ->toContain("googletagmanager.com/gtm.js?id='+i")
        ->toContain('googletagmanager.com/ns.html?id=GTM-TEST123');
});

test('meta description, canonical and open graph tags render on the home page', function () {
    config([
        'seo.meta.description' => 'Acme helps teams ship faster.',
        'seo.canonical' => true,
        'seo.og_image' => 'https://example.com/og.png',
    ]);

    $response = $this->get('/');

    $response->assertOk();

    expect($response->getContent())
        ->toContain('<meta name="description" content="Acme helps teams ship faster.">')
        ->toContain('<link rel="canonical"')
        ->toContain('<meta property="og:title"')
        ->toContain('<meta property="og:image" content="https://example.com/og.png">')
        ->toContain('<meta name="twitter:card" content="summary_large_image">');
});

test('the organization schema renders once an organization name is configured', function () {
    config([
        'seo.organization.name' => 'Acme Sdn Bhd',
        'seo.organization.same_as' => "https://www.facebook.com/acme\nhttps://x.com/acme",
    ]);

    $response = $this->get('/');

    $response->assertOk();

    expect($response->getContent())
        ->toContain('"@type":"Organization"')
        ->toContain('"name":"Acme Sdn Bhd"')
        ->toContain('https://www.facebook.com/acme');
});

test('schema helpers emit valid json-ld', function () {
    $html = (string) seo_schema_faq([
        ['question' => 'What is Kickoff?', 'answer' => 'A Laravel starter.'],
    ]);

    expect($html)->toStartWith('<script type="application/ld+json">');

    preg_match('#<script type="application/ld\+json">(.*)</script>#s', $html, $matches);
    $decoded = json_decode($matches[1], true);

    expect($decoded['@context'])->toBe('https://schema.org');
    expect($decoded['@type'])->toBe('FAQPage');
    expect($decoded['mainEntity'][0]['name'])->toBe('What is Kickoff?');

    $breadcrumb = (string) seo_schema_breadcrumb([
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Blog'],
    ]);

    preg_match('#<script type="application/ld\+json">(.*)</script>#s', $breadcrumb, $matches);
    $decoded = json_decode($matches[1], true);

    expect($decoded['@type'])->toBe('BreadcrumbList');
    expect($decoded['itemListElement'])->toHaveCount(2);
    expect($decoded['itemListElement'][1])->not->toHaveKey('item');
});
