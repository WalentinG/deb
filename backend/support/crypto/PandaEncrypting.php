<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

namespace support\crypto;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

final class PandaEncrypting implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        if (!$request instanceof \support\Request) {
            return $handler($request);
        }
        $resp = $handler($request);

        $token = md5($request->tokenStr());
        $panda = new Aes128cbc(passphrase: substr($token, 0, 16), iv: substr($token, -16));

        return $resp->withHeader('X-Content-Puzzle', 'panda')->withBody($panda->encrypt($resp->rawBody()));
    }
}
