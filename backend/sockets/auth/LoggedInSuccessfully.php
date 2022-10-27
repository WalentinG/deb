<?php

declare(strict_types=1);

namespace sockets\auth;

final class LoggedInSuccessfully
{
    public function __construct(
        public readonly string $type = 'LoggedInSuccessfully',
        public readonly string $state = 'outdated' // outdated, fresh
    ) {
    }
}
