<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MailSettings extends Settings
{
    public string $mailer;

    public string $host;

    public int $port;

    public string $username;

    public string $password;

    /** SMTP transport encryption: 'tls', 'ssl', or '' for none. */
    public string $encryption;

    public string $from_address;

    public string $from_name;

    /** Inject the open-tracking pixel into outgoing HTML mail (mailhistory). */
    public bool $track_opens;

    /** Rewrite links in outgoing HTML mail through the click tracker (mailhistory). */
    public bool $track_clicks;

    public static function group(): string
    {
        return 'mail';
    }
}
