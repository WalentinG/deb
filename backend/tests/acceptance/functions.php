<?php

declare(strict_types=1);

namespace tests\acceptance;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Utils;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

function assertResponseFailed(ResponseInterface $response, int $code, int $httpStatusCode): void
{
    $response->getBody()->rewind();
    $contents = $response->getBody()->getContents();
    /** @var \stdClass $decoded */
    $decoded = json_decode($contents);
    TestCase::assertTrue($httpStatusCode === $response->getStatusCode());
    TestCase::assertSame($code, $decoded->code, "Response: {$contents}");
}

function assertResponseSucceed(ResponseInterface $response): void
{
    $response->getBody()->rewind();
    $contents = $response->getBody()->getContents();
    /** @var \stdClass $decoded */
    $decoded = json_decode($contents);
    TestCase::assertTrue(200 === $response->getStatusCode(), "Response: {$contents}");
    TestCase::assertSame(0, $decoded->code, "Response: {$contents}");
}

/**
 * @param array<int|string, mixed> $data
 */
function assertResponseData(ResponseInterface $response, array $data): void
{
    $response->getBody()->rewind();

    /** @var array<string, mixed> $decoded */
    $decoded = json_decode($response->getBody()->getContents(), true);

    /** @var array<string, mixed> $responseData */
    $responseData = $decoded['data'] ?? [];
    assertIsSubset($data, $responseData);
}

/**
 * @param array<mixed> $subset
 * @param array<mixed> $array
 */
function assertIsSubset(array $subset, array $array): void
{
    foreach ($subset as $key => $value) {
        TestCase::assertArrayHasKey($key, $array);
        if (is_array($value)) {
            assertIsSubset($value, $array[$key]);
            continue;
        }
        TestCase::assertEquals($value, $array[$key] ?? null, "{$key}");
    }
}

function assertResponseString(ResponseInterface $response, string $expected): void
{
    $response->getBody()->rewind();

    /** @var array<string, mixed> $decoded */
    $decoded = json_decode($response->getBody()->getContents(), true);

    TestCase::assertEquals($expected, $decoded['data'] ?? '');
}

/**
 * @param array<string|array<string>> $keys
 */
function assertResponseKeys(ResponseInterface $response, array $keys): void
{
    $response->getBody()->rewind();

    /** @var array<string, mixed> $decoded */
    $decoded = json_decode($response->getBody()->getContents(), true);

    /** @var array<string, mixed> $responseData */
    $responseData = $decoded['data'] ?? [];
    foreach ($keys as $k => $value) {
        if (\is_array($value)) {
            $data = toArr($responseData[$k]);
            foreach ($value as $item) {
                TestCase::assertArrayHasKey($item, $data);
            }
        } else {
            TestCase::assertArrayHasKey($value, $responseData);
        }
    }
}

function assertResponseDataCount(ResponseInterface $response, int $count): void
{
    $response->getBody()->rewind();

    /** @var array<string, mixed> $decoded */
    $decoded = json_decode($response->getBody()->getContents(), true);

    /** @var array<string, mixed> $responseData */
    $responseData = $decoded['data'] ?? [];

    TestCase::assertCount($count, $responseData);
}

function responseData(ResponseInterface $response): mixed
{
    $response->getBody()->rewind();

    /** @var array<string, mixed> $decoded */
    $decoded = json_decode($response->getBody()->getContents(), true);

    return $decoded['data'] ?? null;
}

function assertResponsePaginated(ResponseInterface $response, int $count, int $page = 1, int $total = 1): void
{
    $response->getBody()->rewind();

    /** @var array<string, mixed> $decoded */
    $decoded = json_decode($response->getBody()->getContents(), true);

    /** @var array<string, mixed> $responseData */
    $responseData = $decoded['data'] ?? [];

    TestCase::assertArrayHasKey('list', $responseData);
    TestCase::assertCount($count, toArr($responseData['list'] ?? []));

    TestCase::assertArrayHasKey('page', $responseData);
    TestCase::assertEquals($page, $responseData['page'] ?? null);

    TestCase::assertArrayHasKey('total', $responseData);
    TestCase::assertEquals($total, $responseData['total'] ?? null);
}

/**
 * @param array<string, mixed> $body
 * @return array{0: ResponseInterface, 1: Request}
 */
function sendRequest(string $method, string $url, string $token, array $body = []): array
{
    $request = new Request(
        $method,
        getenv('API_HOST') . $url,
        ['Authorization' => "Bearer {$token}", 'Content-Type' => 'application/json'],
        Utils::jsonEncode($body)
    );
    try {
        $response = (new Client())->send($request);
    } catch (ClientException $e) {
        $response = $e->getResponse();
    }

    return [$response, $request];
}

/**
 * @return array{0: ResponseInterface, 1: Request}
 */
function sendGetRequest(string $url, string $token): array
{
    return sendRequest('GET', $url, $token);
}

/**
 * @param array<string, mixed> $body
 *
 * @return array{0: ResponseInterface, 1: Request}
 */
function sendPostRequest(string $url, string $token, array $body = []): array
{
    return sendRequest('POST', $url, $token, $body);
}
