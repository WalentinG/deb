<?php

declare(strict_types=1);

namespace sockets\typing;

use sockets\SocketGateway;

final class TypingHandler
{
    public function sendStartTyping(SendStartTyping $cmd): void
    {
        $id = toInt(SocketGateway::uidByClientId($cmd->clientId));
        SocketGateway::toUid($cmd->chatId->value, new UserStartedTyping($id, $id));
    }

    public function sendEndTyping(SendEndTyping $cmd): void
    {
        $id = toInt(SocketGateway::uidByClientId($cmd->clientId));
        SocketGateway::toUid($cmd->chatId->value, new UserEndedTyping($id, $id));
    }
}
