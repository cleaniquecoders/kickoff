<?php

use App\Livewire\Admin\Settings\Seo;
use App\Models\User;
use App\Settings\SeoSettings;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

test('an authorized admin can save seo and analytics settings', function () {
    Gate::define('manage.settings', fn () => true);

    $this->actingAs(User::factory()->create());

    Livewire::test(Seo::class)
        ->set('metaTitle', 'Acme — Build Better')
        ->set('metaDescription', 'Acme helps teams ship faster.')
        ->set('metaKeywords', 'acme, laravel, saas')
        ->set('canonicalEnabled', true)
        ->set('ogImage', 'https://example.com/og.png')
        ->set('twitterSite', '@acme')
        ->set('googleAnalyticsId', 'G-ABC1234567')
        ->set('googleTagManagerId', 'GTM-ABC1234')
        ->set('googleSiteVerification', 'token-123')
        ->set('robotsTxt', "User-agent: *\nDisallow: /admin")
        ->set('organizationName', 'Acme Sdn Bhd')
        ->set('organizationLogo', 'https://example.com/logo.png')
        ->set('organizationSameAs', "https://www.facebook.com/acme\nhttps://www.linkedin.com/company/acme")
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    $settings = app(SeoSettings::class);

    expect($settings->meta_title)->toBe('Acme — Build Better');
    expect($settings->meta_description)->toBe('Acme helps teams ship faster.');
    expect($settings->canonical_enabled)->toBeTrue();
    expect($settings->google_analytics_id)->toBe('G-ABC1234567');
    expect($settings->google_tag_manager_id)->toBe('GTM-ABC1234');
    expect($settings->robots_txt)->toBe("User-agent: *\nDisallow: /admin");
    expect($settings->organization_name)->toBe('Acme Sdn Bhd');
});

test('the google analytics id must be a ga4 measurement id', function () {
    Gate::define('manage.settings', fn () => true);

    $this->actingAs(User::factory()->create());

    Livewire::test(Seo::class)
        ->set('googleAnalyticsId', 'UA-12345-1')
        ->call('save')
        ->assertHasErrors(['googleAnalyticsId' => 'regex']);
});

test('the tag manager id must be a gtm container id', function () {
    Gate::define('manage.settings', fn () => true);

    $this->actingAs(User::factory()->create());

    Livewire::test(Seo::class)
        ->set('googleTagManagerId', 'G-ABC1234567')
        ->call('save')
        ->assertHasErrors(['googleTagManagerId' => 'regex']);
});

test('robots txt content is required', function () {
    Gate::define('manage.settings', fn () => true);

    $this->actingAs(User::factory()->create());

    Livewire::test(Seo::class)
        ->set('robotsTxt', '')
        ->call('save')
        ->assertHasErrors(['robotsTxt' => 'required']);
});

test('blank analytics fields are normalized to null', function () {
    Gate::define('manage.settings', fn () => true);

    $this->actingAs(User::factory()->create());

    Livewire::test(Seo::class)
        ->set('googleAnalyticsId', '  ')
        ->set('googleTagManagerId', '')
        ->call('save')
        ->assertHasNoErrors();

    $settings = app(SeoSettings::class);

    expect($settings->google_analytics_id)->toBeNull();
    expect($settings->google_tag_manager_id)->toBeNull();
});
