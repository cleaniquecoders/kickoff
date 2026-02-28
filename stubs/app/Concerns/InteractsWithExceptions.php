<?php

declare(strict_types=1);

namespace App\Concerns;

trait InteractsWithExceptions
{
    public static function unless(bool $condition, ?string $method = null, ?string $message = null, ...$args): void
    {
        self::throwUnless($condition, $method, $message, ...$args);
    }

    public static function throwIf(bool $condition, ?string $method = null, ?string $message = null, ...$args): void
    {
        if (! $condition) {
            if ($method && method_exists(__CLASS__, $method)) {
                throw self::$method(...$args);
            }

            throw new self($message);
        }
    }

    public static function throwUnless(bool $condition, ?string $method = null, ?string $message = null, ...$args): void
    {
        self::throwIf(! $condition, $method, $message, ...$args);
    }
}
