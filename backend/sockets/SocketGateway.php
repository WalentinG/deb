<?php

namespace sockets;

use GatewayWorker\Lib\Gateway;

final class SocketGateway
{
    /** @return string[] */
    public static function clientIdByUid(int $value): array
    {
        return Gateway::getClientIdByUid(toStr($value));
    }

    public static function closeClient(string $clientId): void
    {
        Gateway::closeClient($clientId);
    }

    public static function setSession(string $clientId, object $session): void
    {
        Gateway::setSession($clientId, marshal($session));
    }

    public static function bindUid(string $clientId, int $uid): void
    {
        Gateway::bindUid($clientId, $uid);
    }

    public static function toGroup(string|int $group, object $message): void
    {
        Gateway::sendToGroup($group, encode($message));
    }

    public static function toClient(string $clientId, object $message): void
    {
        Gateway::sendToClient($clientId, encode($message));
    }

    public static function uidByClientId(string $clientId): int
    {
        return toInt(Gateway::getUidByClientId($clientId));
    }

    public static function toUid(int $uid, object $message): void
    {
        Gateway::sendToUid($uid, encode($message));
    }
}