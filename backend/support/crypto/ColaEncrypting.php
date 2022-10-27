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

final class ColaEncrypting implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        if (!$request instanceof \support\Request) {
            return $handler($request);
        }
        $resp = $handler($request);

        return $resp->withHeader('X-Content-Puzzle', 'cola');
    }
}
