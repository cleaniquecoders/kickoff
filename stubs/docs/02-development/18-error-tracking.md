# Error Tracking (Bliptrace)

Every Kickoff app ships with [`bliptrace/laravel`](https://packagist.org/packages/bliptrace/laravel),
the Bliptrace SDK. It hooks Laravel's exception handler and sends unhandled exceptions to the
Bliptrace ingest API **after the response**, so a slow or down ingest never slows the app.

Configure it at **Administration → Settings → Error Tracking** (`/admin/settings/bliptrace`,
`manage.settings` permission: administrator and superadmin).

## How a value is chosen

| Order | Source | Notes |
|---|---|---|
| 1 | Saved on the settings page | Stored in the `bliptrace` settings group. A blank field = not set. |
| 2 | `BLIPTRACE_*` env | The SDK falls back to the deprecated `G8SCOPE_*` names, which G8Deck injects today, so an untouched page just works. |
| 3 | SDK default | From `bliptrace/php` (`environment` defaults to `APP_ENV`). |

Each field on the page shows a **UI / Env / Default** badge for where its current value comes from
(*Env* covers both `BLIPTRACE_*` and `G8SCOPE_*`).
Blank fields show the inherited value as their placeholder.

**Capture is off unless an ingest key exists** (page or `BLIPTRACE_KEY`). Enabled is a three-way
choice: *Auto* (follow `BLIPTRACE_ENABLED`, on when a key is set), *On*, *Off*.

## Options

| Field | Env | Default |
|---|---|---|
| Enabled | `BLIPTRACE_ENABLED` | on when a key is set |
| Ingest key | `BLIPTRACE_KEY` | none (stored encrypted, never shown back, only `••••••••1234`) |
| Endpoint | `BLIPTRACE_ENDPOINT` | `https://ingest.scope.g8suite.com` |
| Environment | `BLIPTRACE_ENVIRONMENT` | `APP_ENV` |
| Release | `BLIPTRACE_RELEASE` | none |
| Sample rate (0–1) | `BLIPTRACE_SAMPLE_RATE` | 1.0 (send every event) |
| Timeout (s) | `BLIPTRACE_TIMEOUT` | 3 |
| Max retries | `BLIPTRACE_MAX_RETRIES` | 2 |
| Retry base / cap (ms) | `BLIPTRACE_RETRY_BASE_MS` / `BLIPTRACE_RETRY_CAP_MS` | 100 / 2000 |
| Max stack frames | `BLIPTRACE_MAX_FRAMES` | 30 |
| Max message length | `BLIPTRACE_MAX_MESSAGE` | 2000 |
| Ignored exceptions | — (config) | auth, authorisation, validation, CSRF, 404, 405 |
| Scrubbed request keys | — (config) | password, token, secret, cookie, … |

Filling *Ignored exceptions* or *Scrubbed request keys* replaces the default list.

## Runtime behaviour

- `AppServiceProvider::boot()` calls `BliptraceSettings::apply()`, which lays the resolved values
  over `config('bliptrace')`. No `config:cache` rebuild is needed after a save.
- The saved values are cached (encrypted) under `settings.bliptrace` and the cache is cleared on
  save. Saving also runs `queue:restart` so workers pick the change up.
- Missing `settings` table (fresh install), unmigrated group, or a database/cache outage: the
  page values are skipped silently and env/defaults apply.
- **Send test event** builds a real event through the SDK and posts it to
  `{endpoint}/v1/ingest`, then shows the HTTP result. It uses the saved settings.
- Every save writes an entry to **Security → Audit Trail** (`BliptraceSettings`), with the key
  recorded only as `[redacted]`.

## The SDK's own settings page

`bliptrace/laravel` has its own DB settings layer and page (`settings.enabled`, `ui.enabled` in `config('bliptrace')`). Both are off by default, and Kickoff leaves them off.

## Manual capture

```php
use G8Scope\Laravel\Facades\G8Scope;

try {
    // ...
} catch (\Throwable $e) {
    G8Scope::capture($e);
}
```
