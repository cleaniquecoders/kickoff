# Error Tracking (Bliptrace)

Every Kickoff app ships with [`g8scope/laravel`](https://packagist.org/packages/g8scope/laravel),
the Bliptrace SDK. It hooks Laravel's exception handler and sends unhandled exceptions to the
Bliptrace ingest API **after the response**, so a slow or down ingest never slows the app.

Configure it at **Administration → Settings → Error Tracking** (`/admin/settings/bliptrace`,
`manage.settings` permission: administrator and superadmin).

## How a value is chosen

| Order | Source | Notes |
|---|---|---|
| 1 | Saved on the settings page | Stored in the `bliptrace` settings group. A blank field = not set. |
| 2 | `G8SCOPE_*` env | G8Deck injects these automatically, so an untouched page just works. |
| 3 | SDK default | From `g8scope/php` (`environment` defaults to `APP_ENV`). |

Each field on the page shows a **UI / Env / Default** badge for where its current value comes from.
Blank fields show the inherited value as their placeholder.

**Capture is off unless an ingest key exists** (page or `G8SCOPE_KEY`). Enabled is a three-way
choice: *Auto* (follow `G8SCOPE_ENABLED`, on when a key is set), *On*, *Off*.

## Options

| Field | Env | Default |
|---|---|---|
| Enabled | `G8SCOPE_ENABLED` | on when a key is set |
| Ingest key | `G8SCOPE_KEY` | none (stored encrypted, never shown back, only `••••••••1234`) |
| Endpoint | `G8SCOPE_ENDPOINT` | `https://ingest.scope.g8suite.com` |
| Environment | `G8SCOPE_ENVIRONMENT` | `APP_ENV` |
| Release | `G8SCOPE_RELEASE` | none |
| Timeout (s) | `G8SCOPE_TIMEOUT` | 3 |
| Max retries | `G8SCOPE_MAX_RETRIES` | 2 |
| Retry base / cap (ms) | `G8SCOPE_RETRY_BASE_MS` / `G8SCOPE_RETRY_CAP_MS` | 100 / 2000 |
| Max stack frames | `G8SCOPE_MAX_FRAMES` | 30 |
| Max message length | `G8SCOPE_MAX_MESSAGE` | 2000 |
| Ignored exceptions | — (config) | auth, authorisation, validation, CSRF, 404, 405 |
| Scrubbed request keys | — (config) | password, token, secret, cookie, … |

Filling *Ignored exceptions* or *Scrubbed request keys* replaces the default list.

## Runtime behaviour

- `AppServiceProvider::boot()` calls `BliptraceSettings::apply()`, which lays the resolved values
  over `config('g8scope')`. No `config:cache` rebuild is needed after a save.
- The saved values are cached (encrypted) under `settings.bliptrace` and the cache is cleared on
  save. Saving also runs `queue:restart` so workers pick the change up.
- Missing `settings` table (fresh install), unmigrated group, or a database/cache outage: the
  page values are skipped silently and env/defaults apply.
- **Send test event** builds a real event through the SDK and posts it to
  `{endpoint}/v1/ingest`, then shows the HTTP result. It uses the saved settings.
- Every save writes an entry to **Security → Audit Trail** (`BliptraceSettings`), with the key
  recorded only as `[redacted]`.

## The SDK's own settings page

`g8scope/laravel` can publish its own page at `/g8scope/settings` and its own `g8scope` settings
group. Kickoff turns both off in `AppServiceProvider::register()`: it drops the SDK's settings
class and migration path, and moves the SDK page to this page's path with the `manage.settings`
gate, where the app route replaces it.

## Manual capture

```php
use G8Scope\Laravel\Facades\G8Scope;

try {
    // ...
} catch (\Throwable $e) {
    G8Scope::capture($e);
}
```
