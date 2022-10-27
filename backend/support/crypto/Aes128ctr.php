<?php

declare(strict_types=1);

namespace support\crypto;

use function base64Safe;

final class Aes128ctr
{
    private const CIPHER_ALGO = 'aes-128-ctr';

    public static function encrypt(string $key, string $data): string
    {
        $iv = openssl_random_pseudo_bytes((int)(openssl_cipher_iv_length(self::CIPHER_ALGO)));
        $encrypted = openssl_encrypt($data, self::CIPHER_ALGO, toStr($key), OPENSSL_RAW_DATA, $iv);
        $ciphertext = base64Safe($encrypted . $iv);

        return base64Safe($ciphertext . $iv);
    }
}
