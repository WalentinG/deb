<?php

declare(strict_types=1);

namespace tests\unit\support\marshal;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class RetryTest extends TestCase
{
    public function testSuccess(): void
    {
        $fn = fn() => 1;

        $result = retryable($fn, 1);

        TestCase::assertEquals(1, $result);
    }

    public function testRetry(): void
    {
        $s = 0;
        $fn = function () use (&$s) {
            if ($s++ < 1) {
                throw new \InvalidArgumentException();
            }
        };

        retryable($fn, 2);

        TestCase::assertEquals(2, $s);
    }

    public function testCompensation(): void
    {
        $fn = fn() => throw new \InvalidArgumentException();
        $s = 0;
        $comp = function () use (&$s) {
            $s = 1;
        };
        try {
            compensate(fn () => retryable($fn, 1), $comp);
        } catch (\InvalidArgumentException) {}

        TestCase::assertEquals(1, $s);
    }

    public function testError(): void
    {
        TestCase::expectException(\InvalidArgumentException::class);

        $fn = fn() => throw new \InvalidArgumentException();

        retryable($fn, 1);
    }
}
