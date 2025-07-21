<?php

return [
    'authentik' => [
        'base_url' => env('AUTHENTIK_BASE_URL', 'http://localhost:9000'),
        'client_id' => env('AUTHENTIK_CLIENT_ID'),
        'client_secret' => env('AUTHENTIK_CLIENT_SECRET'),
        'token_endpoint' => env('AUTHENTIK_TOKEN_ENDPOINT', '/application/o/token/'),
        'userinfo_endpoint' => env('AUTHENTIK_USERINFO_ENDPOINT', '/application/o/userinfo/'),
    ],
];
