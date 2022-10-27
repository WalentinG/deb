<?php

declare(strict_types=1);

namespace tests\acceptance\stubs\support;

use support\bootstrap\Redis;
use support\socket\Sockets;

final class SocketsStub implements Sockets
{
    private const key = 'SOCKETS:STUB';

    /** @return array<mixed> */
    public static function event(int $id, string $type): array
    {
        $event = Redis::hGet(self::key, $id . ':' . $type);
        if (false === $event) {
            return [];
        }
        return toArr(json_decode($event, true));
    }

    public static function cleanUp(): void
    {
        Redis::del(self::key);
    }

    public function toUser(int $id, object $message): void
    {
        Redis::hSet(self::key, $id . ':' . ($message->type ?? ''), encode($message));
    }

    public function toGroup(int $id, object $message): void
    {
        // TODO: Implement toGroup() method.
    }

    public function toAll(object $message): void
    {
        // TODO: Implement toAll() method.
    }

    public function addToGroups(int $userId, array $groups): void
    {
        // TODO: Implement addToGroups() method.
    }

    public function updateSession(int $id, array $session): void
    {
        // TODO: Implement updateSession() method.
    }
}
