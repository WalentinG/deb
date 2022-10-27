<?php

declare(strict_types=1);

namespace support\crypto;

final class Aes128cbc
{
    private const CIPHER_ALGO = 'aes-128-cbc';

    public function __construct(private string $passphrase, private string $iv)
    {
    }

    public function decrypt(string $content): string
    {
        $decrypted = openssl_decrypt($content, self::CIPHER_ALGO, $this->passphrase, 0, $this->iv);
        if (false === $decrypted) {
            throw ButterException::opensslDecryptionFailed();
        }

        return $decrypted;
    }

    public function encrypt(string $data): string
    {
        return toStr(openssl_encrypt($data, self::CIPHER_ALGO, $this->passphrase, 0, $this->iv));
    }
}
