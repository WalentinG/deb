<?php

declare(strict_types=1);

namespace tests\unit\support\http;

use PHPUnit\Framework\TestCase;
use support\http\ClientIpAddress;

/**
 * @internal
 * @covers \support\http\ClientIpAddress
 */
final class ClientIpAddressTest extends TestCase
{
    /**
     * @dataProvider data
     *
     * @param array<string> $trustedProxies
     */
    public function testFrom(string $expected, string $remoteIp, string $xForwardedFor, array $trustedProxies): void
    {
        $clientIp = new ClientIpAddress('X-Forwarded-For', $trustedProxies);

        $ipAddress = $clientIp->from(fn () => $remoteIp, fn () => $xForwardedFor);

        TestCase::assertEquals($expected, $ipAddress?->toString());
    }

    /** @phpstan-ignore-next-line  */
    public function data(): array
    {
        return [
            [
                'clientIp' => '1.1.1.1',
                'remoteIp' => '1.1.1.1',
                'xForwardedFor' => '',
                'trustedProxies' => [],
            ],
            [
                'clientIp' => '1.1.1.1',
                'remoteIp' => '2.2.2.2',
                'xForwardedFor' => '1.1.1.1',
                'trustedProxies' => ['2.2.2.2'],
            ],
            [
                'clientIp' => '1.1.1.1',
                'remoteIp' => '3.3.3.3',
                'xForwardedFor' => '1.1.1.1, 2.2.2.2',
                'trustedProxies' => ['2.2.2.2', '3.3.3.3'],
            ],
            [
                'clientIp' => '1.1.1.1',
                'remoteIp' => '1.1.1.1',
                'xForwardedFor' => '',
                'trustedProxies' => ['0.0.0.0/0'],
            ],
            [
                'clientIp' => '1.1.1.1',
                'remoteIp' => '3.3.3.3',
                'xForwardedFor' => '1.1.1.1, 2.2.2.2',
                'trustedProxies' => ['0.0.0.0/0'],
            ],
        ];
    }
}
