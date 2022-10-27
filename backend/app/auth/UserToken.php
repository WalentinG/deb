<?php

declare(strict_types=1);

namespace app\auth;

use app\user\UserId;
use support\auth\Token;

final class UserToken extends Token
{
    public function __construct(public string $sub, public int $iat, public int $exp)
    {
    }

    public static function issue(string $sub): self
    {
        return new self($sub, $iat = time(), $iat + self::DEFAULT_TTL);
    }

    public function userId(): UserId
    {
        return UserId::from($this->sub);
    }
}
