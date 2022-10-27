<?php

declare(strict_types=1);

namespace sockets\auth;

final class Login
{
    public function __construct(
        public string $clientId,
        public string $token,
        public string $deviceName,
        public string $deviceId,
    ) {
    }
}
