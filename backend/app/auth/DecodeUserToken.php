<?php

declare(strict_types=1);

namespace app\auth;

use support\auth\PublicKey;

final class DecodeUserToken
{
    public function __construct(private IssuedAtStore $store, private PublicKey $publicKey)
    {
    }

    public function call(string $jwt): UserToken
    {
        $token = UserToken::decode($jwt, $this->publicKey);
        $token->assertIsNotRevoked($this->store->find($token->sub));

        return $token;
    }
}
