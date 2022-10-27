<?php

declare(strict_types=1);

namespace sockets;

final class ErrorResponse
{
    public function __construct(
        public int $code,
        public string $message = 'error',
        public string $type = 'error',
    ) {
    }

    public static function exit(int $code, string $message): self
    {
        return new self($code, $message, 'exit');
    }

    public static function error(int $code, string $message = 'error'): self
    {
        return new self($code, $message);
    }

    public static function custom(int $code, string $message, string $type): self
    {
        return new self($code, $message, $type);
    }
}
