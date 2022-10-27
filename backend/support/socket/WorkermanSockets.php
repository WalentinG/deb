<?php

declare(strict_types=1);

namespace support\socket;

use GatewayWorker\Lib\Gateway;

final class WorkermanSockets implements Sockets
{
    public function toUser(int $id, object $message): void
    {
        Gateway::sendToUid($id, encode($message));
    }

    public function toGroup(int $id, object $message): void
    {
        Gateway::sendToGroup($id, encode($message));
    }

    public function toAll(object $message): void
    {
        Gateway::sendToAll(encode($message));
    }

    public function addToGroups(int $userId, array $groups): void
    {
        $clientId = toStr(Gateway::getClientIdByUid(toStr($userId))[0] ?? '');
        foreach ($groups as $group) {
            Gateway::joinGroup($clientId, $group);
        }
    }

    public function updateSession(int $id, array $session): void
    {
        $clientId = Gateway::getClientIdByUid(toStr($id));
        if (!empty($clientId)) {
            Gateway::updateSession($clientId[0], $session);
        }
    }
}
