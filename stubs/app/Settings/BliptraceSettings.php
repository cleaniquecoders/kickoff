<?php

declare(strict_types=1);

namespace App\Settings;

use G8Scope\Laravel\Facades\G8Scope;
use G8Scope\Laravel\Reporter;
use G8Scope\Php\Client;
use G8Scope\Php\Config;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelSettings\Settings;
use Throwable;

/**
 * DB-stored Bliptrace (g8scope/laravel) error-tracking overrides.
 *
 * Every property is nullable: null means "not set in the UI", so the value
 * falls through to the G8SCOPE_* env var and then to the SDK default. That is
 * what lets G8Deck-injected env keep working with zero clicks. apply() lays the
 * resolved values over config('g8scope') at boot and again after a save.
 */
class BliptraceSettings extends Settings
{
    public const string CACHE_KEY = 'settings.bliptrace';

    /** config('g8scope') as env/config resolved it, before any UI override. */
    public const string BASELINE = 'bliptrace.baseline';

    /** Fields the UI may override, mapped to their env var (null = config-file only). */
    public const array FIELDS = [
        'enabled' => 'G8SCOPE_ENABLED',
        'key' => 'G8SCOPE_KEY',
        'endpoint' => 'G8SCOPE_ENDPOINT',
        'environment' => 'G8SCOPE_ENVIRONMENT',
        'release' => 'G8SCOPE_RELEASE',
        'timeout' => 'G8SCOPE_TIMEOUT',
        'max_retries' => 'G8SCOPE_MAX_RETRIES',
        'retry_base_ms' => 'G8SCOPE_RETRY_BASE_MS',
        'retry_cap_ms' => 'G8SCOPE_RETRY_CAP_MS',
        'max_frames' => 'G8SCOPE_MAX_FRAMES',
        'max_message' => 'G8SCOPE_MAX_MESSAGE',
        'ignore_exceptions' => null,
        'scrub_keys' => null,
    ];

    public ?bool $enabled;

    public ?string $key;

    public ?string $endpoint;

    public ?string $environment;

    public ?string $release;

    public ?int $timeout;

    public ?int $max_retries;

    public ?int $retry_base_ms;

    public ?int $retry_cap_ms;

    public ?int $max_frames;

    public ?int $max_message;

    /** @var array<int, string>|null */
    public ?array $ignore_exceptions;

    /** @var array<int, string>|null */
    public ?array $scrub_keys;

    public static function group(): string
    {
        return 'bliptrace';
    }

    /**
     * The ingest key is a credential — keep it encrypted at rest.
     *
     * @return array<int, string>
     */
    public static function encrypted(): array
    {
        return ['key'];
    }

    /**
     * The values saved in the UI (non-null only). Cached encrypted so the key
     * never sits in the cache store in plain text. Returns [] when the settings
     * table is missing, the group isn't migrated, or the DB/cache is down.
     *
     * @return array<string, mixed>
     */
    public static function overrides(): array
    {
        try {
            return decrypt(Cache::rememberForever(self::CACHE_KEY, fn () => encrypt(
                array_filter(app(self::class)->toArray(), fn ($value) => $value !== null),
            )));
        } catch (Throwable) {
            return [];
        }
    }

    public static function forgetCache(): void
    {
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (Throwable) {
            // Cache store down — the next boot falls back to env anyway.
        }
    }

    /**
     * Resolve each field: UI value when set, else env, else SDK default.
     * The SDK is only enabled when an ingest key is present, so a fresh app
     * with no key never tries to send anything.
     *
     * @param  array<string, mixed>  $baseline  config('g8scope') before overrides
     * @param  array<string, mixed>  $overrides  self::overrides()
     * @return array{config: array<string, mixed>, sources: array<string, string>}
     */
    public static function resolve(array $baseline, array $overrides): array
    {
        $defaults = self::defaults();
        $config = [];
        $sources = [];

        foreach (array_keys(self::FIELDS) as $field) {
            if (array_key_exists($field, $overrides)) {
                $config[$field] = $overrides[$field];
                $sources[$field] = 'ui';

                continue;
            }

            // An empty env var (G8SCOPE_RELEASE=) counts as unset.
            $value = $baseline[$field] ?? null;
            $value = $value === null || $value === '' ? $defaults[$field] : $value;
            $config[$field] = $value;
            // Compared with the SDK default rather than env() so the label stays
            // right under config:cache, where env() no longer sees .env values.
            $sources[$field] = self::FIELDS[$field] !== null && self::normalise($value) !== self::normalise($defaults[$field])
                ? 'env'
                : 'default';
        }

        foreach (['timeout', 'max_retries', 'retry_base_ms', 'retry_cap_ms', 'max_frames', 'max_message'] as $int) {
            $config[$int] = (int) $config[$int];
        }

        $config['key'] = filled($config['key']) ? (string) $config['key'] : null;
        $config['release'] = filled($config['release']) ? (string) $config['release'] : null;
        $config['enabled'] = filter_var($config['enabled'], FILTER_VALIDATE_BOOLEAN) && $config['key'] !== null;

        return ['config' => $config, 'sources' => $sources];
    }

    /**
     * Lay the resolved values over config('g8scope') and drop the SDK's cached
     * client so the next capture uses them. Never throws.
     *
     * @return array<string, string> the source of each field
     */
    public static function apply(): array
    {
        $app = app();

        if (! $app->bound(self::BASELINE)) {
            $app->instance(self::BASELINE, (array) config('g8scope', []));
        }

        $resolved = self::resolve((array) $app->make(self::BASELINE), self::overrides());

        config(['g8scope' => array_replace((array) config('g8scope', []), $resolved['config'])]);

        $app->forgetInstance(Client::class);
        $app->forgetInstance(Reporter::class);
        G8Scope::clearResolvedInstance(Reporter::class);

        return $resolved['sources'];
    }

    /**
     * SDK defaults (from g8scope/php's Config), with the two Laravel-specific
     * ones: environment follows APP_ENV, and capture is on once a key exists.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        $sdk = Config::fromArray([]);

        return [
            'enabled' => true,
            'key' => null,
            'endpoint' => $sdk->endpoint,
            'environment' => (string) config('app.env', $sdk->environment),
            'release' => null,
            'timeout' => $sdk->timeout,
            'max_retries' => $sdk->maxRetries,
            'retry_base_ms' => $sdk->retryBaseMs,
            'retry_cap_ms' => $sdk->retryCapMs,
            'max_frames' => $sdk->maxFrames,
            'max_message' => $sdk->maxMessage,
            'ignore_exceptions' => [],
            'scrub_keys' => [],
        ];
    }

    private static function normalise(mixed $value): string
    {
        return is_bool($value) ? ($value ? '1' : '0') : (is_array($value) ? (string) json_encode($value) : (string) $value);
    }
}
