<?php

declare(strict_types=1);

return [
    'default' => [
        'host' => env('REDIS_HOST'),
        'password' => env('REDIS_PASSWORD'),
        'port' => 6379,
        'database' => 0,
    ],
];
