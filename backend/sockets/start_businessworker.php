<?php

declare(strict_types=1);

namespace sockets;

/*
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 *
 * @see http://www.workerman.net/
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

use GatewayWorker\BusinessWorker;
use support\bootstrap\Redis;
use Webman\Config;
use Workerman\Worker;

// 自动加载类
require_once __DIR__ . '/../vendor/autoload.php';

// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = 'YourAppBusinessWorker';
// bussinessWorker进程数量
$worker->count = 1;
// 服务注册地址
$worker->registerAddress = '127.0.0.1:1238';
$worker->eventHandler = Entrypoint::class;
$worker->onWorkerStart = function (Worker $worker): void {
    Config::load(config_path(), ['route']);
    Redis::start($worker);
};
// 如果不是在根目录启动，则运行runAll方法
if (!\defined('GLOBAL_START')) {
    Worker::runAll();
}
