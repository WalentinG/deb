<?php

declare(strict_types=1);

namespace sockets\auth;

use app\user\UserId;

final class SocketSession
{
    public function __construct(
        public UserId $userId,
        public string $deviceId,
        public int $time,
    ) {
    }
}
