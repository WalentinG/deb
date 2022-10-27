<?php

declare(strict_types=1);

namespace sockets\auth\events;

use app\user\UserId;

final class LoggedIn
{
    public function __construct(public readonly UserId $userId)
    {
    }
}
