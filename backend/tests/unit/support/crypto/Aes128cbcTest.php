<?php

declare(strict_types=1);

namespace tests\unit\support\crypto;

use PHPUnit\Framework\TestCase;
use support\crypto\Aes128cbc;

/**
 * @internal
 * @covers \support\crypto\Aes128cbc
 */
final class Aes128cbcTest extends TestCase
{
    public function testDecrypt(): void
    {
        $encrypted = '4L0HHMyx728WLb9kOmYL2uzpIuwgqj6EeRwlJDDIcxNbkSbCh1/xZs+xCLbzLF/Z/KhFPUUeAwHRTGCupPVTm
    Tf3tdQS3An1J0eT0JqKh8qB9GDxnqufQb0m6Wpf0ppZNp+YX7vYPwpB4v/Z0uYM3UU8eO5MCwxX+4/oKrmDYxmeoC7pSBbnm3X8vMXy
    iYoNT0bjCT1YQZa628fL5dFZQA==';

        $decrypted = (new Aes128cbc(passphrase: 'xW.uc8LUi.x7@k!p', iv: 'Nz_zu4*xT8-8Z4ve'))->decrypt($encrypted);

        TestCase::assertEquals(
            json_encode([
                'dist' => 0,
                'emulator' => 0,
                'issued' => 'pusher',
                'lob' => 0,
                'nonce' => '4aq871z9',
                'plat' => 'android',
                'pver' => '29',
                'rid' => 0,
                'uuid' => '66167bc84c70dde1',
                'ver' => '3.10.1',
            ]),
            $decrypted
        );
    }
}
