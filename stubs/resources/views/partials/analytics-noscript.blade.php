{{-- Google Tag Manager (noscript) — include right after <body> in document layouts. --}}
@if (config('seo.google.tag_manager_id'))
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('seo.google.tag_manager_id') }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif
