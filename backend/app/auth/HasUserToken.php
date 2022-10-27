<?php

declare(strict_types=1);

namespace app\auth;

use app\user\UserId;
use support\auth\JwtTokenException;

trait HasUserToken
{
    private ?UserToken $token = null;
    private ?string $tokenStr = null;

    public function withToken(UserToken $token, string $tokenStr): self
    {
        $this->token = $token;
        $this->tokenStr = $tokenStr;

        return $this;
    }

    public function tokenStr(): string
    {
        return $this->tokenStr ?? throw JwtTokenException::unstated();
    }

    public function sub(): int
    {
        return (int)($this->token?->sub ?? throw JwtTokenException::unstated());
    }

    public function userId(): UserId
    {
        return $this->token?->userId() ?? throw JwtTokenException::unstated();
    }
}
