<?php

declare(strict_types=1);

namespace sockets;

final class BusinessException extends \RuntimeException
{
    public function __construct(public string $type, string $message = '', int $code = 0)
    {
        parent::__construct($message, $code);
    }

    public static function unknownMethod(): self
    {
        return new self('error', 'Unknown method');
    }
}
