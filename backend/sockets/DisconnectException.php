<?php

declare(strict_types=1);

namespace sockets;

final class DisconnectException extends \RuntimeException
{
    public static function invalidToken(): self
    {
        return new self('无效的token。', 4124);
    }

    public static function invalidDevice(): self
    {
        return new self('非法请求', 4145);
    }
}
