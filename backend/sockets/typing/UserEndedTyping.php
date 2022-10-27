<?php

namespace sockets\typing;

final class UserEndedTyping
{
    public function __construct(
        public readonly int $userId,
        public readonly int $chatId,
        public readonly string $type = 'UserEndedTyping',
    ) {
    }
}