<?php

return [
    'users' => [
        'superadmin' => [
            'name' => env('SUPERADMIN_NAME', 'Superadmin'),
            'email' => env('SUPERADMIN_EMAIL', 'admin@example.com'),
            'password' => env('SUPERADMIN_PASSWORD', 'password'),
        ],
    ],
];
