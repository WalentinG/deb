<?php

declare(strict_types=1);

namespace app\auth;

use support\auth\JwtTokenException;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

use function support\auth\bearer;

final class UserAuthorizing implements MiddlewareInterface
{
    public function __construct(private readonly DecodeUserToken $decodeUserToken)
    {
    }

    public function process(Request $request, callable $handler): Response
    {
        if (!$request instanceof \support\Request) {
            return $handler($request);
        }

        if (\is_string($auth = $request->header('Authorization'))) {
            return $this->authorized($request, $handler, bearer($auth));
        }

        if (\is_string($jwt = $request->get('token'))) {
            return $this->authorized($request, $handler, $jwt);
        }

        throw JwtTokenException::undefined();
    }

    /**
     * @param callable(Request): Response $handler
     */
    private function authorized(\support\Request $request, callable $handler, string $jwt): Response
    {
        return $handler($request->withToken($this->decodeUserToken->call($jwt), $jwt));
    }
}
