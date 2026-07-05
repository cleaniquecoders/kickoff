<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * DB-stored g8desk support-widget configuration.
 *
 * Drives the native, SDK-free `<x-g8desk-support-widget />` component: when
 * enabled, the authenticated app layout embeds the g8desk intake widget with an
 * HMAC-signed identity payload so signed-in users can open support tickets
 * in-app. `.env` keeps no g8desk keys — everything is admin-editable here and
 * seeded by the settings migration.
 *
 * @property bool   $enabled       Whether the support widget is embedded for authenticated users.
 * @property string $base_url      g8desk base URL (e.g. https://g8desk.com); the widget script loads from {base_url}/intake/widget.js.
 * @property string $public_key    The pk_… data-key from g8desk → channel → Widget → Generate embed.
 * @property string $widget_secret The g8wi_… identity secret used to HMAC-sign the payload; stored encrypted, never sent to the browser.
 */
class G8DeskSettings extends Settings
{
    public bool $enabled;

    public string $base_url;

    public string $public_key;

    public string $widget_secret;

    public static function group(): string
    {
        return 'g8desk';
    }

    /**
     * The identity secret is a credential — keep it encrypted at rest.
     *
     * @return array<int, string>
     */
    public static function encrypted(): array
    {
        return ['widget_secret'];
    }
}
