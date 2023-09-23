<?php

return [
    'fedex' => [
        'base_url' => env('FEDEX_BASE_URL', 'https://api.fedex.com/'),
        'auth_key' => env('FEDEX_AUTH_KEY', 'default_auth_key')
    ],

];
