<?php

declare(strict_types=1);

namespace support\http;

use support\Request;

final class ClientIpAddress
{
    /** @param array<string> $trustedProxies */
    public function __construct(private string $trustedHeader, private array $trustedProxies)
    {
    }

    public function fromRequest(Request $request): ?IpAddress
    {
        return $this->from([$request, 'getRemoteIp'], [$request, 'header']);
    }

    /**
     * @param callable(): string      $remoteIp
     * @param callable(string): mixed $header
     */
    public function from(callable $remoteIp, callable $header): ?IpAddress
    {
        try {
            $ip = IpAddress::fromString($remoteIp());
        } catch (\InvalidArgumentException) {
            return null;
        }

        if (!$ip->containedIn(...$this->trustedProxies)) {
            return $ip;
        }

        $clientIpsString = $header($this->trustedHeader);
        if (!\is_string($clientIpsString)) {
            return $ip;
        }

        $trusted = [];
        foreach (explode(',', $clientIpsString) as $clientIp) {
            try {
                $clientIp = IpAddress::withoutPort($clientIp);
                if (!$clientIp->containedIn(...$this->trustedProxies)) {
                    return $clientIp;
                }
                $trusted[] = $clientIp;
            } catch (\InvalidArgumentException) {
            }
        }

        return $trusted[0] ?? $ip;
    }
}
