<?php

return [

    'defaults' => [
        'api_code' => '312fd13a36dc4f07be38054a0e46f373',
        'api_secret' => '79bcefc87cd5413285e3fd00b1fe8a8246f59f0178bcb49564acce415355a15253610535776157531156205111460850420f3ed0f84511ecac7bdf036ed4afb9',
        'api_url' => 'https://papi.bolue.cn/openApi/getOpenSsoToken',
        'sso_login_url' => 'https://www.bolue.cn/uums/openSsoLogin',
        'default_jump_url' => 'https://www.bolue.cn/products/9015',
    ],

    'api_code' => env('BOLUE_API_CODE'),

    'api_secret' => env('BOLUE_API_SECRET'),

    'api_url' => env('BOLUE_API_URL'),

    'sso_login_url' => env('BOLUE_SSO_LOGIN_URL'),

    'default_jump_url' => env('BOLUE_DEFAULT_JUMP_URL'),

    'allowed_hosts' => [
        'www.bolue.cn',
        'bolue.cn',
    ],

    'targets' => [
        'course-pack' => env('BOLUE_DEFAULT_JUMP_URL'),
    ],

    'http_timeout' => (int) env('BOLUE_HTTP_TIMEOUT', 30),

    'http_retries' => (int) env('BOLUE_HTTP_RETRIES', 3),

    'http_retry_delay_ms' => (int) env('BOLUE_HTTP_RETRY_DELAY_MS', 1000),

];
