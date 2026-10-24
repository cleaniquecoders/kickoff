<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Comma-separated IPs / CIDR ranges whose X-Forwarded-* headers are honoured,
    | or '*' to trust whichever proxy is calling.
    |
    | Local/testing default to '*' so `bin/share` tunnels (ngrok, cloudflared)
    | keep request()->isSecure() true and Livewire/Flux emit https:// URLs.
    | Everywhere else the default is loopback + private ranges: nginx on the same
    | box, Docker and a VPC load balancer work, but an internet client cannot spoof
    | X-Forwarded-For. Behind Cloudflare or another public CDN, list its ranges.
    |
    */

    'proxies' => env('TRUSTED_PROXIES', in_array(env('APP_ENV'), ['local', 'testing'], true)
        ? '*'
        : '127.0.0.1,::1,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16'),

];
