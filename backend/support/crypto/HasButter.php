<?php

declare(strict_types=1);

namespace support\crypto;

use support\client\AppVersion;

trait HasButter
{
    private ?Butter $butter = null;
    private ?AppVersion $appVersion = null;

    public function withButter(Butter $butter): self
    {
        $this->butter = $butter;

        return $this;
    }

    public function appVersion(): AppVersion
    {
        return $this->appVersion ?? $this->appVersion = new AppVersion($this->butter?->ver ?? '');
    }

    public function butter(): Butter
    {
        if (null === $this->butter) {
            throw ButterException::notFoundInRequest();
        }

        return $this->butter;
    }

    public function releaseId(): int
    {
        return $this->butter?->rid ?? 1;
    }

    // 终端标识
    public function rid(): int
    {
        return $this->releaseId();
    }
}
