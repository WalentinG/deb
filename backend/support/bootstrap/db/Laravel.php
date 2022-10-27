<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

namespace support\bootstrap\db;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Webman\Bootstrap;
use Workerman\Worker;

/**
 * Class Laravel.
 */
class Laravel implements Bootstrap
{
    /**
     * @param Worker $worker
     */
    public static function start($worker): void
    {
        if (!class_exists('\Illuminate\Database\Capsule\Manager')) {
            return;
        }
        $capsule = new Capsule();
        $configs = config('database');

        $default_config = $configs['connections'][$configs['default']];
        $capsule->addConnection($default_config);

        foreach ($configs['connections'] as $name => $config) {
            $capsule->addConnection($config, $name);
        }

        if (class_exists('\Illuminate\Events\Dispatcher')) {
            $capsule->setEventDispatcher(new Dispatcher(new Container()));
        }

        $capsule->setAsGlobal();

        $capsule->bootEloquent();
    }
}
