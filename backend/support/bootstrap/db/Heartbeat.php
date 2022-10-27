<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

namespace support\bootstrap\db;

use support\Db;
use Webman\Bootstrap;

/**
 * mysql心跳。定时发送一个查询，防止mysql连接长时间不活跃被mysql服务端断开。
 * 默认不开启，如需开启请到 config/bootstrap.php中添加 support\bootstrap\db\Heartbeat::class,.
 */
class Heartbeat implements Bootstrap
{
    /**
     * @param \Workerman\Worker $worker
     */
    public static function start($worker): void
    {
        \Workerman\Timer::add(55, function (): void {
            Db::select('select 1 limit 1');
        });
    }
}
