# g8desk Support Widget

A native, **SDK-free** integration that embeds the [g8desk](https://g8desk.com) support
widget so signed-in users can open and track support tickets without leaving your app.

No npm package, no vendor JS SDK — just DB-stored settings, one anonymous Blade component,
and roughly five lines of markup rendered into the authenticated layout.

## How it works

1. **Settings** (`App\Settings\G8DeskSettings`, group `g8desk`) hold the configuration in the
   database (via `spatie/laravel-settings`), managed at **Admin → Settings → g8desk Support**:

   | Field | Type | Notes |
   |---|---|---|
   | `enabled` | `bool` | Master switch — nothing renders unless this is on. |
   | `base_url` | `string` | g8desk base URL (e.g. `https://g8desk.com`). The script loads from `{base_url}/intake/widget.js`. |
   | `public_key` | `string` | The `pk_…` data-key from g8desk → channel → Widget → Generate embed. |
   | `widget_secret` | `string` | The `g8wi_…` identity secret. **Stored encrypted**, never sent to the browser. |

2. **The widget component** (`<x-g8desk-support-widget />`) resolves the settings and renders
   **nothing** unless the widget is enabled, both keys are set, and a user is signed in. When
   it does render, it signs the user's identity server-side and emits two `<script>` tags:

   ```blade
   <script>window.g8deskSettings = { ref: …, name: …, email: …, exp: …, sig: … };</script>
   <script src="{base_url}/intake/widget.js" data-key="{public_key}" async></script>
   ```

3. **Identity signature.** The payload is signed with HMAC-SHA256 using the widget secret so
   g8desk can trust who the user is. The canonical signing string is:

   ```text
   ref|email|name|exp
   ```

   `ref` is the user's `uuid` (falling back to the primary key), `exp` is `time() + 300`
   (a 5-minute validity window). **The order — `email` before `name` — is canonical; do not
   reorder it**, or signature verification on the g8desk side will fail.

## Where it's wired in

The component is included once, just before `</body>` in the authenticated app layout
(`resources/views/components/layouts/app/sidebar.blade.php`), so it appears on every
signed-in page automatically:

```blade
<x-g8desk-support-widget />
```

Because the widget guards on `auth()->check()` and the enabled/keys checks internally, it is
safe to leave included even when g8desk is not configured — it simply renders nothing.

## Enabling it

1. In g8desk, open your channel → **Widget → Generate embed** and copy the `pk_…` public key
   and `g8wi_…` widget secret.
2. Go to **Admin → Settings → g8desk Support** (requires the `manage.settings` permission).
3. Toggle **Enable**, confirm the base URL, paste the public key and widget secret, and save.

That's it — no build step, no SDK install.
