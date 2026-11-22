<?php

use App\Livewire\Admin\Settings\Bliptrace;
use App\Models\Audit;
use App\Models\User;
use App\Settings\BliptraceSettings;
use Database\Seeders\AccessControlSeeder;
use G8Scope\Php\Client;
use G8Scope\Php\Config;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

const BLIPTRACE_KEY = 'bt_live_abcdefghijkl1234';

/** Pretend these BLIPTRACE_* values came from the environment, then re-resolve. */
function bliptraceEnv(array $env = []): void
{
    app()->instance(BliptraceSettings::BASELINE, array_replace((array) app(BliptraceSettings::BASELINE), $env));
    BliptraceSettings::forgetCache();
    BliptraceSettings::apply();
}

function bliptraceAdmin(string $role = 'superadmin'): User
{
    test()->seed(AccessControlSeeder::class);
    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

/** The config the SDK's client was actually built with. */
function bliptraceClientConfig(): Config
{
    return (fn () => $this->config)->call(app(Client::class));
}

beforeEach(fn () => bliptraceEnv(['key' => null, 'enabled' => true]));

describe('access', function () {
    it('redirects guests to login', function () {
        $this->get(route('admin.settings.bliptrace'))->assertRedirect(route('login'));
    });

    it('forbids users without settings permission', function () {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.settings.bliptrace'))
            ->assertForbidden();
    });

    it('lets administrators and superadmins in', function (string $role) {
        $this->actingAs(bliptraceAdmin($role))
            ->get(route('admin.settings.bliptrace'))
            ->assertOk()
            ->assertSee('Error tracking')
            ->assertSee('Send test event');
    })->with(['administrator', 'superadmin']);

    it('is in the admin menu', function () {
        $this->actingAs(bliptraceAdmin());

        expect(json_encode(menu('administration')->menus()))->toContain('admin\/settings\/bliptrace');
    });

    it('keeps the SDK\'s own settings layer and page off', function () {
        $action = Route::getRoutes()->match(Request::create('/admin/settings/bliptrace'))->getActionName();

        expect($action)->toContain(Bliptrace::class)
            ->and(config('bliptrace.settings.enabled'))->toBeFalse()
            ->and(config('bliptrace.ui.enabled'))->toBeFalse()
            ->and(collect(Route::getRoutes()->getRoutes())->filter(fn ($route) => str_starts_with($route->getActionName(), 'G8Scope\\'))->all())->toBe([]);
    });
});

describe('save and validation', function () {
    it('saves every option and audits the change without the key', function () {
        $admin = bliptraceAdmin();

        Livewire::actingAs($admin)->test(Bliptrace::class)
            ->set('enabled', 'on')
            ->set('key', BLIPTRACE_KEY)
            ->set('endpoint', 'https://ingest.example.com')
            ->set('environment', 'staging')
            ->set('release', 'app@2.0.0')
            ->set('sampleRate', '0.25')
            ->set('timeout', '5')
            ->set('maxRetries', '3')
            ->set('retryBaseMs', '200')
            ->set('retryCapMs', '4000')
            ->set('maxFrames', '40')
            ->set('maxMessage', '1000')
            ->set('ignoreExceptions', "Illuminate\\Validation\\ValidationException\n\\RuntimeException")
            ->set('scrubKeys', "password\nnric\n")
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast')
            ->assertSet('key', '');

        $settings = app(BliptraceSettings::class);

        expect($settings->enabled)->toBeTrue()
            ->and($settings->key)->toBe(BLIPTRACE_KEY)
            ->and($settings->sample_rate)->toBe(0.25)
            ->and($settings->timeout)->toBe(5)
            ->and($settings->retry_cap_ms)->toBe(4000)
            ->and($settings->ignore_exceptions)->toBe(['Illuminate\\Validation\\ValidationException', 'RuntimeException'])
            ->and($settings->scrub_keys)->toBe(['password', 'nric']);

        $audit = Audit::where('auditable_type', BliptraceSettings::class)->sole();

        expect($audit->user_id)->toBe($admin->id)
            ->and($audit->new_values['key'])->toBe('[redacted]')
            ->and($audit->new_values['endpoint'])->toBe('https://ingest.example.com')
            ->and(json_encode($audit->getAttributes()))->not->toContain(BLIPTRACE_KEY);
    });

    it('rejects invalid values', function (string $property, string $value) {
        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)
            ->set($property, $value)
            ->call('save')
            ->assertHasErrors($property);
    })->with([
        'endpoint not a url' => ['endpoint', 'not a url'],
        'endpoint not http' => ['endpoint', 'ftp://ingest.example.com'],
        'sample rate above 1' => ['sampleRate', '1.5'],
        'sample rate below 0' => ['sampleRate', '-0.1'],
        'sample rate not numeric' => ['sampleRate', 'half'],
        'timeout too high' => ['timeout', '120'],
        'retries negative' => ['maxRetries', '-1'],
        'frames not numeric' => ['maxFrames', 'many'],
        'unknown ignored class' => ['ignoreExceptions', 'App\\Nope\\MissingException'],
        'enabled garbage' => ['enabled', 'maybe'],
    ]);

    it('refuses to turn capture on with no key anywhere', function () {
        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)
            ->set('enabled', 'on')
            ->call('save')
            ->assertHasErrors('key');
    });

    it('allows turning capture on when the key comes from env', function () {
        bliptraceEnv(['key' => 'env-key']);

        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)
            ->set('enabled', 'on')
            ->call('save')
            ->assertHasNoErrors();
    });

    it('blocks saving for users without permission', function () {
        Livewire::actingAs(User::factory()->create())->test(Bliptrace::class)->assertForbidden();
    });
});

