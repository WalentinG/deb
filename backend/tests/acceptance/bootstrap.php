<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use support\bootstrap\db\Laravel;
use support\bootstrap\Redis;
use Webman\Config;

require_once __DIR__ . '/../../vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

if (method_exists('Dotenv\Dotenv', 'createUnsafeMutable')) {
    Dotenv::createUnsafeMutable(base_path())->load();
} else {
    Dotenv::createMutable(base_path())->load();
}

Config::load(config_path(), ['route']);

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

$files = config('autoload.files', []);

foreach (is_array($files) ? $files : [] as $file) {
    include_once $file;
}

Redis::start(null);
Laravel::start(null);
