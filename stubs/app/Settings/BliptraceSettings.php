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
 * DB-stored Bliptrace (bliptrace/laravel) error-tracking overrides.
 *
 * Every property is nullable: null means "not set in the UI", so the value
 * falls through to the BLIPTRACE_* env var and then to the SDK default. That is
 * what lets G8Deck-injected env keep working with zero clicks. apply() lays the
 * resolved values over config('bliptrace') at boot and again after a save.
 */
class BliptraceSettings extends Settings
{
    public const string CACHE_KEY = 'settings.bliptrace';

    /** config('bliptrace') as env/config resolved it, before any UI override. */
    public const string BASELINE = 'bliptrace.baseline';

    /** Fields the UI may override, mapped to their env var (null = config-file only). */
    public const array FIELDS = [
        'enabled' => 'BLIPTRACE_ENABLED',
        'key' => 'BLIPTRACE_KEY',
        'endpoint' => 'BLIPTRACE_ENDPOINT',
        'environment' => 'BLIPTRACE_ENVIRONMENT',
        'release' => 'BLIPTRACE_RELEASE',
        'sample_rate' => 'BLIPTRACE_SAMPLE_RATE',
        'timeout' => 'BLIPTRACE_TIMEOUT',
        'max_retries' => 'BLIPTRACE_MAX_RETRIES',
        'retry_base_ms' => 'BLIPTRACE_RETRY_BASE_MS',
        'retry_cap_ms' => 'BLIPTRACE_RETRY_CAP_MS',
        'max_frames' => 'BLIPTRACE_MAX_FRAMES',
        'max_message' => 'BLIPTRACE_MAX_MESSAGE',
        'max_breadcrumbs' => 'BLIPTRACE_MAX_BREADCRUMBS',
        'breadcrumbs_requests' => 'BLIPTRACE_BREADCRUMBS_REQUESTS',
        'breadcrumbs_queries' => 'BLIPTRACE_BREADCRUMBS_QUERIES',
        'breadcrumbs_logs' => 'BLIPTRACE_BREADCRUMBS_LOGS',
        'breadcrumbs_jobs' => 'BLIPTRACE_BREADCRUMBS_JOBS',
        'breadcrumbs_cache' => 'BLIPTRACE_BREADCRUMBS_CACHE',
        'ignore_exceptions' => null,
        'scrub_keys' => null,
        'tags' => null,
    ];

    /** Breadcrumb sources: field breadcrumbs_{source} maps to config('bliptrace.breadcrumbs.{source}'). */
    public const array BREADCRUMBS = ['requests', 'queries', 'logs', 'jobs', 'cache'];

    public ?bool $enabled;

    public ?string $key;

    public ?string $endpoint;

    public ?string $environment;

    public ?string $release;

    public ?float $sample_rate;

    public ?int $timeout;

    public ?int $max_retries;

    public ?int $retry_base_ms;

    public ?int $retry_cap_ms;

    public ?int $max_frames;

    public ?int $max_message;

    public ?int $max_breadcrumbs;

    public ?bool $breadcrumbs_requests;

    public ?bool $breadcrumbs_queries;

    public ?bool $breadcrumbs_logs;

    public ?bool $breadcrumbs_jobs;

    public ?bool $breadcrumbs_cache;

    /** @var array<int, string>|null */
    public ?array $ignore_exceptions;

    /** @var array<int, string>|null */
    public ?array $scrub_keys;