describe('masking', function () {
    it('never sends the stored key to the browser', function () {
        $admin = bliptraceAdmin();
        Livewire::actingAs($admin)->test(Bliptrace::class)->set('key', BLIPTRACE_KEY)->call('save');

        Livewire::actingAs($admin)->test(Bliptrace::class)
            ->assertSet('key', '')
            ->assertSee('••••••••1234')
            ->assertDontSee(BLIPTRACE_KEY);

        $this->actingAs($admin)->get(route('admin.settings.bliptrace'))->assertDontSee(BLIPTRACE_KEY);
    });

    it('stores the key encrypted', function () {
        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)->set('key', BLIPTRACE_KEY)->call('save');

        $raw = DB::table('settings')->where('group', 'bliptrace')->where('name', 'key')->value('payload');

        expect($raw)->not->toContain(BLIPTRACE_KEY)
            ->and(Cache::get(BliptraceSettings::CACHE_KEY))->not->toContain(BLIPTRACE_KEY);
    });

    it('keeps the saved key when the field is left blank, and can clear it', function () {
        $admin = bliptraceAdmin();
        Livewire::actingAs($admin)->test(Bliptrace::class)->set('key', BLIPTRACE_KEY)->call('save');

        Livewire::actingAs($admin)->test(Bliptrace::class)->set('release', 'x@1')->call('save');
        expect(app(BliptraceSettings::class)->key)->toBe(BLIPTRACE_KEY);

        Livewire::actingAs($admin)->test(Bliptrace::class)->set('clearKey', true)->call('save');
        expect(app(BliptraceSettings::class)->key)->toBeNull();
    });
});

