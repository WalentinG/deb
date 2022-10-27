<?php

declare(strict_types=1);

namespace support\crypto;

use function base64Safe;

final class Aes128gcm
{
    private const CIPHER_ALGO = 'aes-128-gcm';

    public static function encrypt(string $key, string $content): string
    {
        $tag_length = 16;
        $iv = openssl_random_pseudo_bytes((int)(openssl_cipher_iv_length(self::CIPHER_ALGO)));
        $tag = '';
        $ciphertext = openssl_encrypt($content, self::CIPHER_ALGO, $key, OPENSSL_RAW_DATA, $iv, $tag, '', $tag_length);

        return base64Safe($iv . $ciphertext . $tag);
    }
}
