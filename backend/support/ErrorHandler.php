<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

namespace support;

use Illuminate\Database\RecordsNotFoundException;
use support\auth\JwtTokenException;
use support\bootstrap\Log;
use Throwable;
use Webman\Exception\ExceptionHandlerInterface;
use Webman\Http\Request;
use Webman\Http\Response;

final class ErrorHandler implements ExceptionHandlerInterface
{
    /** @var array<string> */
    private array $dontReport = [JwtTokenException::class];

    public function report(Throwable $exception): void
    {
        if (in_array($exception::class, $this->dontReport)) {
            return;
        }

        $level = match (true) {
            $exception instanceof \InvalidArgumentException => 'debug',
            default => 'error',
        };

        $message = nonEmpty($exception->getMessage(), class_basename($exception));

        Log::log($level, $message, exceptionContext($exception) + ['exception' => (string)$exception]);
    }

    public function render(Request $request, Throwable $exception): Response
    {
        $code = 0 === toInt($exception->getCode()) ? 1 : toInt($exception->getCode());
        $message = nonEmpty($exception->getMessage(), class_basename($exception));
        $httpCode = match (true) {
            $exception instanceof JwtTokenException => 401,
            $exception instanceof RecordsNotFoundException => 404,
            $exception instanceof \InvalidArgumentException => 422,
            default => 500,
        };

        return httpError($httpCode, $code, $message);
    }
}
