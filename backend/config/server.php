<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

return [
    'listen' => 'http://0.0.0.0:8989',
    'transport' => 'tcp',
    'reusePort' => true,
    'context' => [],
    'name' => 'deb',
    'count' => env('SERVER_PROCESS_COUNT', cpu_count() * 2),
    'user' => env('SERVER_PROCESS_USER', ''),
    'group' => env('SERVER_PROCESS_GROUP', ''),
    'pid_file' => runtime_path() . '/webman.pid',
    'max_request' => 1000000,
    'stdout_file' => runtime_path() . '/logs/stdout.log',
    'status_file' => runtime_path() . '/webman.status',
    'log_file' => runtime_path() . '/logs/workerman.log',
    'max_package_size' => 10 * 1024 * 1024,
];