describe('resolution order', function () {
    it('prefers UI, then env, then the SDK default', function () {
        $resolved = BliptraceSettings::resolve(
            ['key' => 'env-key', 'endpoint' => 'https://env.example.com', 'environment' => '', 'timeout' => '7', 'sample_rate' => '0.5'],
            ['endpoint' => 'https://ui.example.com'],
        );

        expect($resolved['config'])->toMatchArray([
            'endpoint' => 'https://ui.example.com',
            'key' => 'env-key',
            'timeout' => 7,
            'environment' => config('app.env'),
            'max_frames' => 30,
            'sample_rate' => 0.5,
            'enabled' => true,
        ])->and($resolved['sources'])->toMatchArray([
            'endpoint' => 'ui',
            'key' => 'env',
            'timeout' => 'env',
            'sample_rate' => 'env',
            'environment' => 'default',
            'max_frames' => 'default',
        ]);
    });

    it('shows the source of each field on the page', function () {
        bliptraceEnv(['key' => 'env-key-0000000000']);

        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)
            ->set('endpoint', 'https://ui.example.com')
            ->call('save')
            ->assertSeeInOrder(['Ingest key', 'Env'])
            ->assertSeeInOrder(['Endpoint', 'UI'])
            ->assertSeeInOrder(['Release', 'Default']);
    });

    it('labels an env value equal to the SDK default as default', function () {
        expect(BliptraceSettings::resolve(['sample_rate' => '1.0', 'timeout' => '3'], [])['sources'])
            ->toMatchArray(['sample_rate' => 'default', 'timeout' => 'default']);
    });

    it('has the SDK honour BLIPTRACE_* env on its own, with nothing saved', function (bool $enabled) {
        // Exactly what the SDK's merged config holds when G8Deck injects env.
        config(['bliptrace.key' => 'env-key', 'bliptrace.endpoint' => 'https://env.example.com', 'bliptrace.enabled' => $enabled, 'bliptrace.sample_rate' => '0.5']);
        app()->forgetInstance(Client::class);

        $client = bliptraceClientConfig();

        expect($client->endpoint)->toBe('https://env.example.com')
            ->and($client->enabled)->toBe($enabled)
            ->and($client->sampleRate)->toBe(0.5);
    })->with(['enabled' => true, 'disabled' => false]);

    it('still honours the deprecated G8SCOPE_* env through the SDK config', function () {
        putenv('BLIPTRACE_ENDPOINT');
        putenv('G8SCOPE_ENDPOINT=https://legacy.example.com');

        try {
            $sdkConfig = require glob(base_path('vendor/bliptrace/laravel/config/*.php'))[0];
        } finally {
            putenv('G8SCOPE_ENDPOINT');
        }

        expect($sdkConfig['endpoint'])->toBe('https://legacy.example.com')
            ->and(BliptraceSettings::resolve($sdkConfig, [])['sources']['endpoint'])->toBe('env');
    });

    it('lets env work with zero clicks', function () {
        bliptraceEnv(['key' => 'env-key', 'endpoint' => 'https://env.example.com']);

        expect(config('bliptrace.enabled'))->toBeTrue()
            ->and(config('bliptrace.key'))->toBe('env-key')
            ->and(bliptraceClientConfig()->endpoint)->toBe('https://env.example.com');
    });
});

describe('runtime override', function () {
    it('applies saved values to the SDK without a config cache rebuild', function () {
        bliptraceEnv(['key' => 'env-key']);
        Cache::put('illuminate:queue:restart', null);

        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)
            ->set('key', BLIPTRACE_KEY)
            ->set('endpoint', 'https://ui.example.com')
            ->set('release', 'app@3.0.0')
            ->set('maxFrames', '12')
            ->set('sampleRate', '0.1')
            ->call('save');

        $client = bliptraceClientConfig();

        expect($client->key)->toBe(BLIPTRACE_KEY)
            ->and($client->endpoint)->toBe('https://ui.example.com')
            ->and($client->release)->toBe('app@3.0.0')
            ->and($client->maxFrames)->toBe(12)
            ->and($client->sampleRate)->toBe(0.1)
            ->and($client->enabled)->toBeTrue()
            ->and(Cache::get('illuminate:queue:restart'))->not->toBeNull();
    });

    it('serves overrides from cache and refreshes it on save', function () {
        $admin = bliptraceAdmin();
        Livewire::actingAs($admin)->test(Bliptrace::class)->set('release', 'one')->call('save');

        expect(BliptraceSettings::overrides()['release'])->toBe('one');

        DB::table('settings')->where('group', 'bliptrace')->where('name', 'release')->update(['payload' => json_encode('sneaky')]);
        expect(BliptraceSettings::overrides()['release'])->toBe('one');

        Livewire::actingAs($admin)->test(Bliptrace::class)->set('release', 'two')->call('save');
        expect(BliptraceSettings::overrides()['release'])->toBe('two');
    });

    it('lets the UI switch capture off even with an env key', function () {
        bliptraceEnv(['key' => 'env-key']);

        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)->set('enabled', 'off')->call('save');

        expect(config('bliptrace.enabled'))->toBeFalse()
            ->and(bliptraceClientConfig()->enabled)->toBeFalse();
    });
});

