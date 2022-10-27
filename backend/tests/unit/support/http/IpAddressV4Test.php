<?php

declare(strict_types=1);

namespace tests\unit\support\http;

use PHPUnit\Framework\TestCase;
use support\http\IpAddressV4;

/**
 * @internal
 * @covers \support\http\IpAddressV4
 */
final class IpAddressV4Test extends TestCase
{
    /**
     * @dataProvider containedInData
     *
     * @param array<string> $list
     */
    public function testContainedIn(bool $expected, string $ipv4, array $list): void
    {
        $ipAddress = new IpAddressV4($ipv4);

        $containedIn = $ipAddress->containedIn(...$list);

        TestCase::assertSame($expected, $containedIn);
    }

    /** @phpstan-ignore-next-line  */
    public function containedInData(): array
    {
        return [
            [true, '192.168.1.1', ['192.168.1.1']],
            [true, '192.168.1.1', ['192.168.1.1/1']],
            [true, '192.168.1.1', ['192.168.1.0/24']],
            [false, '192.168.1.1', ['1.2.3.4/1']],
            [false, '192.168.1.1', ['192.168.1.1/33']], // invalid subnet
            [true, '192.168.1.1', ['1.2.3.4/1', '192.168.1.0/24']],
            [true, '192.168.1.1', ['192.168.1.0/24', '1.2.3.4/1']],
            [false, '192.168.1.1', ['1.2.3.4/1', '4.3.2.1/1']],
            [true, '1.2.3.4', ['0.0.0.0/0']],
            [true, '1.2.3.4', ['192.168.1.0/0']],
            [false, '1.2.3.4', ['256.256.256/0']], // invalid CIDR notation
        ];
    }

    public function testWithoutPort(): void
    {
        $ipAddress = '192.168.1.1:8001';

        $withoutPort = IpAddressV4::withoutPort($ipAddress);

        TestCase::assertSame('192.168.1.1', $withoutPort->toString());
    }

    public function testWithoutPortNoDefinedPort(): void
    {
        $ipAddress = '192.168.1.1';

        $withoutPort = IpAddressV4::withoutPort($ipAddress);

        TestCase::assertSame($ipAddress, $withoutPort->toString());
    }
}
