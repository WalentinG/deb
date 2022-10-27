<?php

declare(strict_types=1);

namespace Dependence;

use app\auth\DecodeUserToken;
use app\auth\IssuedAtStore;
use app\auth\UserAuthorizing;
use app\auth\ServerToken;
use DI\Container;
use Godruoyi\Snowflake\Snowflake;
use ReCaptcha\ReCaptcha;
use sockets\auth\events\LoggedIn;
use sockets\auth\events\LoggedOut;
use support\auth\PrivateKey;
use support\auth\PublicKey;
use support\bus\endpoint\Endpoint;
use support\bus\endpoint\EndpointRouter;
use support\bus\entrypoint\Decoders;
use support\bus\messagesRouter\Router;
use support\bus\mutex\RedisMutexService;
use support\bus\saga\SagaConfiguration;
use support\bus\saga\store\EloquentStore;
use support\bus\serialization\JsonEncoder;
use support\bus\serialization\PlainEncoder;
use support\bus\serviceHandler\RouterConfigurator;
use support\bus\transport\imMemory\InMemoryTransport;
use support\bus\transport\redis\RedisChannel;
use support\bus\transport\redis\RedisTransport;
use support\crypto\Aes128cbc;
use support\crypto\DecryptingButter;
use support\http\ClientIpAddress;
use support\socket\Sockets;
use support\socket\WorkermanSockets;
use support\telegram\TelegramBot;
use support\telegram\TelegramCredentials;
use Workerman\Http\Client;
use function support\bus\saga\searchSagaHandlers;
use function support\bus\serviceHandler\searchHandlers;

return [
    ServerToken::class => static fn () => new ServerToken(
        value: toStr(env('SERVER_AUTHORIZATION_TOKEN'))
    ),

    // client-ip
    ClientIpAddress::class => static fn () => new ClientIpAddress(
        trustedHeader: toStr(env('REAL_IP_HEADER')),
        trustedProxies: toArr(env('REAL_IP_FROM'))
    ),

    // decrypting-butter
    DecryptingButter::class => static fn () => new DecryptingButter(
        tiger: new Aes128cbc(passphrase: '7#0apwZ0zg*a932y', iv: '6v2cOih#uL2rmBj^'),
        tiger2: new Aes128cbc(passphrase: 'xW.uc8LUi.x7@k!p', iv: 'Nz_zu4*xT8-8Z4ve'),
    ),

    DecodeUserToken::class => static fn (IssuedAtStore $store) => new DecodeUserToken(
        store: $store,
        publicKey: PublicKey::ES256(toStr(env('JWT_ECDSA_PUBLIC')))
    ),

    // La Authorization
    UserAuthorizing::class => static fn (DecodeUserToken $decodeLaToken) => new UserAuthorizing(
        decodeUserToken: $decodeLaToken
    ),

    // tg bot
    TelegramBot::class => static fn () => new TelegramBot(
        http: new Client(),
        cred: new TelegramCredentials(toStr(env('TELEGRAM_BOT_TOKEN'))),
        channel: toStr(env('TELEGRAM_CHANNEL'))
    ),

    // recaptcha
    ReCaptcha::class => static fn () => new ReCaptcha(
        secret: toStr(env('RECAPTCHA_SECRET_KEY'))
    ),

    // Service bus
    Router::class => static fn (Container $container) => Router::configure(
        new RouterConfigurator(
            container: $container,
            classList: searchHandlers([__DIR__ . '/../app'])
        ),
        new SagaConfiguration(
            sagas: new EloquentStore(),
            mutex: new RedisMutexService(),
            classList: searchSagaHandlers([__DIR__ . '/../app'])
        )
    ),

    Decoders::class => static fn (JsonEncoder $json, PlainEncoder $plain) => new Decoders(
        decoders: [
            JsonEncoder::contentType() => $json,
            PlainEncoder::contentType() => $plain,
        ]
    ),

    EndpointRouter::class => static fn (InMemoryTransport $inMemory, JsonEncoder $json, PlainEncoder $plain) => EndpointRouter::default(
        defaultEndpoint: new Endpoint(
            name: 'app-inner',
            transport: $inMemory,
            encoder: $plain,
        )
    ),

    // Sockets
    Sockets::class => static fn () => new WorkermanSockets(),

    PrivateKey::class => static fn () => PrivateKey::ES256(toStr(env('JWT_ECDSA_PRIVATE'))),

    Snowflake::class => static fn () => (new Snowflake())->setStartTimeStamp(strtotime('2022-07-01') * 1000),
];
