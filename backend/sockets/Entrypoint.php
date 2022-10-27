<?php

declare(strict_types=1);

namespace sockets;

use app\user\UserId;
use sockets\auth\events\LoggedOut;
use sockets\auth\Login;
use sockets\auth\LoginHandler;
use sockets\auth\UserIsOffline;
use sockets\typing\SendEndTyping;
use sockets\typing\SendStartTyping;
use sockets\typing\TypingHandler;
use support\auth\JwtTokenException;
use support\bus\KernelContext;

final class Entrypoint
{
    public static function onConnect(string $clientId): void
    {
    }

    public static function onMessage(string $clientId, mixed $message): void
    {
        try {
            /** @var array<string, mixed> $data */
            $data = json_decode(toStr($message), true);
            if ('Login' !== $type = $data['type']) {
                self::assertAuthorized();
            }
            match ($type) {
                'Login' => containerGet(LoginHandler::class)->handle(
                    unmarshal(Login::class, snakeToCamel($data + ['clientId' => $clientId]))
                ),
                'SendStartTyping' => containerGet(TypingHandler::class)->sendStartTyping(
                    unmarshal(SendStartTyping::class, snakeToCamel($data + ['clientId' => $clientId]))
                ),
                'SendEndTyping' => containerGet(TypingHandler::class)->sendEndTyping(
                    unmarshal(SendEndTyping::class, snakeToCamel($data + ['clientId' => $clientId]))
                ),
                default => throw BusinessException::unknownMethod()
            };
        } catch (DisconnectException $e) {
            SocketGateway::toClient($clientId, ErrorResponse::exit($e->getCode(), $e->getMessage()));
            SocketGateway::closeClient($clientId);
        } catch (BusinessException $e) {
            SocketGateway::toClient($clientId, ErrorResponse::custom($e->getCode(), $e->getMessage(), $e->type));
        } catch (JwtTokenException $e) {
            SocketGateway::toClient($clientId, ErrorResponse::error($e->getCode(), $e->getMessage()));
        } catch (\Throwable) {
            SocketGateway::toClient($clientId, ErrorResponse::error(500, 'Error processing event'));
        }
    }

    public static function onClose(string $clientId): void
    {
        if (isset($_SESSION['userId'])) {
            containerGet(KernelContext::class)->delivery(new LoggedOut(UserId::from($id = toInt($_SESSION['userId']))));
            SocketGateway::toGroup($id, new UserIsOffline($id, toStr(time())));
        }
    }

    private static function assertAuthorized(): void
    {
        if (!isset($_SESSION['userId'])) {
            throw DisconnectException::invalidToken();
        }
    }
}
