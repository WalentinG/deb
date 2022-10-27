<?php

declare(strict_types=1);

namespace tests\unit\support\bus\serviceHandler;

use DI\Container;
use PHPUnit\Framework\TestCase;
use support\bus\messagesRouter\Router;
use support\bus\serviceHandler\RouterConfigurator;

/**
 * @internal
 * @covers \RouterConfigurator
 */
final class RouterConfiguratorTest extends TestCase
{
    public function testConfigure(): void
    {
        $router = new Router();
        $configurator = new RouterConfigurator(new Container(), [EventsHandlerStub::class]);

        $configurator->configure($router);

        TestCase::assertCount(1, $router->match(new CommandStub()));
        TestCase::assertCount(3, $router->match(new EventStub()));
    }
}
