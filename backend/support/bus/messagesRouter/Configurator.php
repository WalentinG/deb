<?php

declare(strict_types=1);

namespace support\bus\messagesRouter;

interface Configurator
{
    public function configure(Router $router): void;
}
