{{--
    Native, SDK-free g8desk support widget.

    Resolves the DB-stored G8DeskSettings and embeds the g8desk intake widget for
    authenticated users only. The identity payload is HMAC-signed server-side with
    the widget secret (which never reaches the browser); g8desk verifies the
    signature to trust who the user is. Renders NOTHING unless the widget is
    enabled, both keys are set, and someone is signed in.

    Canonical signing string: ref|email|name|exp  (email BEFORE name — do not reorder).

    rescue(): if the settings row isn't seeded yet (fresh install, migrations
    pending) resolving the Settings class throws — swallow it and render nothing,
    mirroring AppServiceProvider::applyDatabaseSettings().
--}}
@php
    $s = rescue(fn () => app(App\Settings\G8DeskSettings::class), null, false);
@endphp

@if ($s && $s->enabled && $s->public_key !== '' && $s->widget_secret !== '' && auth()->check())
    @php
        $u = auth()->user();
        $ref = (string) ($u->uuid ?? $u->getKey());
        $name = (string) $u->name;
        $email = (string) $u->email;
        $exp = time() + 300;
        $sig = hash_hmac('sha256', $ref.'|'.$email.'|'.$name.'|'.$exp, $s->widget_secret);
    @endphp

    <script>window.g8deskSettings = { ref: @js($ref), name: @js($name), email: @js($email), exp: {{ $exp }}, sig: @js($sig) };</script>
    <script src="{{ rtrim($s->base_url, '/') }}/intake/widget.js" data-key="{{ $s->public_key }}" async></script>
@endif
