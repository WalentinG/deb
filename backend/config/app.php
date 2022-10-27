<?php

declare(strict_types=1);

return [
    'host_static' => env('HOST_STATIC'),
    'web_host' => env('HOST_WEB'),
    'socket_host' => env('HOST_WEBSOCKET'),
    'customerContactUrl' => env('CONTACT_URL'),
    'customerContactUrlHx' => env('CONTACT_URL_HX', ''),
    'private_js_cdn' => env('HOST_PRIVATE_JS_CDN', ''),
    'public_js_cdn' => env('HOST_PUBLIC_JS_CDN', ''),
    'debug' => env('APP_DEBUG', false),
    'default_timezone' => 'Asia/Shanghai',
    'game_chatwoot_hmac_key' => env('GAME_CHATWOOT_HMAC_KEY', ''),
    'error_reporting' => E_ALL & ~E_DEPRECATED & ~E_STRICT,
    'request_class' => \support\Request::class,
];
