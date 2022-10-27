<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

namespace support;

use app\auth\HasUserToken;
use Respect\Validation\Validator as v;
use support\bootstrap\Log;
use support\crypto\HasButter;
use support\http\HasClientIp;
use support\http\IpAddress;
use support\image\Image;
use Webman\Http\UploadFile;

final class Request extends \Webman\Http\Request
{
    use HasButter;
    use HasClientIp;
    use HasUserToken;

    private ?IpAddress $serverIp = null;
    private int $lob = 0;

    public function serverIp(): IpAddress
    {
        return $this->serverIp ?? $this->serverIp = IpAddress::fromString($this->getLocalIp());
    }

    /**
     * @template T of object
     *
     * @param class-string<T>      $class
     * @param array<string, mixed> $extra
     *
     * @return T
     */
    public function unmarshal(string $class, array $extra = []): object
    {
        return unmarshal($class, snakeToCamel(toArr($this->post()) + $extra));
    }

    public function int(string $name, int $default = 0): int
    {
        return toInt($this->input($name), $default);
    }

    public function bool(string $name): bool
    {
        return (bool)$this->input($name);
    }

    public function str(string $name, string $default = ''): string
    {
        return toStr($this->input($name), $default);
    }

    // 业务平台
    // 对于观众端，使用客户端传入的标识，因为观众允许跨业务平台登录
    // 对于主播端，使用主播签约的平台标识
    public function lob(): int
    {
        if ($this->lob > 0) {
            return $this->lob;
        }

        $this->lob = $this->butter?->lob ?? 1;

        if ($this->lob > 0) {
            return $this->lob;
        }

        if (0 === $this->butter?->lob) {
            Log::debug('Butter has lob with value 0', ['butter' => marshal($this->butter)]);
        }

        return 1;
    }

    public function withLob2(): void
    {
        $this->lob = 2;
    }

    public function anchorIssued(): bool
    {
        return 0 === $this->butter?->lob;
    }

    /**
     * @throws \RuntimeException
     */
    public function image(string $name = null): Image
    {
        $file = parent::file($name);
        if ($file instanceof UploadFile) {
            return new Image($file);
        }

        v::instance(UploadFile::class)->check($file);
        throw new \RuntimeException('');
    }
}
