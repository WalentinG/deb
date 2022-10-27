<?php

declare(strict_types=1);

namespace tests\unit\support\http;

use PHPUnit\Framework\TestCase;
use support\http\IpAddress;
use support\http\IpAddressV4;
use support\http\IpAddressV6;

/**
 * @internal
 * @covers \support\http\IpAddress
 */
final class IpAddressTest extends TestCase
{
    public function testCreateIpv6(): void
    {
        $ipv6 = 'a01:198:603:0:396e:4789:8e99:890f';

        $ipAddress = IpAddress::fromString($ipv6);

        TestCase::assertInstanceOf(IpAddressV6::class, $ipAddress);
    }

    public function testCreateIpv4(): void
    {
        $ipv4 = '192.168.1.1';

        $ipAddress = IpAddress::fromString($ipv4);

        TestCase::assertInstanceOf(IpAddressV4::class, $ipAddress);
    }
}
