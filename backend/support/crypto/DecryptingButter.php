<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

namespace support\crypto;

use support\bootstrap\Log;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

final class DecryptingButter implements MiddlewareInterface
{
    private const X_LIVE_BUTTER = 'X-Live-Butter';
    private const X_LIVE_BUTTER2 = 'X-Live-Butter2';

    public function __construct(private Aes128cbc $tiger, private Aes128cbc $tiger2)
    {
    }

    public function process(Request $request, callable $handler): Response
    {
        if (!$request instanceof \support\Request) {
            return $handler($request);
        }
        try {
            if (\is_string($decoded = $request->header(self::X_LIVE_BUTTER))) {
                return $handler($request->withButter(Butter::decode($this->tiger->decrypt($decoded))));
            }

            if (\is_string($decoded2 = $request->header(self::X_LIVE_BUTTER2))) {
                return $handler($request->withButter(Butter::decode($this->tiger2->decrypt($decoded2))));
            }
        } catch (ButterException $butterException) {
            Log::debug($butterException->getMessage(), $butterException->getTrace());

            return $handler($request->withButter(Butter::empty()));
        }

        return $handler($request);
    }
}
