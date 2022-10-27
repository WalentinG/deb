<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

\Webman\Config::load(config_path(), [
    'app', 'bootstrap', 'container', 'database', 'dependence', 'redis', 'route', 'view',
    'exception', 'log', 'middleaware', 'pika', 'process', 'server', 'session', 'static', 'translation',
]);

$files = config('autoload.files', []);

foreach (is_array($files) ? $files : [] as $file) {
    include_once $file;
}
