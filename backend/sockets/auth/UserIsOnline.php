<?php

declare(strict_types=1);

namespace sockets\auth;

final class UserIsOnline
{
    public function __construct(
        public int $userId,
        public string $type = 'UserIsOnline',
    ) {
    }
}
