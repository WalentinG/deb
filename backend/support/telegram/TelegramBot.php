<?php

declare(strict_types=1);

namespace support\telegram;

use Psr\Http\Message\ResponseInterface;
use support\bootstrap\Log;
use Workerman\Http\Client;

final class TelegramBot
{
    private const BOT_API = 'https://api.telegram.org/bot';

    public function __construct(private Client $http, private TelegramCredentials $cred, private string $channel)
    {
    }

    public function sendMessage(string $message): void
    {
        $this->http->post(
            url: self::BOT_API . $this->cred->token . '/SendMessage',
            data: ['text' => $message, 'chat_id' => $this->channel],
            /* @phpstan-ignore-next-line */
            success_callback: function (ResponseInterface $r): void {
                if (200 !== $r->getStatusCode()) {
                    Log::error("Sending message to TelegramClient failed: Invalid status code {$r->getStatusCode()}");

                    return;
                }
                $body = $r->getBody()->getContents();
                $content = json_decode($body, true);

                if (\is_array($content) && ($content['ok'] ?? false) === false) {
                    Log::error("Sending message to TelegramClient failed: Invalid status code {$r->getStatusCode()}");
                }
            },
            /* @phpstan-ignore-next-line */
            error_callback: fn (\Throwable $e) => Log::error("Sending message to TelegramClient failed: {$e->getMessage()}")
        );
    }
}
