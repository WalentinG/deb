<?php

declare(strict_types=1);

namespace support\auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

abstract class Token
{
    public const DEFAULT_TTL = 30 * 24 * 60 * 60;
    private const RENEW_TTL = 3 * 24 * 60 * 60;

    public int $iat;
    public int $exp;

    final public static function decode(string $encoded, PublicKey $publicKey): static
    {
        try {
            $payload = JWT::decode(jwt: $encoded, keyOrKeyArray: new Key($publicKey->key, $publicKey->algorithm));
        } catch (\InvalidArgumentException|\UnexpectedValueException $e) {
            throw JwtTokenException::unexpected($e);
        }

        return unmarshal(static::class, (array)$payload);
    }

    final public function encode(PrivateKey $privateKey): string
    {
        return JWT::encode(payload: (array)$this, key: $privateKey->key, alg: $privateKey->algorithm);
    }

    final public function isExpiredSoon(int $now): bool
    {
        return $this->exp - $now < self::RENEW_TTL && $now - $this->iat > 5;
    }

    final public function renew(int $iat, int $exp, PrivateKey $privateKey): string
    {
        return JWT::encode(payload: ['iat' => $iat, 'exp' => $exp] + (array)$this, key: $privateKey->key, alg: $privateKey->algorithm);
    }

    final public function assertIsNotRevoked(int $storedIat): void
    {
        if (0 === $storedIat) {
            throw JwtTokenException::revoked();
        }
        if ($storedIat > time()) {
            throw JwtTokenException::blocked();
        }
        if ($this->iat < $storedIat && time() - $storedIat > 5) {
            throw JwtTokenException::revoked();
        }
    }
}
