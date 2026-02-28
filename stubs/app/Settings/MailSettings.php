<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MailSettings extends Settings
{
    public string $from_address;

    public string $from_name;

    public static function group(): string
    {
        return 'mail';
    }
}
