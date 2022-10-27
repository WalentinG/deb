<?php

declare(strict_types=1);

return [
    'monitor' => [
        'handler' => support\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            'monitor_dir' => [
                app_path(),
                config_path(),
                base_path() . '/support',
                base_path() . '/.env',
            ],
            'monitor_extensions' => ['php', 'env',],
        ],
    ],
];