    /** @var array<string, string>|null */
    public ?array $tags;

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
     * @param  array<string, mixed>  $baseline  config('bliptrace') before overrides
     * @param  array<string, mixed>  $overrides  self::overrides()
     * @return array{config: array<string, mixed>, sources: array<string, string>}
     */
    public static function resolve(array $baseline, array $overrides): array
    {
        $defaults = self::defaults();
        $config = $defaults; // every field is set below
        $sources = [];

        foreach (array_keys(self::FIELDS) as $field) {
            if (array_key_exists($field, $overrides)) {
                $config[$field] = $overrides[$field];
                $sources[$field] = 'ui';

                continue;
            }

            // An empty env var (BLIPTRACE_RELEASE=) counts as unset.
            $value = str_starts_with($field, 'breadcrumbs_')
                ? $baseline['breadcrumbs'][substr($field, 12)] ?? null
                : $baseline[$field] ?? null;
            $value = $value === null || $value === '' ? $defaults[$field] : $value;
            $config[$field] = $value;
            // Compared with the SDK default rather than env() so the label stays
            // right under config:cache, where env() no longer sees .env values.
            $sources[$field] = self::FIELDS[$field] !== null && self::normalise($value) !== self::normalise($defaults[$field])
                ? 'env'
                : 'default';
        }

        $config['sample_rate'] = (float) $config['sample_rate'];

        foreach (['timeout', 'max_retries', 'retry_base_ms', 'retry_cap_ms', 'max_frames', 'max_message', 'max_breadcrumbs'] as $int) {
            $config[$int] = (int) $config[$int];
        }

        foreach (self::BREADCRUMBS as $source) {
            $config["breadcrumbs_{$source}"] = filter_var($config["breadcrumbs_{$source}"], FILTER_VALIDATE_BOOLEAN);
        }

        $config['key'] = filled($config['key']) ? (string) $config['key'] : null;
        $config['release'] = filled($config['release']) ? (string) $config['release'] : null;
        $config['enabled'] = filter_var($config['enabled'], FILTER_VALIDATE_BOOLEAN) && $config['key'] !== null;

        return ['config' => $config, 'sources' => $sources];
    }

    /**
     * Lay the resolved values over config('bliptrace') and drop the SDK's cached
     * client so the next capture uses them. Never throws.
     *
     * @return array<string, string> the source of each field
     */
    public static function apply(): array
    {
        $app = app();

        if (! $app->bound(self::BASELINE)) {
            $app->instance(self::BASELINE, (array) config('bliptrace', []));
        }

        $resolved = self::resolve((array) $app->make(self::BASELINE), self::overrides());
        $config = $resolved['config'];

        // The SDK reads the toggles nested, as bliptrace.breadcrumbs.{source}.
        foreach (self::BREADCRUMBS as $source) {
            $config['breadcrumbs'][$source] = $config["breadcrumbs_{$source}"];
            unset($config["breadcrumbs_{$source}"]);
        }

        config(['bliptrace' => array_replace((array) config('bliptrace', []), $config)]);

        $app->forgetInstance(Client::class);
        $app->forgetInstance(Reporter::class);
        G8Scope::clearResolvedInstance(Reporter::class);

        return $resolved['sources'];
    }

    /**
     * SDK defaults (from bliptrace/php's Config), with the Laravel-specific
     * ones: environment follows APP_ENV, capture is on once a key exists, and
     * the breadcrumb toggles mirror bliptrace/laravel's config (cache off).
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
            'sample_rate' => $sdk->sampleRate,
            'timeout' => $sdk->timeout,
            'max_retries' => $sdk->maxRetries,
            'retry_base_ms' => $sdk->retryBaseMs,
            'retry_cap_ms' => $sdk->retryCapMs,
            'max_frames' => $sdk->maxFrames,
            'max_message' => $sdk->maxMessage,
            'max_breadcrumbs' => $sdk->maxBreadcrumbs,
            'breadcrumbs_requests' => true,
            'breadcrumbs_queries' => true,
            'breadcrumbs_logs' => true,
            'breadcrumbs_jobs' => true,
            'breadcrumbs_cache' => false,
            'ignore_exceptions' => [],
            'scrub_keys' => [],
            'tags' => [],
        ];
    }

    private static function normalise(mixed $value): string
    {
        return match (true) {
            is_bool($value) => $value ? '1' : '0',
            is_array($value) => (string) json_encode($value),
            // "1.0" from env and 1.0 from the SDK are the same value.
            is_numeric($value) => (string) (float) $value,
            default => (string) $value,
        };
    }
}
