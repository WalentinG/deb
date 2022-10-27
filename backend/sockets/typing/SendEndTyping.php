<?php

declare(strict_types=1);

namespace sockets\typing;

use app\user\UserId;

final class SendEndTyping
{
    public function __construct(
        public readonly UserId $chatId,
        public string $clientId,
    ) {
    }
}
