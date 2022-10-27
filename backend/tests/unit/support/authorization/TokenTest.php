<?php

declare(strict_types=1);

namespace tests\unit\support\authorization;

use PHPUnit\Framework\TestCase;
use support\auth\JwtTokenException;
use support\auth\PrivateKey;
use support\auth\PublicKey;
use support\auth\Token;

/**
 * @internal
 * @coversNothing
 */
final class TokenTest extends TestCase
{
    public function testSuccess(): void
    {
        $token = new TokenStub('a', 'b', time(), time() + Token::DEFAULT_TTL);

        $encoded = $token->encode(new PrivateKey('key', 'HS256'));
        $decoded = TokenStub::decode($encoded, new PublicKey('key', 'HS256'));

        TestCase::assertEquals($token, $decoded);
    }

    public function testFailedDecode(): void
    {
        TestCase::expectException(JwtTokenException::class);

        $encoded = 'token';

        TokenStub::decode($encoded, new PublicKey('key', 'HS256'));
    }

    public function testIsExpiredSoon(): void
    {
        $token = new TokenStub('a', 'b', time(), time() + 100);

        $isExpiredSoon = $token->isExpiredSoon(time() + 10);

        TestCase::assertTrue($isExpiredSoon);
    }

    public function testIsNotExpiredSoon(): void
    {
        $token = new TokenStub('a', 'b', time(), time() + 3 * 24 * 60 * 60 + 11);

        $isExpiredSoon = $token->isExpiredSoon(time() + 10);

        TestCase::assertFalse($isExpiredSoon);
    }

    public function testRenew(): void
    {
        $token = new TokenStub('a', 'b', $t = time(), $t + 100);

        $renewed = $token->renew($t, $t + 200, new PrivateKey('key', 'HS256'));

        TestCase::assertEquals($t + 200, TokenStub::decode($renewed, new PublicKey('key', 'HS256'))->exp);
    }

    public function testIsNotRevoked(): void
    {
        $token = new TokenStub('a', 'b', time(), time() + 3 * 24 * 60 * 60 + 11);

        $token->assertIsNotRevoked(time());

        TestCase::assertTrue(true);
    }

    public function testIsRevoked(): void
    {
        $this->expectException(JwtTokenException::class);

        $token = new TokenStub('a', 'b', time(), time() + 3 * 24 * 60 * 60 + 11);

        $token->assertIsNotRevoked(time() + 100);
    }
}
