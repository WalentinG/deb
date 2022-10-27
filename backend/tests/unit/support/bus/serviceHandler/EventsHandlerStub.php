<?php

declare(strict_types=1);

namespace tests\unit\support\bus\serviceHandler;

use support\bus\serviceHandler\attribute\CommandHandler;
use support\bus\serviceHandler\attribute\EventHandler;

final class EventsHandlerStub
{
    #[CommandHandler]
    public function handleCommand(CommandStub $event): void
    {
    }

    #[EventHandler]
    public function handleEvent(EventStub $event): void
    {
    }

    #[EventHandler]
    public static function handleEventOneMoreTime(EventStub $event): void
    {
    }

    #[EventHandler]
    public static function handleTwoEvents(EventStub|SecondEventStub $event): void
    {
    }
}
