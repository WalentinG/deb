<?php

declare(strict_types=1);

global $argv;

$log['default'] = [
    'handlers' => [
        [
            'class' => Monolog\Handler\StreamHandler::class,
            'constructor' => [
                !in_array('-d', $argv, true) && (in_array('start', $argv, true))
                    ? STDERR : runtime_path() . '/logs/webman.log',
                match (toStr(env('LOG_LEVEL', 'DEBUG'))) {
                    'INFO' => Monolog\Logger::INFO,
                    'ERROR' => Monolog\Logger::ERROR,
                    default => Monolog\Logger::DEBUG
                },
            ],
            'formatter' => [
                'class' => Monolog\Formatter\JsonFormatter::class,
                'constructor' => [],
            ],
        ],
    ],
];

if (env('TELEGRAM_ALARM_BOT') && env('TELEGRAM_ALARM_CHANNEL')) {
    $log['telegram'] = [
        'handlers' => [
            [
                'class' => Monolog\Handler\TelegramBotHandler::class,
                'constructor' => [
                    env('TELEGRAM_ALARM_BOT'),
                    env('TELEGRAM_ALARM_CHANNEL'),
                    Monolog\Logger::INFO,
                ],
                'formatter' => [
                    'class' => Monolog\Formatter\LineFormatter::class,
                    'constructor' => [],
                ],
            ],
        ],
    ];
}

return $log;
