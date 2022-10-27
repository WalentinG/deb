<?php

declare(strict_types=1);

namespace sockets\typing;

final class UserStartedTyping
{
    public function __construct(
        public readonly int $userId,
        public readonly int $chatId,
        public readonly string $type = 'UserStartedTyping',
    ) {
    }
}