describe('disabled by default', function () {
    it('is off on a fresh app with no key', function () {
        expect(config('bliptrace.enabled'))->toBeFalse()
            ->and(app(Client::class)->buildEnvelope(new RuntimeException('boom')))->toBeNull();
    });

    it('does not send a test event while off', function () {
        Http::fake();

        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)
            ->call('sendTestEvent')
            ->assertSet('testResult.ok', false)
            ->assertSee('Error tracking is off');

        Http::assertNothingSent();
    });
});

describe('test event', function () {
    beforeEach(fn () => bliptraceEnv(['key' => BLIPTRACE_KEY, 'endpoint' => 'https://ingest.example.com/']));

    it('reports a test exception and shows the result', function () {
        Http::fake(['ingest.example.com/*' => Http::response(['accepted' => true], 202)]);

        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)
            ->call('sendTestEvent')
            ->assertSet('testResult.ok', true)
            ->assertSee('Accepted (HTTP 202)');

        Http::assertSent(fn (HttpRequest $request) => $request->url() === 'https://ingest.example.com/v1/ingest'
            && $request->hasHeader('X-Ingest-Key', BLIPTRACE_KEY)
            && str_contains((string) json_encode($request->data()), 'Bliptrace test event'));
    });

    it('shows a rejection', function () {
        Http::fake(['*' => Http::response([], 401)]);

        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)
            ->call('sendTestEvent')
            ->assertSet('testResult.ok', false)
            ->assertSee('Rejected (HTTP 401)');
    });

    it('shows an unreachable endpoint', function () {
        Http::fake(fn () => throw new ConnectionException('refused'));

        Livewire::actingAs(bliptraceAdmin())->test(Bliptrace::class)
            ->call('sendTestEvent')
            ->assertSet('testResult.ok', false)
            ->assertSee('Could not reach');
    });
});

describe('missing settings table', function () {
    it('falls back to env silently', function () {
        $settings = app(BliptraceSettings::class);
        $settings->key = 'ui-key';
        $settings->save();
        Schema::drop('settings');
        app()->forgetInstance(BliptraceSettings::class); // as on the next boot

        bliptraceEnv(['key' => 'env-key', 'endpoint' => 'https://env.example.com']);

        expect(BliptraceSettings::overrides())->toBe([])
            ->and(config('bliptrace.key'))->toBe('env-key')
            ->and(config('bliptrace.enabled'))->toBeTrue()
            ->and(bliptraceClientConfig()->endpoint)->toBe('https://env.example.com');
    });

    it('falls back to env when the database is unreachable', function () {
        $settings = app(BliptraceSettings::class);
        $settings->key = 'ui-key';
        $settings->save();

        config([
            'database.connections.unreachable' => ['driver' => 'sqlite', 'database' => '/nonexistent/dir/db.sqlite'],
            'settings.repositories.database.connection' => 'unreachable',
        ]);
        app()->forgetScopedInstances(); // as on the next boot

        bliptraceEnv(['key' => 'env-key']);

        expect(BliptraceSettings::overrides())->toBe([])
            ->and(config('bliptrace.key'))->toBe('env-key');
    });
});
