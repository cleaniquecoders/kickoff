<?php

use App\Livewire\Admin\Settings\G8Desk;
use App\Models\User;
use App\Settings\G8DeskSettings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

test('an authorized admin can save g8desk support settings', function () {
    Gate::define('manage.settings', fn () => true);

    $this->actingAs(User::factory()->create());

    Livewire::test(G8Desk::class)
        ->set('enabled', true)
        ->set('baseUrl', 'https://support.example.com')
        ->set('publicKey', 'pk_live_123')
        ->set('widgetSecret', 'g8wi_secret_xyz')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('toast');

    $settings = app(G8DeskSettings::class);

    expect($settings->enabled)->toBeTrue();
    expect($settings->base_url)->toBe('https://support.example.com');
    expect($settings->public_key)->toBe('pk_live_123');
    expect($settings->widget_secret)->toBe('g8wi_secret_xyz');
});

test('the public key is required when the widget is enabled', function () {
    Gate::define('manage.settings', fn () => true);

    $this->actingAs(User::factory()->create());

    Livewire::test(G8Desk::class)
        ->set('enabled', true)
        ->set('baseUrl', 'https://g8desk.com')
        ->set('publicKey', '')
        ->set('widgetSecret', 'g8wi_secret_xyz')
        ->call('save')
        ->assertHasErrors(['publicKey' => 'required']);
});

test('the widget renders a signed script for a configured, authenticated user', function () {
    $secret = 'g8wi_test_secret';

    $settings = app(G8DeskSettings::class);
    $settings->enabled = true;
    $settings->base_url = 'https://support.example.com/';
    $settings->public_key = 'pk_live_abc';
    $settings->widget_secret = $secret;
    $settings->save();

    // Deterministic identity so the extracted (JS-encoded) values match the raw
    // values the signature was computed over.
    $this->actingAs(User::factory()->create([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
    ]));

    $html = Blade::render('<x-g8desk-support-widget />');

    // The intake script is embedded with the public key, base URL trimmed of the trailing slash.
    expect($html)->toContain('src="https://support.example.com/intake/widget.js"');
    expect($html)->toContain('data-key="pk_live_abc"');
    expect($html)->toContain('window.g8deskSettings');

    // The signature must be a valid HMAC over the canonical ref|email|name|exp string.
    // Blade's @js encodes string values with single quotes, so match either quote.
    expect($html)->toMatch('/ref:\s*["\']([^"\']+)["\']/');
    preg_match('/ref:\s*["\']([^"\']+)["\']/', $html, $refMatch);
    preg_match('/email:\s*["\']([^"\']+)["\']/', $html, $emailMatch);
    preg_match('/name:\s*["\']([^"\']+)["\']/', $html, $nameMatch);
    preg_match('/exp:\s*(\d+)/', $html, $expMatch);
    preg_match('/sig:\s*["\']([a-f0-9]{64})["\']/', $html, $sigMatch);

    $expected = hash_hmac(
        'sha256',
        $refMatch[1].'|'.$emailMatch[1].'|'.$nameMatch[1].'|'.$expMatch[1],
        $secret,
    );

    expect($sigMatch[1])->toBe($expected);
});

test('the widget renders nothing for a guest', function () {
    $settings = app(G8DeskSettings::class);
    $settings->enabled = true;
    $settings->base_url = 'https://g8desk.com';
    $settings->public_key = 'pk_live_abc';
    $settings->widget_secret = 'g8wi_test_secret';
    $settings->save();

    $html = Blade::render('<x-g8desk-support-widget />');

    expect(trim($html))->toBe('');
});

test('the widget renders nothing when disabled', function () {
    $settings = app(G8DeskSettings::class);
    $settings->enabled = false;
    $settings->public_key = 'pk_live_abc';
    $settings->widget_secret = 'g8wi_test_secret';
    $settings->save();

    $this->actingAs(User::factory()->create());

    $html = Blade::render('<x-g8desk-support-widget />');

    expect(trim($html))->toBe('');
});
