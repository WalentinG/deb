<?php

declare(strict_types=1);

namespace tests\unit\support\http;

use PHPUnit\Framework\TestCase;
use support\http\IpAddressV6;

/**
 * @internal
 * @covers \support\http\IpAddressV6
 */
final class IpAddressV6Test extends TestCase
{
    /**
     * @dataProvider containedInData
     *
     * @param array<string> $list
     */
    public function testContainedIn(bool $expected, string $ipv6, array $list): void
    {
        $ipAddress = new IpAddressV6($ipv6);

        $containedIn = $ipAddress->containedIn(...$list);

        TestCase::assertSame($expected, $containedIn);
    }

    /** @phpstan-ignore-next-line  */
    public function containedInData(): array
    {
        return [
            [true, '2a01:198:603:0:396e:4789:8e99:890f', ['2a01:198:603:0::/65']],
            [false, '2a00:198:603:0:396e:4789:8e99:890f', ['2a01:198:603:0::/65']],
            [false, '2a01:198:603:0:396e:4789:8e99:890f', ['::1']],
            [true, '0:0:0:0:0:0:0:1', ['::1']],
            [false, '0:0:603:0:396e:4789:8e99:0001', ['::1']],
            [true, '0:0:603:0:396e:4789:8e99:0001', ['::/0']],
            [true, '0:0:603:0:396e:4789:8e99:0001', ['2a01:198:603:0::/0']],
            [true, '2a01:198:603:0:396e:4789:8e99:890f', ['::1', '2a01:198:603:0::/65']],
            [true, '2a01:198:603:0:396e:4789:8e99:890f', ['2a01:198:603:0::/65', '::1']],
            [false, '2a01:198:603:0:396e:4789:8e99:890f', ['::1', '1a01:198:603:0::/65']],
            [false, '2a01:198:603:0:396e:4789:8e99:890f', ['unknown']],
        ];
    }

    public function testWithoutPort(): void
    {
        $ipAddress = '[1fff:0:a88:85a3::ac1f]:8001';

        $withoutPort = IpAddressV6::withoutPort($ipAddress);

        TestCase::assertSame('1fff:0:a88:85a3::ac1f', $withoutPort->toString());
    }

    public function testWithoutPortNoPortDefined(): void
    {
        $ipAddress = '1fff:0:a88:85a3::ac1f';

        $withoutPort = IpAddressV6::withoutPort($ipAddress);

        TestCase::assertSame($ipAddress, $withoutPort->toString());
    }
}
