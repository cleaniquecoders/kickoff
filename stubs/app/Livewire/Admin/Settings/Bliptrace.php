<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Settings;

use App\Models\Audit;
use App\Settings\BliptraceSettings;
use Closure;
use G8Scope\Laravel\Context\RequestContext;
use G8Scope\Php\Client;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RuntimeException;

/**
 * Admin > Settings > Error tracking (Bliptrace, g8scope/laravel).
 *
 * A blank field means "not set here": the value falls through to the
 * G8SCOPE_* env var, then the SDK default. The ingest key is write-only — the
 * stored value is never put on a public property, so it never reaches the
 * browser; only a masked hint is rendered.
 */
#[Layout('components.layouts.app')]
class Bliptrace extends Component
{
    /** Property => BliptraceSettings field, for the plain scalar fields (used to fill the form). */
    private const array SCALARS = [
        'endpoint' => 'endpoint',
        'environment' => 'environment',
        'release' => 'release',
        'timeout' => 'timeout',
        'maxRetries' => 'max_retries',
        'retryBaseMs' => 'retry_base_ms',
        'retryCapMs' => 'retry_cap_ms',
        'maxFrames' => 'max_frames',
        'maxMessage' => 'max_message',
    ];

    /** '' = follow env, 'on', 'off'. */
    public string $enabled = '';

    /** A NEW key only. Never filled with the stored key. */
    public string $key = '';

    public bool $clearKey = false;

    public string $endpoint = '';

    public string $environment = '';

    public string $release = '';

    public string $timeout = '';

    public string $maxRetries = '';

    public string $retryBaseMs = '';

    public string $retryCapMs = '';

    public string $maxFrames = '';

    public string $maxMessage = '';

    /** One exception class per line. */
    public string $ignoreExceptions = '';

    /** One request key per line. */
    public string $scrubKeys = '';

    /** @var array{ok: bool, message: string}|null */
    public ?array $testResult = null;

    public function mount(): void
    {
        $this->authorize('manage.settings');

        $saved = BliptraceSettings::overrides();

        $this->enabled = match ($saved['enabled'] ?? null) {
            true => 'on',
            false => 'off',
            default => '',
        };

        foreach (self::SCALARS as $property => $field) {
            $this->{$property} = (string) ($saved[$field] ?? '');
        }

        $this->ignoreExceptions = implode("\n", $saved['ignore_exceptions'] ?? []);
        $this->scrubKeys = implode("\n", $saved['scrub_keys'] ?? []);
    }

    public function save(): void
    {
        $this->authorize('manage.settings');

        $this->validate($this->rules(), [], [
            'maxRetries' => 'max retries',
            'retryBaseMs' => 'retry base delay',
            'retryCapMs' => 'retry delay cap',
            'maxFrames' => 'max frames',
            'maxMessage' => 'max message length',
            'ignoreExceptions' => 'ignored exceptions',
            'scrubKeys' => 'scrubbed keys',
        ]);

        // Not a validation rule: rules other than `required` skip an empty field.
        if ($this->enabled === 'on' && blank($this->keyAfterSave())) {
            $this->addError('key', __('An ingest key is required to turn error tracking on (none is set in the environment).'));

            return;
        }

        $settings = app(BliptraceSettings::class);
        $before = $settings->toArray();

        $settings->enabled = match ($this->enabled) {
            'on' => true,
            'off' => false,
            default => null,
        };

        if ($this->clearKey) {
            $settings->key = null;
        } elseif (trim($this->key) !== '') {
            $settings->key = trim($this->key);
        }

        $string = fn (string $value): ?string => trim($value) === '' ? null : trim($value);
        $int = fn (string $value): ?int => trim($value) === '' ? null : (int) $value;

        $settings->endpoint = $string($this->endpoint);
        $settings->environment = $string($this->environment);
        $settings->release = $string($this->release);
        $settings->timeout = $int($this->timeout);
        $settings->max_retries = $int($this->maxRetries);
        $settings->retry_base_ms = $int($this->retryBaseMs);
        $settings->retry_cap_ms = $int($this->retryCapMs);
        $settings->max_frames = $int($this->maxFrames);
        $settings->max_message = $int($this->maxMessage);

        $settings->ignore_exceptions = $this->lines($this->ignoreExceptions) ?: null;
        $settings->scrub_keys = $this->lines($this->scrubKeys) ?: null;
        $settings->save();

        BliptraceSettings::forgetCache();
        BliptraceSettings::apply();

        // A queue worker boots once; restart it so queued jobs report with the new values.
        Artisan::call('queue:restart');

        $this->audit($before, $settings->toArray());

        $this->key = '';
        $this->clearKey = false;
        $this->testResult = null;

        $this->dispatch('toast',
            type: 'success',
            message: __('Error tracking settings saved.'),
            duration: 3000,
        );
    }

