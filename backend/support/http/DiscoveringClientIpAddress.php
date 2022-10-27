<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

namespace support\http;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

final class DiscoveringClientIpAddress implements MiddlewareInterface
{
    public function __construct(private ClientIpAddress $ip)
    {
    }

    public function process(Request $request, callable $handler): Response
    {
        if (!$request instanceof \support\Request) {
            return $handler($request);
        }

        $request->withClientIp($this->ip->fromRequest($request));

        return $handler($request);
    }
}
