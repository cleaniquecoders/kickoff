<?php

use CleaniqueCoders\MailHistory\Actions\GetMailHistoryReport;
use CleaniqueCoders\MailHistory\Listeners\Mails\StoreMessageSending as StoreMailMessageSending;
use CleaniqueCoders\MailHistory\Listeners\Mails\StoreMessageSent as StoreMailMessageSent;
use CleaniqueCoders\MailHistory\Listeners\Notifications\StoreMailSending as StoreNotificationMessageSending;
use CleaniqueCoders\MailHistory\Listeners\Notifications\StoreMailSent as StoreNotificationMessageSent;
use CleaniqueCoders\MailHistory\Models\MailHistory;
use CleaniqueCoders\MailHistory\Models\MailHistoryEvent;
use CleaniqueCoders\MailHistory\Webhooks\MailgunHandler;
use CleaniqueCoders\MailHistory\Webhooks\PostmarkHandler;
use CleaniqueCoders\MailHistory\Webhooks\ResendHandler;
use CleaniqueCoders\MailHistory\Webhooks\SendgridHandler;
use CleaniqueCoders\MailHistory\Webhooks\SesHandler;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;

return [
    'enabled' => env('MAILHISTORY_ENABLED', true),

    'model' => MailHistory::class,

    'event-model' => MailHistoryEvent::class,

    'report' => GetMailHistoryReport::class,

    'user-model' => '\App\Models\User',

    'events' => [
        MessageSending::class => [
            StoreMailMessageSending::class,
        ],
        MessageSent::class => [
            StoreMailMessageSent::class,
        ],
        NotificationSending::class => [
            StoreNotificationMessageSending::class,
        ],
        NotificationSent::class => [
            StoreNotificationMessageSent::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook endpoints for receiving delivery status updates
    | from email providers. Disabled by default — enable per provider.
    |
    */

    'webhooks' => [
        'enabled' => env('MAILHISTORY_WEBHOOKS_ENABLED', false),
        'path' => 'mailhistory/webhooks',
        'middleware' => [],
        'providers' => [
            'mailgun' => [
                'handler' => MailgunHandler::class,
                'signing_key' => env('MAILHISTORY_MAILGUN_SIGNING_KEY'),
            ],
            'ses' => [
                'handler' => SesHandler::class,
            ],
            'postmark' => [
                'handler' => PostmarkHandler::class,
                'token' => env('MAILHISTORY_POSTMARK_WEBHOOK_TOKEN'),
            ],
            'sendgrid' => [
                'handler' => SendgridHandler::class,
                'verification_key' => env('MAILHISTORY_SENDGRID_VERIFICATION_KEY'),
            ],
            'resend' => [
                'handler' => ResendHandler::class,
                'signing_secret' => env('MAILHISTORY_RESEND_SIGNING_SECRET'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking Configuration
    |--------------------------------------------------------------------------
    |
    | Configure open (pixel) and click tracking. When enabled, the package
    | can inject tracking pixels and rewrite links in outgoing emails.
    |
    */

    'tracking' => [
        'open' => [
            'enabled' => env('MAILHISTORY_TRACK_OPENS', false),
        ],
        'click' => [
            'enabled' => env('MAILHISTORY_TRACK_CLICKS', false),
            'exclude_patterns' => [
                '*unsubscribe*',
            ],
        ],
        'path' => 'mailhistory/track',
        'middleware' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Retention Policy
    |--------------------------------------------------------------------------
    |
    | Configure automatic pruning of old mail history records.
    | Use the mailhistory:prune command to apply.
    |
    */

    'retention' => [
        'enabled' => env('MAILHISTORY_RETENTION_ENABLED', false),
        'days' => env('MAILHISTORY_RETENTION_DAYS', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard UI
    |--------------------------------------------------------------------------
    |
    | Enable the built-in Livewire dashboard for viewing mail history
    | statistics and activity. Requires livewire/livewire ^3.0 or ^4.0.
    |
    */

    'ui' => [
        'enabled' => env('MAILHISTORY_UI_ENABLED', false),
        'prefix' => 'mailhistory',
        'middleware' => ['web', 'auth'],
        'name' => 'mailhistory.',
    ],
];