    /**
     * Report a test exception through the SDK's envelope builder and post it
     * synchronously, so the admin sees the ingest API's answer. The SDK's own
     * transport is fire-and-forget (no result), so this mirrors its contract:
     * POST {endpoint}/v1/ingest with the X-Ingest-Key header.
     */
    public function sendTestEvent(): void
    {
        $this->authorize('manage.settings');

        $config = (array) config('g8scope');

        if (! ($config['enabled'] ?? false)) {
            $this->testResult = ['ok' => false, 'message' => __('Error tracking is off. Add an ingest key and turn it on, then save.')];

            return;
        }

        $envelope = app(Client::class)->buildEnvelope(
            new RuntimeException('Bliptrace test event from '.config('app.name')),
            RequestContext::capture(app()),
        );

        if ($envelope === null) {
            $this->testResult = ['ok' => false, 'message' => __('The SDK skipped the event. Is RuntimeException in the ignored exceptions?')];

            return;
        }

        $url = rtrim((string) $config['endpoint'], '/').'/v1/ingest';

        try {
            $response = Http::withHeaders(['X-Ingest-Key' => (string) $config['key']])
                ->acceptJson()
                ->timeout(max(1, (int) $config['timeout']))
                ->post($url, $envelope);
        } catch (ConnectionException) {
            $this->testResult = ['ok' => false, 'message' => __('Could not reach :url. Check the endpoint.', ['url' => $url])];

            return;
        }

        $this->testResult = $response->successful()
            ? ['ok' => true, 'message' => __('Accepted (HTTP :status). Event :id should appear in Bliptrace shortly.', ['status' => $response->status(), 'id' => $envelope['event_id'] ?? '?'])]
            : ['ok' => false, 'message' => __('Rejected (HTTP :status). Check the ingest key and endpoint.', ['status' => $response->status()])];
    }

    public function render(): View
    {
        $overrides = BliptraceSettings::overrides();
        $resolved = BliptraceSettings::resolve((array) app(BliptraceSettings::BASELINE), $overrides);

        return view('livewire.admin.settings.bliptrace', [
            'effective' => $resolved['config'],
            'sources' => $resolved['sources'],
            'maskedKey' => self::mask($resolved['config']['key']),
            'keyStored' => array_key_exists('key', $overrides),
        ]);
    }

    public static function mask(?string $key): ?string
    {
        if ($key === null || $key === '') {
            return null;
        }

        return str_repeat('•', 8).(strlen($key) >= 12 ? substr($key, -4) : '');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $int = fn (int $min, int $max) => ['nullable', 'integer', "min:{$min}", "max:{$max}"];

        return [
            'enabled' => ['in:,on,off'],
            'key' => ['nullable', 'string', 'max:255'],
            'endpoint' => ['nullable', 'url:http,https', 'max:255'],
            'environment' => ['nullable', 'string', 'max:64'],
            'release' => ['nullable', 'string', 'max:255'],
            'timeout' => $int(1, 30),
            'maxRetries' => $int(0, 10),
            'retryBaseMs' => $int(0, 60000),
            'retryCapMs' => $int(0, 60000),
            'maxFrames' => $int(1, 200),
            'maxMessage' => $int(100, 20000),
            'ignoreExceptions' => ['nullable', 'string', 'max:5000', function (string $attribute, mixed $value, Closure $fail): void {
                foreach ($this->lines((string) $value) as $class) {
                    if (! class_exists($class) && ! interface_exists($class)) {
                        $fail(__(':class is not a known class.', ['class' => $class]));

                        return;
                    }
                }
            }],
            'scrubKeys' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** The key that will be in effect once this form is saved. */
    private function keyAfterSave(): ?string
    {
        return match (true) {
            $this->clearKey => null,
            filled(trim($this->key)) => trim($this->key),
            default => BliptraceSettings::overrides()['key'] ?? null,
        } ?? app(BliptraceSettings::BASELINE)['key'] ?? null;
    }

    /**
     * @return array<int, string>
     */
    private function lines(string $value): array
    {
        return array_values(array_unique(array_filter(array_map(
            fn (string $line) => ltrim(trim($line), '\\'),
            preg_split('/\R/', $value) ?: [],
        ), fn (string $line) => $line !== '')));
    }

    /**
     * Record the change in the Audit Trail. The key is never written, only
     * whether it was set, changed or cleared.
     *
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     */
    private function audit(array $before, array $after): void
    {
        $old = [];
        $new = [];

        foreach (array_keys(BliptraceSettings::FIELDS) as $field) {
            if (($before[$field] ?? null) === ($after[$field] ?? null)) {
                continue;
            }

            $old[$field] = $field === 'key' ? ($before[$field] === null ? null : '[redacted]') : $before[$field] ?? null;
            $new[$field] = $field === 'key' ? ($after[$field] === null ? null : '[redacted]') : $after[$field] ?? null;
        }

        if ($new === []) {
            return;
        }

        $user = auth()->user();

        Audit::create([
            'user_type' => $user?->getMorphClass(),
            'user_id' => $user?->getAuthIdentifier(),
            'event' => 'updated',
            'auditable_type' => BliptraceSettings::class,
            'auditable_id' => 0,
            'old_values' => $old,
            'new_values' => $new,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'tags' => 'settings,bliptrace',
        ]);
    }
}
