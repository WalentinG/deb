<?php

declare(strict_types=1);

namespace support\socket;

interface Sockets
{
    public function toUser(int $id, object $message): void;

    public function toGroup(int $id, object $message): void;

    public function toAll(object $message): void;

    /** @param array<string> $groups */
    public function addToGroups(int $userId, array $groups): void;

    /** @param array<string, mixed> $session */
    public function updateSession(int $id, array $session): void;
}
