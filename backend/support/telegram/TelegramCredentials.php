<?php

declare(strict_types=1);

namespace support\telegram;

/**
 * TelegramBot api token.
 *
 * @see https://core.telegram.org/bots/api#authorizing-your-bot
 */
final class TelegramCredentials
{
    public function __construct(public string $token)
    {
        if ('' === $token) {
            throw new \InvalidArgumentException('API token can\'t be empty');
        }

        if (false === (bool)preg_match('/(\d+):[\w\-]+/', $token)) {
            throw new \InvalidArgumentException('Invalid bot api token (via regular expression "/(\d+)\:[\w\-]+/")');
        }
    }
}
