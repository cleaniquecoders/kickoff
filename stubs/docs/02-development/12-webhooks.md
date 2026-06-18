# Webhooks

Outgoing webhooks via [cleaniquecoders/laravel-config-webhook](https://github.com/cleaniquecoders/laravel-config-webhook):
subscribers register a URL + secret + event types; the package delivers HMAC-SHA256-signed JSON
payloads with queued retries (exponential backoff) and full delivery logs.

## Pre-configured Defaults

- **Admin UI** at `/admin/webhooks` (sidebar: Settings → Webhooks), gated by the
  `admin.manage.webhooks` permission, rendered in the app layout
- **Queue**: deliveries run on the `webhooks` queue — already registered in
  `config/horizon.php`, so Horizon picks it up automatically
- **Event catalogue** (`config/config-webhook.php` → `events`): seeded with
  `user.created` / `user.updated` / `user.deleted` as examples — replace with your domain events

## Dispatching Events

Register event types in the config (or via `ConfigWebhook::registerEvent()`), then dispatch —
see the [package README](https://github.com/cleaniquecoders/laravel-config-webhook#usage) for
the dispatch and domain-event mapping API.

## Receiving Side (Verification)

Receivers verify the `X-Webhook-Signature` header: `hash_hmac('sha256', $rawBody, $secret)`.

## Maintenance

Prune old delivery logs periodically — add to your scheduler (`routes/console.php`):

```php
Schedule::command('config-webhook:prune --days=30')->daily();
```

## Env Toggles

```env
CONFIG_WEBHOOK_FEATURE=true
CONFIG_WEBHOOK_QUEUE=webhooks
```
