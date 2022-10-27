<?php

declare(strict_types=1);

namespace sockets\auth;

use app\auth\DecodeUserToken;
use app\auth\UserToken;
use app\user\UserId;
use sockets\SocketGateway;
use sockets\auth\events\LoggedIn;
use sockets\DisconnectException;
use support\bus\KernelContext;

final class LoginHandler
{
    public function __construct(private readonly DecodeUserToken $decodeLaToken, private readonly KernelContext $bus)
    {
    }

    public function handle(Login $loginMessage): void
    {
        $this->validateDevice($loginMessage->deviceId);
        $token = $this->decodeToken($loginMessage->token);
        $this->logoutOtherDevices($id = $token->userId(), $loginMessage->clientId);
        $this->createSession($loginMessage->clientId, $id, $loginMessage->deviceId);
        $this->bindUid($loginMessage->clientId, $id);
        $this->fireEvent($id);
        $this->sendUserIsOnline($id);
        $this->successResponse($loginMessage->clientId);
    }

    private function validateDevice(string $deviceId): void
    {
        if (\strlen($deviceId) > 50) {
            throw DisconnectException::invalidDevice();
        }
    }

    private function decodeToken(string $token): UserToken
    {
        return $this->decodeLaToken->call($token);
    }

    private function logoutOtherDevices(UserId $userId, string $clientId): void
    {
        $clientList = SocketGateway::clientIdByUid($userId->value);
        foreach ($clientList as $cid) {
            if (0 !== strcmp($cid, $clientId)) {
                SocketGateway::closeClient($cid);
            }
        }
    }

    private function createSession(string $clientId, UserId $userId, string $deviceId): void
    {
        $session = new SocketSession($userId, $deviceId, time());
        SocketGateway::setSession($clientId, $session);
    }

    private function bindUid(string $clientId, UserId $userId): void
    {
        SocketGateway::bindUid($clientId, $userId->value);
    }

    private function fireEvent(UserId $userId): void
    {
        $this->bus->delivery(new LoggedIn($userId));
    }

    private function sendUserIsOnline(UserId $userId): void
    {
        SocketGateway::toGroup($userId->value, new UserIsOnline($userId->value));
    }

    private function successResponse(string $clientId): void
    {
        SocketGateway::toClient($clientId, new LoggedInSuccessfully());
    }
}
