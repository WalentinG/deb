<?php

declare(strict_types=1);

namespace tests\acceptance;

use cebe\openapi\spec\Schema;
use GuzzleHttp\Psr7\Request;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use League\OpenAPIValidation\Schema\SchemaValidator;
use Psr\Http\Message\ResponseInterface;

final class OpenApi
{
    private static ?self $instance = null;

    public function __construct(private readonly ValidatorBuilder $v)
    {
    }

    public static function assertEndpoint(Request $request, ResponseInterface $response): void
    {
        self::make()->v->getRequestValidator()->validate($request);
        self::make()->v->getResponseValidator()->validate(new OperationAddress($request->getUri()->getPath(), strtolower($request->getMethod())), $response);
    }

    /** @param array<string, mixed> $message */
    public static function assertEvent(array $message, string $type): void
    {
        $openapi = self::make()->v->getRequestValidator()->getSchema();
        $schema = $openapi->components?->schemas[$type];
        if ($schema instanceof Schema) {
            (new SchemaValidator())->validate($message, $schema);
        }
    }

    private static function make(): self
    {
        return self::$instance ?? self::$instance = new self(
            (new ValidatorBuilder())->fromYamlFile(base_path() . '/api.yml')
        );
    }
}
