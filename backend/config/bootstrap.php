<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

return [
    support\bootstrap\db\Laravel::class,
    \support\bootstrap\Log::class,
    \support\bootstrap\Redis::class,
    support\bootstrap\db\Heartbeat::class,
    \support\bootstrap\Cache::class,
    support\bootstrap\Subscriber::class,
];
