<?php

declare(strict_types=1);

namespace sockets\auth;

final class UserIsOffline
{
    public function __construct(
        public int $userId,
        public string $lastSeen,
        public string $type = 'UserIsOffline',
    ) {
    }
}
