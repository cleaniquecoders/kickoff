<?php

// config for CleaniqueCoders/ConfigWebhook
return [

    /*
    |--------------------------------------------------------------------------
    | Feature Toggle
    |--------------------------------------------------------------------------
    |
    | Master switch for the package. When disabled, dispatching webhooks
    | becomes a no-op and the (optional) admin route is not registered.
    |
    */
    'feature' => env('CONFIG_WEBHOOK_FEATURE', true),

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | The queue name that webhook delivery jobs are pushed onto. Registered in
    | config/horizon.php so Horizon workers process it.
    |
    */
    'queue' => env('CONFIG_WEBHOOK_QUEUE', 'webhooks'),

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The application's authenticatable model. Used for the optional
    | `webhooks.user_id` ownership relation. Set to null to disable the
    | relation entirely.
    |
    */
    'user_model' => env('CONFIG_WEBHOOK_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    */
    'table' => [
        'webhooks' => 'webhooks',
        'delivery_logs' => 'webhook_delivery_logs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Delivery Defaults
    |--------------------------------------------------------------------------
    |
    | Default values applied to new webhooks and the bounds enforced by the
    | admin UI validation.
    |
    */
    'defaults' => [
        'max_retries' => 5,
        'timeout' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Backoff
    |--------------------------------------------------------------------------
    |
    | Exponential backoff for failed deliveries. The delay before attempt N is
    | `base * (multiplier ^ (N - 1))` seconds. With base=10, multiplier=3 that
    | yields 10s, 30s, 90s, 270s, 810s ...
    |
    */
    'backoff' => [
        'base' => 10,
        'multiplier' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Signature
    |--------------------------------------------------------------------------
    |
    | Outgoing payloads are signed with an HMAC of the JSON body using the
    | webhook's secret. Receivers verify the signature header to authenticate
    | the request.
    |
    */
    'signature' => [
        'algo' => 'sha256',
        'header' => 'X-Webhook-Signature',
        'event_header' => 'X-Webhook-Event',
        'delivery_header' => 'X-Webhook-Delivery',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP
    |--------------------------------------------------------------------------
    */
    'user_agent' => env('CONFIG_WEBHOOK_USER_AGENT', 'Laravel-Config-Webhook'),

    // Truncate stored response bodies to this many characters (0 = unlimited).
    'response_body_limit' => 5000,

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    |
    | The catalogue of event types a webhook may subscribe to, as a
    | `type => label` map (the label is shown in the admin UI). Hosts may also
    | register events at runtime via ConfigWebhook::registerEvent() and map
    | domain events via ConfigWebhook::listen(). Add your own domain events
    | here, e.g. 'order.created' => 'Order Created'.
    |
    */
    'events' => [
        'user.created' => 'User Created',
        'user.updated' => 'User Updated',
        'user.deleted' => 'User Deleted',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization Gate
    |--------------------------------------------------------------------------
    |
    | Controls access to the admin UI. Maps to the admin.manage.webhooks
    | permission seeded from config/access-control.php.
    |
    */
    'gate' => 'admin.manage.webhooks',

    /*
    |--------------------------------------------------------------------------
    | Admin Route
    |--------------------------------------------------------------------------
    |
    | Registers the full-page Livewire management screen, linked from the
    | sidebar Settings menu.
    |
    */
    'route' => [
        'enabled' => env('CONFIG_WEBHOOK_ROUTE_ENABLED', true),
        'prefix' => 'admin/webhooks',
        'name' => 'config-webhook.index',
        'middleware' => ['web', 'auth'],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI
    |--------------------------------------------------------------------------
    */
    'ui' => [
        // Render the admin UI inside the application layout.
        'layout' => 'components.layouts.app',
        'per_page' => 15,
    ],

];
