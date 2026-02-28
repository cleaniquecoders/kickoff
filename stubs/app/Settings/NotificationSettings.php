<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationSettings extends Settings
{
    public bool $enabled;

    public array $channels;

    public static function group(): string
    {
        return 'notification';
    }
}
