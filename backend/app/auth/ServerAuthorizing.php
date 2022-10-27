<?php

declare(strict_types=1);

namespace app\auth;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

use function support\auth\bearer;

final class ServerAuthorizing implements MiddlewareInterface
{
    public function __construct(private readonly ServerToken $token)
    {
    }

    public function process(Request $request, callable $handler): Response
    {
        if (!$request instanceof \support\Request) {
            return $handler($request);
        }

        if (\is_string($auth = $request->header('Authorization'))) {
            if ($this->token->value === bearer($auth)) {
                return $handler($request);
            }
        }

        throw new \Exception('Unauthorized', 401);
    }
}
