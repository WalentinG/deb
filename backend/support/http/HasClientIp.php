<?php

declare(strict_types=1);

namespace support\http;

trait HasClientIp
{
    private ?IpAddress $clientIp = null;

    public function withClientIp(?IpAddress $clientIp): self
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    public function clientIp(): ?IpAddress
    {
        return $this->clientIp;
    }
}
